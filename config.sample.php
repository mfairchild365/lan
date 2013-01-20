<?php
use LAN\Config;

/**********************************************************************************************************************
* autoload and include path
*/
require __DIR__ . '/vendor/autoload.php';

/**********************************************************************************************************************
* php related settings
*/

ini_set('display_errors', false);

error_reporting(E_ALL);

/**********************************************************************************************************************
 * DB related settings
 */
Config::set('DB_HOST'     , 'localhost');
Config::set('DB_USER'     , 'user');
Config::set('DB_PASSWORD' , 'password');
Config::set('DB_NAME'     , 'lan');


#Config::set('SERVER_ADDR', '192.168.1.x');  //Set this manually if the application can't find it.

#Config::set('SERVER_ETH', 'eth0'); //The ethernet port to use (for automatic calls).  MAC servers should use something like en1

#Config::set('SERVER_PORT', 8000); //Websockets port to use

#Config::set('WWW_PATH', '/lan/www/'); //URL relativie to the server's ip address to the www directory.

/**********************************************************************************************************************
 * Steam related settings
 */

#Config::set('STEAM_API_KEY', 'your-steam-api-key');  //Required to sync with steam.  Get an api key here: http://steamcommunity.com/dev/apikey

#Config::set('STEAM_CACHE_TTL_PROFILES', 10);  //10 seconds


