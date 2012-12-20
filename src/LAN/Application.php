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
            $this->sendToAll("USER_CONNECTED", $connection->getUser());
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
                $this->sendToAll("USER_DISCONNECTED", $this->connections[$connection->resourceId]->getUser());
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
            self::sendMessageToClient($connection, "ERROR", $e);
        }

        echo "error: " . $e->getMessage() . PHP_EOL;

        $connection->close();
    }

    public function sendToAll($action, $data)
    {
        foreach ($this->connections as $connection) {
            $connection->send($action, $data);
        }
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

    /**
     * Sends a message to the client in JSON form.
     *
     * Possible actions include
     *   - USER_INFORMATION  - Detailed information about the logged in user (sent on onConnect)
     *   - USER_CONNECTED    - Sent to all users when a user is connected.
     *   - USER_DISCONNECTED - Sent to all users when a user is disconnected.
     *   - MESSAGE_RECEIVED  - Sent to all users when a new message is received.
     *   - ERROR             - Information about an error.
     *
     * example JSON output:
     * {
     *     "action": "USER_CONNECTED",
     *     "data": {
     *         "LAN\\User\\Record": {
     *             "id": "1",
     *             "ip": "192.168.1.139",
     *             "name": "UNKNOWN",
     *             "date_created": "2012-12-20 15:34:13",
     *             "date_edited": "2012-12-20 15:34:13",
     *             "status": null,
     *             "host_name": "GHETO_BLASTER"
     *         }
     *     }
     * }
     *
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param $action
     * @param $data
     *
     * @throws Exception
     *
     * @return void
     */
    public static function sendMessageToClient(\Ratchet\ConnectionInterface $connection, $action, $data)
    {
        if(!in_array($action, array('USER_INFORMATION', 'USER_CONNECTED', 'USER_DISCONNECTED', 'MESSAGE_RECEIVED', 'ERROR'))) {
            throw new Exception("Unknown Action Type: " . $action);
        }

        $message = array();

        $message['action'] = $action;

        //Render the data if we can.
        if ($data instanceof \Lan\Renderable) {
            $newData                   = array();
            $newData[get_class($data)] = $data->render();

            $data = $newData;
        }

        $message['data'] = $data;

        $connection->send(json_encode($message));
    }
}