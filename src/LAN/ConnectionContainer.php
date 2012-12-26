<?php
namespace LAN;

class ConnectionContainer
{
    protected $connection = false; // \Wrench\Connection object

    function __construct(\Ratchet\ConnectionInterface $connection)
    {
        $this->setConnection($connection);
    }

    function getConnection()
    {
        return $this->connection;
    }

    function getUser()
    {
        return User\Record::getUser($this->connection);
    }

    function setConnection(\Ratchet\ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }


    function send($action, $data)
    {
        Application::sendMessageToClient($this->getConnection(), $action, $data);
    }
}