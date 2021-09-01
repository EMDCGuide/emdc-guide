<?php
namespace MissionalDigerati\CommunityProfile\Stores;

use MissionalDigerati\CommunityProfile\Stores\SectionStore;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

/**
 * A class for handling the storage of the Question in the database.
 *
 * NOTE: we create and use a md5 hash of the question as the look up field.
 */
class QuestionStore
{
    /**
     * The name of the Section table.
     *
     * @var string
     */
    public static $tableName = 'copr_questions';

    /**
     * The WordPress database
     *
     * @var object
     */
    protected $db = null;

    /**
     * The database table prefix
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Build the class
     *
     * @param   object    $db        The WordPress database object
     * @param   string    $prefix    The prefix for the database table (default: '')
     */
    public function __construct($db, $prefix = '')
    {
        if (!$db) {
            throw new \InvalidArgumentException('the WordPress database object must be set.');
        }
        $this->db = $db;
        $this->prefix = $prefix;
    }

    /**
     * Create a question
     *
     * @param  integer  $sectionId      The id of the existing section
     * @param  string   $choices        The question choices
     * @param  integer  $number         The question number
     * @param  string   $question       The question
     * @param  string   $questionType   The type of question
     * @return integer|false            It returns the id or false if it failed to create/update
     */
    public function create($sectionId, $choices, $number, $question, $questionType)
    {
        $exists = $this->findByQuestion($sectionId, $question);
        if ($exists) {
            return $exists->id;
        }

        $created = $this->createQuestion(
            $sectionId,
            $choices,
            $number,
            $question,
            $questionType
        );
        return ($created) ? $this->db->insert_id : false;
    }

    /**
     * Create or Update a question
     *
     * @param  integer  $sectionId      The id of the existing section
     * @param  string   $choices        The question choices
     * @param  integer  $number         The question number
     * @param  string   $question       The question
     * @param  string   $questionType   The type of question
     * @return integer|false            Returns the id if inserted otherwise it returns false
     */
    public function createOrUpdate($sectionId, $choices, $number, $question, $questionType)
    {
        $exists = $this->findByQuestion($sectionId, $question);
        if (!$exists) {
            $created = $this->createQuestion(
                $sectionId,
                $choices,
                $number,
                $question,
                $questionType
            );
            return ($created) ? $this->db->insert_id : false;
        }

        if (
            ($exists->question_number !== $number) ||
            ($exists->choice !== $choices) ||
            ($exists->question_type !== $questionType)
        ) {
            // Only update if there was a change.
            $this->updateQuestion(
                $sectionId,
                $choices,
                $number,
                $question,
                $questionType
            );
        }
        return $exists->id;
    }

    /**
     * Find a question by it's question
     *
     * @param  integer  $sectionId  The id of the existing section
     * @param  string   $question   The question to find
     * @return object               The question details
     */
    public function findByQuestion($sectionId, $question)
    {
        $tableName = $this->prefix . self::$tableName;
        $hash = md5(trim($question));
        $prepare = $this->db->prepare(
            "SELECT * FROM {$tableName}
            WHERE unique_hash = '%s' AND copr_section_id = %d",
            $hash,
            $sectionId
        );
        $result = $this->db->get_row($prepare);
        if ($result) {
            $result->id = intval($result->id);
            $result->question_number = intval($result->question_number);
        }
        return $result;
    }

    /**
     * Set up the questions table.
     *
     * @param   string  $charsetCollate The character set and collation
     *
     * @return void
     */
    public function setUp($charsetCollate)
    {
        $tableName = $this->prefix . self::$tableName;
        $sectionTableName = $this->prefix . SectionStore::$tableName;
        $sql = "CREATE TABLE {$tableName} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            copr_section_id mediumint(9) NOT NULL,
            unique_hash varchar(255) DEFAULT '' NOT NULL,
            question_number mediumint(9) NOT NULL,
            question_type varchar(45) DEFAULT 'text' NOT NULL,
            question_choices varchar(255) DEFAULT '' NOT NULL,
            question longtext DEFAULT '' NOT NULL,
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY unique_hash (unique_hash),
            FOREIGN KEY (copr_section_id) REFERENCES {$sectionTableName} (id)
        ) {$charsetCollate};";
        dbDelta($sql);
    }

    /**
     * Update the given question.  Only the number will be updated.  We use question to create
     * the unique hash.
     *
     * @param  integer  $sectionId      The id of the existing section
     * @param  string   $choices        The question choices
     * @param  integer  $number         The question number
     * @param  string   $question       The question
     * @param  string   $questionType   The type of question
     * @return  integer|false           It returns the id or false if it doesn't exist
     */
    public function update($sectionId, $choices, $number, $question, $questionType)
    {
        $exists = $this->findByQuestion($sectionId, $question);
        if (!$exists) {
            return false;
        }

        if (
            ($exists->question_number !== $number) ||
            ($exists->choice !== $choices) ||
            ($exists->question_type !== $questionType)
        ) {
            // Only update if there was a change.
            $this->updateQuestion(
                $sectionId,
                $choices,
                $number,
                $question,
                $questionType
            );
        }
        return $exists->id;
    }

    /**
     * Create a question
     *
     * @param  integer  $sectionId      The id of the existing section
     * @param  string   $choices        The question choices
     * @param  integer  $number         The question number
     * @param  string   $question       The question
     * @param  string   $questionType   The type of question
     * @return boolean                  Was it successfully created?
     *
     * @access protected
     */
    protected function createQuestion($sectionId, $choices, $number, $question, $questionType)
    {
        $tableName = $this->prefix . self::$tableName;
        $question = trim($question);
        $hash = md5($question);
        $prepare = $this->db->prepare(
            "INSERT INTO {$tableName}
                (copr_section_id, unique_hash, question_number, question_type, question_choices, question, created_at)
                VALUES(%d, %s, %d, %s, %s, %s, NOW())
            ",
            $sectionId,
            $hash,
            $number,
            $questionType,
            $choices,
            $question
        );
        return $this->db->query($prepare);
    }

    /**
     * Update the given question.  Only the number will be updated.  We use question to create
     * the unique hash.
     *
     * @param  integer  $sectionId  The id of the existing section
     * @param  string   $choices        The question choices
     * @param  integer  $number         The question number
     * @param  string   $question       The question
     * @param  string   $questionType   The type of question
     * @return  integer                 The number of rows affected by the update
     *
     * @access protected
     */
    protected function updateQuestion($sectionId, $choices, $number, $question, $questionType)
    {
        $tableName = $this->prefix . self::$tableName;
        $question = trim($question);
        $hash = md5($question);
        $prepare = $this->db->prepare(
            "UPDATE {$tableName} SET question_number = %s, question_type = %s, question_choices = %s
            WHERE unique_hash = %s AND copr_section_id = %d",
            $number,
            $questionType,
            $choices,
            $hash,
            $sectionId
        );
        return $this->db->query($prepare);
    }
}
