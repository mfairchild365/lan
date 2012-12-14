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
}