<?php

if (file_exists(dirname(dirname(__FILE__)) . '/config.inc.php')) {
    require_once dirname(dirname(__FILE__)) . '/config.inc.php';
} else {
    require dirname(dirname(__FILE__)) . '/config.sample.php';
}

require dirname(__FILE__) . '/../vendor/openid.php';

$baseURL = \LAN\Util::getURL();
$authURL = $baseURL . "auth.php";

if (!isset($_COOKIE['lan'])) {
    echo "You need to log into the LAN system first...";
    exit();
}

//connect to DB.
\LAN\Util::connectDB();

//Get the user
if (!$user = \LAN\User\Record::getByID((int)$_COOKIE['lan'])) {
    echo "Unable to find your user record...";
    exit();
}
echo $user->getIP() . " and " . $_SERVER['REMOTE_ADDR'];
//do a little security check
if ($user->getIP() != $_SERVER['REMOTE_ADDR']) {
    echo "You failed the security check...";
    exit();
}

try {
    $openid = new LightOpenID($authURL);

    //check if we need to send the user to steam.
    if(!$openid->mode) {
        $openid->identity = "http://steamcommunity.com/openid";
        header('Location: ' . $openid->authUrl());
        exit();
    }

    if($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
        exit();
    }

    if ($openid->validate()) {
        $id = substr($openid->identity, 36);

        $user->setSteamID64($id);
        $user->save();

        header('Location: ' . $baseURL);
        exit();
    } else {
        echo "You have chosen not to identify yourself.  Please try again if you feel this was an error.";
    }
} catch(ErrorException $e) {
    echo $e->getMessage();
}