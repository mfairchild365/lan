<?php
if (file_exists(dirname(dirname(__FILE__)) . '/config.inc.php')) {
    require_once dirname(dirname(__FILE__)) . '/config.inc.php';
} else {
    require dirname(dirname(__FILE__)) . '/config.sample.php';
}

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__) . '/vendor/autoload.php';

//Start the steam cache server
$pid = pcntl_fork();
if ($pid == -1) {
    die('could not fork');
} else if ($pid) {
    // we are the parent

    echo "Starting Server..." . PHP_EOL;

    //Start the actual server
    $server = IoServer::factory(
        new WsServer(
            new \LAN\Application()
        ),
        \LAN\Config::get('SERVER_PORT'), \LAN\Config::get('SERVER_ADDR')
    );

    $server->run();

    pcntl_wait($status); //Protect against Zombie children
} else {
    echo "Starting Steam Caching Server..." . PHP_EOL;

    //We have to re-include everything because we are not the parent fork.
    if (file_exists(dirname(dirname(__FILE__)) . '/config.inc.php')) {
        require_once dirname(dirname(__FILE__)) . '/config.inc.php';
    } else {
        require dirname(dirname(__FILE__)) . '/config.sample.php';
    }

    //Connect to the DB.
    \LAN\Util::setDB(\LAN\Config::get('DB_HOST'),
        \LAN\Config::get('DB_USER'),
        \LAN\Config::get('DB_PASSWORD'),
        \LAN\Config::get('DB_NAME')
    );

    while(true) {
        //Grad a request and make sure it is cached.
        $json = \LAN\Steam\Profiles::getJSON(false);

        //Sleep and grab a new one.
        sleep(5);
    }

    exit();
}
