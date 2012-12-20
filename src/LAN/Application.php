<?php
namespace LAN;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Application implements MessageComponentInterface {
    protected $connections = array();

    public function __construct()
    {
        Util::setDB(Config::get('DB_HOST'), Config::get('DB_USER'), Config::get('DB_PASSWORD'), Config::get('DB_NAME'));
    }

    public function onOpen(ConnectionInterface $connection) {
        $connection = new ConnectionContainer($connection);

        //Save in array
        $this->connections[$connection->getConnection()->resourceId] = $connection;

        //Display connection on server.
        echo "--------NEW CONNECTION--------" . PHP_EOL;
        echo "ID  : " . $connection->getConnection()->resourceId . PHP_EOL;
        echo "IP  : " . $connection->getUser()->getIP() . PHP_EOL;
        echo "MAC : " . $connection->getUser()->getMAC() . PHP_EOL;

        // Store the new connection to send messages to later
        foreach ($this->connections as $tmp) {
            $tmp->getConnection()->send("NEW USER: " . $connection->getUser()->render());
        }
    }

    public function onMessage(ConnectionInterface $connection, $msg) {
        echo "--------ACTION--------" . PHP_EOL;
        echo "IP  : " . $this->connections[$connection->resourceId]->getUser()->getIP() . PHP_EOL;

        foreach ($this->clients as $client) {
            if ($connection !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $connection) {
        echo "--------CONNECTION CLOSED--------" . PHP_EOL;
        echo "IP  : " . $this->connections[$connection->resourceId]->getUser()->getIP() . PHP_EOL;

        // The connection is closed, remove it, as we can no longer send it messages
        unset($this->connections[$connection->resourceId]);
    }

    public function onError(ConnectionInterface $connection, \Exception $e) {
        echo "--------ERROR--------" . PHP_EOL;

        echo "IP  : " . $this->connections[$connection->resourceId]->getUser()->getIP() . PHP_EOL;
        echo "error: " . $e->getMessage() . PHP_EOL;

        $connection->close();
    }
}