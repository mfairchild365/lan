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
                    <li><a href='#' id='edit-profile'>Your Name</a></li>
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
        <!-- Modal -->
        <div id="edit-profile-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="edit-profile-modal" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                <h3 id="myModalLabel">Tell us a little about yourself</h3>
            </div>
            <div class="modal-body">
                <div id='edit-profile-alert' class="alert alert-block alert-error hide fade in">
                    <button type="button" class="close" data-dismiss="alert">X</button>
                    <h4 class="alert-heading">Oh snap! You got an error!</h4>
                    <p id='edit-profile-alert-text'>Unknown Error</p>
                </div>
                <form id='edit-profile-form' action='#'>
                    <fieldset>
                        <legend>Edit Profile</legend>
                        <label for="edit-name">
                            <input type="text" id="edit-name" />
                        </label>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                <button id='save-profile' class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</body>
</html>
