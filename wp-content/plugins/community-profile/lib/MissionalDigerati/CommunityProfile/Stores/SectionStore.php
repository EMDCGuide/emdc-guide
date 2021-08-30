<?php
namespace MissionalDigerati\CommunityProfile\Stores;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

/**
 * A class for handling the storage of the Section in the database.
 */
class SectionStore
{
    /**
     * The name of the Section table.
     *
     * @var string
     */
    public static $tableName = 'copr_sections';

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
     * Create a new section
     *
     * @param  string $title    The title of the section
     * @param  string $tag      The tag of the section
     *
     * @return boolean          Did it successfully create?
     */
    public function create($title, $tag)
    {
        $tableName = $this->prefix . self::$tableName;
        $prepare = $this->db->prepare("INSERT INTO {$tableName}
                (title, tag, created_at)
                VALUES(%s, %s, NOW())
            ",
            $title,
            strtolower($tag)
        );
        return $this->db->query($prepare);
    }

    /**
     * Set up the sections table.
     *
     * @param   string  $charsetCollate The character set and collation
     *
     * @return void
     */
    public function setUp($charsetCollate)
    {
        $tableName = $this->prefix . self::$tableName;
        $sql = "CREATE TABLE {$tableName} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) DEFAULT '' NOT NULL,
            tag varchar(45) DEFAULT '' NOT NULL,
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) {$charsetCollate};";
        dbDelta($sql);
    }
}
