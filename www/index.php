<?php
namespace LAN;

if (file_exists(dirname(dirname(__FILE__)) . '/config.inc.php')) {
    require_once dirname(dirname(__FILE__)) . '/config.inc.php';
} else {
    require dirname(dirname(__FILE__)) . '/config.sample.php';
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Bootstrap 101 Template</title>
    <!-- Bootstrap using http://bootswatch.com/cyborg/ -->
    <link href="<?php echo Util::getURL();?>css/bootstrap.min.css" rel="stylesheet" media="screen">

    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script src="<?php echo Util::getURL();?>js/bootstrap.min.js"></script>

    <script type="text/javascript">
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
<body>
    <div class="container-fluid">
        <div class="navbar">
            <div class="navbar-inner">
                <a class="brand" href="#">LAN</a>
                <ul class="nav">
                    <li class="active"><a href="#">Home</a></li>
                </ul>
            </div>
        </div>

        <div class='row-fluid'>
            <div class='span3 well'>
                <div class='page-header'>
                    <h2>Users</h2>
                </div>
            </div>
            <div class='span9 well'>
                <div class='page-header'>
                    <h2>Chat</h2>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
