<?php
namespace LAN;

class Config
{
    protected static $data = array(
        //SERVER RELATED SETTINGS
        'SERVER_ADDR'      => false,  //SERVER ADDRESS.  (IE: 192.168.1.5) (Leave false to auto-detect)
        'SERVER_PORT'      => '8000', //SERVER Port
        'SERVER_ETH'       => 'eth0', //SERVER ETHERNET PORT (used when auto detecting IP address) (IE: 'eth0' or 'en1')
        'APPLICATION_NAME' => 'lan',  //APPLICATION NAME (ie 192.168.1.5:8000/APPLICATION_NAME)
        'WWW_PATH'         => '/',    //Path to the www directory from the server's IP address. (IE: '/lan/www/') (TRAILING SLASH IS IMPORTANT)
        'CACHE_DIR'        => false,  //The Cache Dir

        //DB RELATED SETTINGS
        'DB_HOST'          => false,  //DATABASE HOST
        'DB_USER'          => false,  //DATABASE USER
        'DB_PASSWORD'      => false,  //DATABASE PASSWORD
        'DB_NAME'          => false,  //DATABASE NAME

        //STEAM SETTINGS
        'STEAM_API_KEY'            => false,  //Your steam cache key
        'STEAM_CACHE_TTL_PROFILES' => 15,     //Time to live in seconds for cache files
    );

    private function __construct()
    {
        //Do nothing
    }

    public static function get($key)
    {
        if (!isset(self::$data[$key])) {
            return false;
        }

        //Special default case: SERVER_ADDR
        if ($key == 'SERVER_ADDR' && self::$data[$key] == false) {
            if (isset($_SERVER['SERVER_ADDR'])) {
                return $_SERVER['SERVER_ADDR'];
            }

            return str_replace("\n","", exec("ifconfig " . self::$data['SERVER_ETH'] . " | grep 'inet addr' | awk -F':' {'print $2'} | awk -F' ' {'print $1'}"));
        }

        //Special default case: CACHE_DIR
        if ($key == 'CACHE_DIR' && self::$data[$key] == false) {
            return  dirname(dirname(dirname(__FILE__))) . "/tmp/";
        }

        return self::$data[$key];
    }

    public static function set($key, $value)
    {
        return self::$data[$key] = $value;
    }
}