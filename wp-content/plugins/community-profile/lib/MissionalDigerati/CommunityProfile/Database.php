<?php
namespace MissionalDigerati\CommunityProfile;

/**
 * A class for handling database functionality such as set up and upgrading.
 */
class Database
{
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
     * An array of database stores
     *
     * @var array
     */
    protected $stores = [];

    /**
     * Set up the class
     *
     * @param string $charsetCollate The character set and collation
     */
    public function __construct($charsetCollate)
    {
        $this->charsetCollate = $charsetCollate;
    }

    /**
     * Add a database store to our stores library
     *
     * @param object $store The store to add
     * @return void
     */
    public function addStore($store)
    {
        $this->stores[] = $store;
    }

    /**
     * Install our databases
     *
     * @return void
     */
    public function install()
    {
        foreach ($this->stores as $store) {
            if (method_exists($store, 'setUp')) {
                $store->setUp($this->charsetCollate);
            }
        }
        add_option('copr_db_version', $this->version);
    }
}
