<?php
namespace LAN\Message;

class RecordList extends \DB\RecordList
{
    static function getDefaultOptions()
    {
        $options = array();
        $options['itemClass'] = '\LAN\Message\Record';
        $options['listClass'] = __CLASS__;

        return $options;
    }

    public static function getAllMessages($options = array())
    {
        //Build the list
        $options = $options + self::getDefaultOptions();
        $options['sql'] = "SELECT id
                           FROM messages";

        return self::getBySql($options);
    }
}