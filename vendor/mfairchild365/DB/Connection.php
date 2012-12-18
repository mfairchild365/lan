<?php
/**
 * Simple Connection
 *
 * PHP version 5
 *
 * @category  Publishing
 * @package   DB
 * @author    Michael Fairchild <mfairchild365@gmail.com>
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 */
namespace DB;

abstract class Connection
{
    protected static $db;  //The database

    private function __construct() {
        //Private so that it can't be instantiated (singleton)
    }

    /**
     * Sets the DB with a database object
     * (MUST BE MYSQLI FOR NOW)
     *
     * (Your application must establish a connection on its own... then pass here)
     *
     * @param $db MYSQLI database object
     */
    public static function setDB($db)
    {
        self::$db = $db;
    }

    /**
     * Get the database connection
     *
     * @internal param $db
     * @return Mysqli DB connection
     */
    public static function getDB()
    {
        return self::$db;
    }
}