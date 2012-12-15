<?php
namespace LAN;

class Config
{
    protected static $data = array(
        'SERVER_ADDR'      => false,  //SERVER ADDRESS.  (IE: 192.168.1.5) (Leave false to auto-detect)
        'SERVER_PORT'      => '8000', //SERVER Port
        'SERVER_ETH'       => 0,      //SERVER ETHERNET PORT (used when auto detecting IP address)
        'APPLICATION_NAME' => 'lan',  //APPLICATION NAME (ie 192.168.1.5:8000/APPLICATION_NAME)
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

            return str_replace("\n","", exec("ifconfig eth" . self::$data['SERVER_ETH'] . " | grep 'inet addr' | awk -F':' {'print $2'} | awk -F' ' {'print $1'}"));
        }

        return self::$data[$key];
    }

    public static function set($key, $value)
    {
        return self::$data[$key] = $value;
    }
}