<?php
namespace LAN;

class ConnectionContainer
{
    protected $user        = false; // \LAN\User\Record   object
    protected $connections = false; // \Wrench\Connection object

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
        return $this->user;
    }

    function setConnection(\Ratchet\ConnectionInterface $connection)
    {
        $this->connection = $connection;

        //Look for user
        $this->user = User\Record::getUser($connection);
    }
}