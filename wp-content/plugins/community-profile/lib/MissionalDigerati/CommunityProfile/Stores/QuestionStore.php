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
     * @param object $db The WordPress database object
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
     * @param  integer  $sectionId  The id of the existing section
     * @param  integer  $number     The question number
     * @param  string   $question   The question
     * @return integer|false        Returns the id if inserted otherwise it returns false
     */
    public function create($sectionId, $number, $question)
    {
        $exists = $this->findByQuestion($question);
        if ($exists) {
            return $exists->id;
        }

        $created = $this->createQuestion($sectionId, $number, $question);
        return ($created) ? $this->db->insert_id : false;
    }

    /**
     * Find a question by it's question
     *
     * @param  string $question The question to find
     * @return object           The question details
     */
    public function findByQuestion($question)
    {
        $tableName = $this->prefix . self::$tableName;
        $hash = md5($question);
        $prepare = $this->db->prepare("SELECT * FROM {$tableName} WHERE unique_hash = '%s'",
            $hash
        );
        $question = $this->db->get_row($prepare);
        if ($question) {
            $question->id = intval($question->id);
        }
        return $question;
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
            question longtext DEFAULT '' NOT NULL,
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY (unique_hash),
            FOREIGN KEY  (copr_section_id) REFERENCES {$sectionTableName}(id)
        ) {$charsetCollate};";
        dbDelta($sql);
    }

    /**
     * Create a question
     *
     * @param  integer  $sectionId  The id of the existing section
     * @param  integer  $number     The question number
     * @param  string   $question   The question
     * @return boolean              Was it successfully created?
     *
     * @access protected
     */
    protected function createQuestion($sectionId, $number, $question)
    {
        $tableName = $this->prefix . self::$tableName;
        $hash = md5($question);
        $prepare = $this->db->prepare("INSERT INTO {$tableName}
                (copr_section_id, unique_hash, question_number, question, created_at)
                VALUES(%d, %s, %d, %s, NOW())
            ",
            $sectionId,
            $hash,
            $number,
            $question
        );
        return $this->db->query($prepare);
    }
}
