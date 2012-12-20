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

        if ($this->getUserConnectionCount($connection->getUser()->getID()) == 1) {
            $this->sendToAll("NEW USER: " . $this->renderObject($connection->getUser()));
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

        //May not be a set connection if an error happened during connection.
        if (isset($this->connections[$connection->resourceId])) {
            echo "IP  : " . $this->connections[$connection->resourceId]->getUser()->getIP() . PHP_EOL;

            if ($this->getUserConnectionCount($this->connections[$connection->resourceId]->getUser()->getID()) == 1) {
                $this->sendToAll("LOGOUT: " . $this->renderObject($this->connections[$connection->resourceId]->getUser()));
            }
        }

        // The connection is closed, remove it, as we can no longer send it messages
        unset($this->connections[$connection->resourceId]);
    }

    public function onError(ConnectionInterface $connection, \Exception $e) {
        echo "--------ERROR--------" . PHP_EOL;

        //May not be a set connection if an error happened during connection.
        if (isset($this->connections[$connection->resourceId])) {
            echo "IP  : " . $this->connections[$connection->resourceId]->getUser()->getIP() . PHP_EOL;
        }

        if ($e instanceof \Lan\Renderable) {
            $connection->send($this->renderObject(($e)));
        }

        echo "error: " . $e->getMessage() . PHP_EOL;

        $connection->close();
    }

    public function sendToAll($message)
    {
        foreach ($this->connections as $connection) {
            $connection->getConnection()->send($message);
        }
    }

    public function renderObject($object)
    {
        if (!$object instanceof \Lan\Renderable) {
            throw new \Exception("Unable to render Object");
        }

        $array = array();
        $array[get_class($object)] = $object->render();

        return json_encode($array);
    }

    public function getUserConnectionCount($userID)
    {
        $count = 0;

        foreach ($this->connections as $connection) {
            if ($connection->getUser()->getID() == $userID) {
                $count++;
            }
        }

        return $count;
    }
}