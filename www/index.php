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
    <script src="<?php echo Util::getURL();?>js/main.js"></script>

    <script type="text/javascript">
        $(function(){
            app.init("ws://<?php echo \LAN\Config::get('SERVER_ADDR'); ?>:<?php echo \LAN\Config::get('SERVER_PORT'); ?>/lan");
        });
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
                <ul class='nav pull-right'>
                    <li><a href='#'><span id='connection-status' class='badge badge-important'>Offline</span></a></li>
                </ul>
            </div>
        </div>

        <div class='row-fluid'>
            <div class='span3 well'>
                <div class='page-header'>
                    <h2>Users</h2>
                </div>
                <ul id='user-list'>

                </ul>
            </div>
            <div class='span9 well'>
                <div class='page-header'>
                    <h2>Chat</h2>
                </div>
                <div id='chat-container'>
                    <ul id='message-list'>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
