<?php
namespace LAN\Message;

class Record extends \DB\Record implements \LAN\Renderable
{
    protected $id;           //INT(32)
    protected $users_id;     //VARCHAR(45)
    protected $message;      //TEXT
    protected $date_created; //DATETIME
    protected $date_edited;  //DATETIME

    public static function getByID($id)
    {
        return self::getByAnyField(__CLASS__, 'id', (int)$id);
    }

    function keys()
    {
        return array('id');
    }

    public static function getTable()
    {
        return 'messages';
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

    public static function createNewMessage($userId, $message)
    {
        $record = new self();

        $record->setUsersId($userId);
        $record->setMessage($message);

        $record->save();

        return $record;
    }

    function getID()
    {
        return $this->id;
    }

    function getUsersId()
    {
        return $this->users_id;
    }

    function getUser()
    {
        return \LAN\User\Record::getByID($this->users_id);
    }

    function getMessage()
    {
        return $this->message;
    }

    function getDateCreated()
    {
        return $this->date_created;
    }

    function getDateUpdated()
    {
        return $this->date_edited;
    }

    function setUsersId($id)
    {
        $this->users_id = $id;
    }

    function setMessage($message)
    {
        $this->message = $message;
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

    function render()
    {
        //Convert this object to an array
        $data = $this->toArray();

        return $data;
    }
}