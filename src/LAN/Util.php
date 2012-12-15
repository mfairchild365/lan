<?php
namespace LAN;

class Util
{
    public static function getMAC($ip)
    {
        //run the external command, break output into lines
        $arp   = `arp -a $ip`;

        $matches = array();

        preg_match('/([0-9a-fA-F]{2}[:-]){5}([0-9a-fA-F]{2})/', $arp, $matches);

        //Return the MAC address if found...
        if (isset($matches[0])) {
            return $matches[0];
        }

        return false;
    }

    /**
     * Connect to the database and return it
     *
     * @throws \Exception
     * @return mysqli
     */
    public static function getDB()
    {
        //Reduce connections by saving the current connection and reusing it.
        static $db = false;

        if (!$db) {
            $db = new \mysqli(Config::get('DB_HOST'), Config::get('DB_USER'), Config::get('DB_PASSWORD'), Config::get('DB_NAME'));
            if (mysqli_connect_error()) {
                throw new \Exception('Database connection error (' . mysqli_connect_errno() . ') '
                    . mysqli_connect_error());
            }
            $db->set_charset('utf8');
        }

        return $db;
    }
}