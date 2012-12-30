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
        //Save in array
        $this->connections[$connection->resourceId] = new ConnectionContainer($connection);

        //Set as online.
        $this->connections[$connection->resourceId]->getUser()->setStatus("ONLINE");
        $this->connections[$connection->resourceId]->getUser()->save();

        //Display connection on server.
        echo "--------NEW CONNECTION--------" . PHP_EOL;
        echo "ID  : " . $this->connections[$connection->resourceId]->getConnection()->resourceId . PHP_EOL;
        echo "IP  : " . $this->connections[$connection->resourceId]->getUser()->getIP() . PHP_EOL;
        echo "MAC : " . $this->connections[$connection->resourceId]->getUser()->getMAC() . PHP_EOL;

        //Update the client's list with all users currently online.
        foreach (User\RecordList::getAllOnline() as $user) {
            $this->connections[$connection->resourceId]->send('USER_CONNECTED', $user);
        }

        //Tell everyone else that this guy just came online.
        if ($this->getUserConnectionCount($this->connections[$connection->resourceId]->getUser()->getID()) == 1) {
            $this->sendToAll("USER_CONNECTED", $this->connections[$connection->resourceId]->getUser());
        }

        $this->connections[$connection->resourceId]->send('USER_INFORMATION', $this->connections[$connection->resourceId]->getUser());
    }

    public function onMessage(ConnectionInterface $connection, $msg) {
        echo "--------ACTION--------" . PHP_EOL;
        echo "IP  : " . $this->connections[$connection->resourceId]->getUser()->getIP() . PHP_EOL;

        $data = json_decode($msg, true);

        if (!isset($data['action'])) {
            throw new Exception("An action must be passed.");
        }

        $class = '';

        switch ($data['action']) {
            case 'UPDATE_USER':
                $class = '\LAN\User\ActionHandler';
                break;
        }

        $handler = new $class;

        $result = $handler->handle($data['action'], $data['data'], $this->connections[$connection->resourceId]);

        if ($result) {
            $this->sendToAll($result['action'], $result['data']);
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

            //Set as offline
            $this->connections[$connection->resourceId]->getUser()->setStatus("OFFLINE");
            $this->connections[$connection->resourceId]->getUser()->save();
        }

        $connection = $this->connections[$connection->resourceId];

        // The connection is closed, remove it, as we can no longer send it messages
        unset($this->connections[$connection->getConnection()->resourceId]);

        $this->sendToAll("USER_DISCONNECTED", $connection->getUser());
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
     *   - USER_UPDATED      - Sent to everyone when a user has been updated
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
        if(!in_array($action, array('USER_INFORMATION', 'USER_CONNECTED', 'USER_DISCONNECTED', 'MESSAGE_RECEIVED', 'ERROR', 'USER_UPDATED'))) {
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