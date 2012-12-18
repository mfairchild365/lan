<?php
namespace LAN\User;

class Record extends \DB\Record
{
    protected $id;           //INT(32)
    protected $mac;          //VARCHAR(32)
    protected $ip;           //VARCHAR(16)
    protected $name;         //VARCHAR(256)
    protected $date_created; //DATETIME
    protected $date_updated; //DATETIME
    protected $status;       //ENUM('ONLINE', 'OFFLINE')
    protected $host_name;    //VARCHAR(256)

    public static function getByID($id)
    {
        return self::getByAnyField('\UNL\VisitorChat\User\Record', 'id', (int)$id);
    }

    public static function getByMAC($mac)
    {
        return self::getByAnyField('\UNL\VisitorChat\User\Record', 'mac', $mac);
    }

    function keys()
    {
        return array('id');
    }

    public static function getTable()
    {
        return 'users';
    }

    public static function getUser(\Ratchet\ConnectionInterface $connection)
    {
        return self::createNewUser($connection);
    }

    public static function createNewUser(\Ratchet\ConnectionInterface $connection)
    {
        $record = new self();

        try {
            $record->setMAC(\LAN\Util::getMac($connection->remoteAddress));
            $record->setIP($connection->remoteAddress);
            $record->setHostName(gethostbyaddr($connection->remoteAddress));
            //TODO: $record->setDateCreated();
        } catch (\Exception $e) {

        }

        return $record;
    }

    function getID()
    {
        return $this->id;
    }

    function getMAC()
    {
        return $this->mac;
    }

    function getIP()
    {
        return $this->ip;
    }

    function getName()
    {
        return $this->name;
    }

    function getDateCreated()
    {
        return $this->date_created;
    }

    function getDateUpdated()
    {
        return $this->date_updated;
    }

    function getStatus()
    {
        return $this->status;
    }

    function getHostName()
    {
        return $this->host_name;
    }

    function setMAC($mac)
    {
        $this->mac = $mac;
    }

    function setIP($ip)
    {
        $this->ip = $ip;
    }

    function setName($name)
    {
        $this->name = $name;
    }

    function setDateCreated($date)
    {
        $this->date_created = $date;
    }

    function setDateUpdated($date)
    {
        $this->date_updated = $date;
    }

    function setStatus($status)
    {
        $this->status = $status;
    }

    function setHostName($name)
    {
        $this->host_name = $name;
    }
}