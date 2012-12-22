<?php
namespace LAN\User;

class RecordList extends \DB\RecordList
{
    static function getDefaultOptions()
    {
        $options = array();
        $options['itemClass'] = '\LAN\User\Record';
        $options['listClass'] = __CLASS__;

        return $options;
    }

    public static function getAllOnline($options = array())
    {
        //Build the list
        $options = $options + self::getDefaultOptions();
        $options['sql'] = "SELECT id
                           FROM users
                           WHERE status = 'ONLINE'";

        return self::getBySql($options);
    }
}