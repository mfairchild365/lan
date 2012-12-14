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

//Get the IP address of the server (UNIX ONLY... FIRST ETHERNET PORT ONLY) TODO: make configurable
$ip = str_replace("\n","",shell_exec("ifconfig eth0 | grep 'inet addr' | awk -F':' {'print $2'} | awk -F' ' {'print $1'}"));

$server = new \Wrench\Server('ws://' . $ip . ':8000/', array(
        'allowed_origins' => array(
            'mysite.localhost'
        ),
    )
);

$server->registerApplication('lan', new \LAN\Application());

$server->run();

