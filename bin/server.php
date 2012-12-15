<?php
use Wrench\Socket;
use Wrench\Resource;

use \Closure;
use \InvalidArgumentException;

if (file_exists(dirname(dirname(__FILE__)) . '/config.inc.php')) {
    require_once dirname(dirname(__FILE__)) . '/config.inc.php';
} else {
    require dirname(dirname(__FILE__)) . '/config.sample.php';
}

$server = new \Wrench\Server('ws://' . \LAN\Config::get('SERVER_ADDR') . ':' . \LAN\Config::get('SERVER_PORT') . '/');

$server->registerApplication(\LAN\Config::get('APPLICATION_NAME'), new \LAN\Application());

$server->run();

