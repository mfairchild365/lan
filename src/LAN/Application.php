<?php
namespace LAN;

class Application extends \Wrench\Application\Application
{
    protected $connections = array();


    public function onConnect(\Wrench\Connection $connection)
    {
        $connection = new ConnectionContainer($connection);

        //Save in array
        $this->connections[$connection->getConnection()->getId()] = $connection;

        echo "IP  : " . $connection->getUser()->getIP() . PHP_EOL;
        echo "MAC : " . $connection->getUser()->getMAC() . PHP_EOL;

    }

    public function onDisconnect(\Wrench\Connection $connection)
    {
        echo "disconnected" . PHP_EOL;
        unset($this->connections[$connection->getId()]);
    }

    public function onUpdate()
    {
        //echo "Update" . PHP_EOL;
    }

    public function onData($data, $connection)
    {
        echo "Data" . PHP_EOL;
        print_r($data);
    }
}