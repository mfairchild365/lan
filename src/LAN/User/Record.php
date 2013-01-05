<?php
namespace LAN\User;

class Record extends \DB\Record implements \LAN\Renderable
{
    protected $id;           //INT(32)
    protected $mac;          //VARCHAR(32)
    protected $ip;           //VARCHAR(16)
    protected $name;         //VARCHAR(256)
    protected $date_created; //DATETIME
    protected $date_edited; //DATETIME
    protected $status;       //ENUM('ONLINE', 'OFFLINE')
    protected $host_name;    //VARCHAR(256)
    protected $steam_id_64;  //VARCHAR(256)

    public static function getByID($id)
    {
        return self::getByAnyField(__CLASS__, 'id', (int)$id);
    }

    public static function getByMAC($mac)
    {
        return self::getByAnyField(__CLASS__, 'mac', $mac);
    }

    function keys()
    {
        return array('id');
    }

    public static function getTable()
    {
        return 'users';
    }

    function insert()
    {
        $this->date_created = \LAN\Util::epochToDateTime();
        $this->date_edited  = \LAN\Util::epochToDateTime();

        return parent::insert();
    }

    function update()
    {
        $this->date_edited = \LAN\Util::epochToDateTime();

        return parent::update();
    }

    public static function getUser(\Ratchet\ConnectionInterface $connection, $mac)
    {
        if ($record = self::getByMAC($mac)) {
            return $record;
        }

        return self::createNewUser($connection, $mac);
    }

    public static function createNewUser(\Ratchet\ConnectionInterface $connection, $mac)
    {
        $record = new self();

        $record->setMAC($mac);
        $record->setIP($connection->remoteAddress);
        $record->setHostName(gethostbyaddr($connection->remoteAddress));
        $record->setName("UNKNOWN");

        $record->save();

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

    function getSteamID64()
    {
        return $this->steam_id_64;
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

    function setDateEdited($date)
    {
        $this->date_edited = $date;
    }

    function setStatus($status)
    {
        $this->status = $status;
    }

    function setHostName($name)
    {
        $this->host_name = $name;
    }

    function setSteamID64($id)
    {
        $this->steam_id_64 = $id;
    }

    function render()
    {
        //Convert this object to an array
        $data = $this->toArray();

        //Don't send the mac address
        unset($data['mac']);

        return $data;
    }
}