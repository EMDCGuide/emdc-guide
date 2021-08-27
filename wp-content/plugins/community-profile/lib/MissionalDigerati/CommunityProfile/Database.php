<?php
namespace MissionalDigerati\CommunityProfile;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

/**
 * A class for handling database functionality such as set up and upgrading.
 */
class Database
{
    /**
     * The table name for the answers table.
     *
     * @var string
     */
    public $answersTableName = 'copr_answers';

    /**
     * The table name for the questions table.
     *
     * @var string
     */
    public $questionsTableName = 'copr_questions';

    /**
     * The table name for the sections table.
     *
     * @var string
     */
    public $sectionsTableName = 'copr_sections';

    /**
     * The current version of the database
     *
     * @var string
     */
    public $version = '1.0';

    /**
     * The character set and collation
     *
     * @var string
     */
    protected $charsetCollate = '';

    /**
     * The database prefix
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Set up the class
     *
     * @param string $charsetCollate The character set and collation
     * @param string $prefix         The database prefix
     */
    public function __construct($charsetCollate, $prefix = '')
    {
        $this->charsetCollate = $charsetCollate;
        $this->prefix = $prefix;
        $this->answersTableName = $prefix . $this->answersTableName;
        $this->questionsTableName = $prefix . $this->questionsTableName;
        $this->sectionsTableName = $prefix . $this->sectionsTableName;
    }

    /**
     * Install our databases
     *
     * @return void
     */
    public function install()
    {
        $this->createSectionsTable();
        $this->createQuestionsTable();
        $this->createAnswersTable();
        add_option('copr_db_version', $this->version);
    }

    /**
     * Create the answers table.
     *
     * @return void
     */
    protected function createAnswersTable()
    {
        $sql = "CREATE TABLE {$this->answersTableName} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            copr_question_id mediumint(9) NOT NULL,
            user_id mediumint(9) NOT NULL,
            group_id mediumint(9) NOT NULL,
            answer longtext DEFAULT '' NOT NULL,
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY  (copr_question_id) REFERENCES {$this->questionsTableName}(id)
        ) {$this->charsetCollate};";
        dbDelta($sql);
    }

    /**
     * Create the questions table.
     *
     * @return void
     */
    protected function createQuestionsTable()
    {
        $sql = "CREATE TABLE {$this->questionsTableName} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            copr_section_id mediumint(9) NOT NULL,
            unique_hash varchar(255) DEFAULT '' NOT NULL,
            question longtext DEFAULT '' NOT NULL,
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY (unique_hash),
            FOREIGN KEY  (copr_section_id) REFERENCES {$this->sectionsTableName}(id)
        ) {$this->charsetCollate};";
        dbDelta($sql);
    }

    /**
     * Create the sections table.
     *
     * @return void
     */
    protected function createSectionsTable()
    {
        $sql = "CREATE TABLE {$this->sectionsTableName} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) DEFAULT '' NOT NULL,
            tag varchar(45) DEFAULT '' NOT NULL,
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) {$this->charsetCollate};";
        dbDelta($sql);
    }
}
