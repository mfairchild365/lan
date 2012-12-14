<?php
namespace LAN;

class Application extends \Wrench\Application\Application
{
    protected $connections = array();


    public function onConnect(\Wrench\Connection $connection)
    {
        $this->connections[$connection->getId()] = $connection;

        echo "ID: " . $connection->getId() . PHP_EOL;
        echo "IP: " . $connection->getIp() . PHP_EOL;

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