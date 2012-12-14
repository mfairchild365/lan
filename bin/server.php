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

$server = new \Wrench\Server('ws://192.168.1.131:8000/', array(
        'allowed_origins' => array(
            'mysite.localhost'
        ),
    )
);

$server->registerApplication('lan', new \LAN\Application());

$server->run();

