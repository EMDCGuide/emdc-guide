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
     * @return integer|false    Returns the id if inserted otherwise it returns false
     */
    public function create($title, $tag)
    {
        $exists = $this->findByTag($tag);
        if ($exists) {
            return $exists->id;
        }
        $tableName = $this->prefix . self::$tableName;
        $prepare = $this->db->prepare("INSERT INTO {$tableName}
                (title, tag, created_at)
                VALUES(%s, %s, NOW())
            ",
            $title,
            strtolower($tag)
        );
        $this->db->query($prepare);
        return $this->db->insert_id;
    }

    /**
     * Find the section based on it's tag.
     *
     * @param  string $tag  The tag of the section
     * @return object       The tag details
     */
    public function findByTag($tag)
    {
        $tableName = $this->prefix . self::$tableName;
        $prepare = $this->db->prepare("SELECT * FROM {$tableName} WHERE tag = '%s'",
            strtolower($tag)
        );
        return $this->db->get_row($prepare);
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
            UNIQUE KEY (tag),
            PRIMARY KEY  (id)
        ) {$charsetCollate};";
        dbDelta($sql);
    }
}
