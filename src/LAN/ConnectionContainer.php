<?php
namespace LAN;

class ConnectionContainer
{
    protected $connection = false; // \Wrench\Connection object
    protected $mac        = false; // Cash the MAC address as arp lookup may be slow.

    function __construct(\Ratchet\ConnectionInterface $connection)
    {
        $this->setConnection($connection);
        $this->mac = \LAN\Util::getMac($connection->remoteAddress);
    }

    function getConnection()
    {
        return $this->connection;
    }

    function getUser()
    {
        return User\Record::getUser($this->connection, $this->mac);
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