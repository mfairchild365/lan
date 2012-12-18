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
