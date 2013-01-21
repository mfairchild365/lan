<?php
if (file_exists(dirname(dirname(__FILE__)) . '/config.inc.php')) {
    require_once dirname(dirname(__FILE__)) . '/config.inc.php';
} else {
    require dirname(dirname(__FILE__)) . '/config.sample.php';
}

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__) . '/vendor/autoload.php';

\LAN\Util::connectDB();

// we are the parent
echo "Starting Server..." . PHP_EOL;

$app = &new \LAN\Application();

//Start the actual server
$server = IoServer::factory(
    new WsServer(
        $app
    ),
    \LAN\Config::get('SERVER_PORT'), \LAN\Config::get('SERVER_ADDR')
);

$server->loop->addPeriodicTimer(\LAN\Config::get('STEAM_CACHE_TTL_PROFILES'), function($app) {
    echo "STARTING 'STEAM' REQUEST" . PHP_EOL;

    //Get JSON and send to all clients.
    $json = \LAN\Steam\Profiles::getJSON(false);

    \LAN\Application::sendToAll('STEAM_PROFILES', $json);
});

$server->run();
