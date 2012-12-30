<?php
namespace LAN;

class Util
{
    protected static $db = false;

    public static function getMAC($ip)
    {
        //Regex to get MAC address
        $regex = '/([0-9a-fA-F]{2}[:-]){5}([0-9a-fA-F]{2})/';

        //run the external command, break output into lines
        $arp   = `arp -a $ip`;

        $matches = array();

        preg_match($regex, $arp, $matches);

        //Return the MAC address if found...
        if (isset($matches[0])) {
            return $matches[0];
        }

        if ($ip == Config::get('SERVER_ADDR')) {
            $ifconifg = shell_exec('ifconfig | grep eth' . Config::get('SERVER_ETH'));

            preg_match($regex, $ifconifg, $matches);

            //Return the MAC address if found...
            if (isset($matches[0])) {
                return $matches[0];
            }
        }

        throw new Exception('Unable to find MAC address', 500);
    }

    public static function setDB($host, $user, $password, $database)
    {
        self::$db = new \mysqli($host, $user, $password, $database);

        if (mysqli_connect_error()) {
            throw new \Exception('Database connection error (' . mysqli_connect_errno() . ') '
                . mysqli_connect_error());
        }

        self::$db->set_charset('utf8');

        //Set DB connection
        \DB\Connection::setDB(self::$db);
    }

    /**
     * Connect to the database and return it
     *
     * @throws \Exception
     * @return mysqli
     */
    public static function getDB()
    {
        //If it isn't set yet, try to set it.
        if (!self::$db) {
            self::setDB(Config::get('DB_HOST'), Config::get('DB_USER'), Config::get('DB_PASSWORD'), Config::get('DB_NAME'));
        }

        return self::$db;
    }

    public static function epochToDateTime($time = false)
    {
        if (!$time) {
            $time = time();
        }

        return date("Y-m-d H:i:s", $time);
    }

    public static function getURL()
    {
        return "http://" . Config::get('SERVER_ADDR') . Config::get('WWW_PATH');
    }

    public static function makeClickableLinks($text) {
        return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#+%-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $text);
    }
}