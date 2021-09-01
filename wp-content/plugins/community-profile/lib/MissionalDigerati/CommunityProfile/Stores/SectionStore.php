<?php
namespace MissionalDigerati\CommunityProfile\Stores;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

/**
 * A class for handling the storage of the Section in the database.
 *
 * NOTE: we use tag as the look up field.  It is a unique field.
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
     * Create a new section
     *
     * @param  string $title    The title of the section
     * @param  string $tag      The tag of the section
     * @param  string $url      The url of the section
     *
     * @return integer|false    Returns the id if inserted otherwise it returns false
     */
    public function create($title, $tag, $url)
    {
        $exists = $this->findByTag($tag);
        if ($exists) {
            return $exists->id;
        }

        $created = $this->createSection($title, $tag, $url);
        return ($created) ? $this->db->insert_id : false;
    }

    /**
     * convience method to create or update the given section.
     *
     * @param   string          $title          The title of the section
     * @param   string          $tag            The tag to find the current section.
     * @param   string          $url            The url of the section
     * @return  integer|false                   It returns the id or false if it failed to create/update
     */
    public function createOrUpdate($title, $tag, $url)
    {
        $exists = $this->findByTag($tag);
        if (!$exists) {
            $created = $this->createSection($title, $tag, $url);
            return ($created) ? $this->db->insert_id : false;
        }

        if (($exists->title !== $title) || ($exists->url !== $url)) {
            // Only update if there was a change.
            $this->updateSection($title, $tag, $url);
        }
        return $exists->id;
    }

    /**
     * Find the section based on it's tag.
     *
     * @param  string $tag  The tag of the section
     * @return object       The section details
     */
    public function findByTag($tag)
    {
        $tableName = $this->prefix . self::$tableName;
        $prepare = $this->db->prepare(
            "SELECT * FROM {$tableName} WHERE tag = '%s'",
            strtolower(trim($tag))
        );
        $section = $this->db->get_row($prepare);
        if ($section) {
            $section->id = intval($section->id);
        }
        return $section;
    }

    /**
     * Update the existing section.
     *
     * NOTE: Do not update the tag field!
     *
     * @param   string          $title      The title of the section
     * @param   string          $tag        The tag to find the current section.
     * @param   string          $url            The url of the section
     *
     * @return  integer|false               It returns the id or false if it doesn't exist
     */
    public function update($title, $tag, $url)
    {
        $exists = $this->findByTag($tag);
        if (!$exists) {
            return false;
        }

        if (($exists->title !== $title) || ($exists->url !== $url)) {
            // Only update if there was a change.
            $this->updateSection($title, $tag, $url);
        }
        return $exists->id;
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
            url varchar(255) DEFAULT '' NOT NULL,
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            UNIQUE KEY tag (tag),
            PRIMARY KEY  (id)
        ) {$charsetCollate};";
        dbDelta($sql);
    }

    /**
     * Create the section
     *
     * @param  string $title    The title of the section
     * @param  string $tag      The tag of the section
     * @param  string $url      The url of the section
     *
     * @return boolean          Was it successfully created?
     * @access protected
     */
    protected function createSection($title, $tag, $url)
    {
        $tableName = $this->prefix . self::$tableName;
        $prepare = $this->db->prepare(
            "INSERT INTO {$tableName}
                (title, tag, url, created_at)
                VALUES(%s, %s, %s, NOW())
            ",
            $title,
            strtolower(trim($tag)),
            $url
        );
        return $this->db->query($prepare);
    }

    /**
     * Update the existing section.
     *
     * NOTE: Do not update the tag field!
     *
     * @param   string      $title  The title of the section
     * @param   string      $tag    The tag to find the current section.
     * @param   string      $url    The url of the section
     *
     * @return  integer             The number of rows affected by the update
     * @access  protected
     */
    protected function updateSection($title, $tag, $url)
    {
        $tableName = $this->prefix . self::$tableName;
        $prepare = $this->db->prepare(
            "UPDATE {$tableName} SET title = %s, url = %s WHERE tag = %s",
            $title,
            $url,
            strtolower(trim($tag))
        );
        return $this->db->query($prepare);
    }
}
