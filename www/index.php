<?php
    if (file_exists(dirname(dirname(__FILE__)) . '/config.inc.php')) {
        require_once dirname(dirname(__FILE__)) . '/config.inc.php';
    } else {
        require dirname(dirname(__FILE__)) . '/config.sample.php';
    }
?>

<html>
    <head>
        <script>
            try {
                var conn = new WebSocket('ws://<?php echo \LAN\Config::get('SERVER_ADDR'); ?>:<?php echo \LAN\Config::get('SERVER_PORT'); ?>/lan');
                console.log('WebSocket - status '+conn.readyState);
                conn.onopen = function(e) {
                    console.log("Connection established!");
                };
                conn.onmessage = function(e) {
                    console.log(e.data);
                };
            } catch(ex){
                console.log(ex);
            }
        </script>
    </head>
</html>