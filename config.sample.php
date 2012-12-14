<?php
/**********************************************************************************************************************
* autoload and include path
*/
function autoload($class)
{
    $file = str_replace(array('_', '\\'), '/', $class).'.php';

    if ($fullpath = stream_resolve_include_path($file)) {
        include $fullpath;
        return true;
    }

    return false;
}

spl_autoload_register("autoload");

set_include_path(
    implode(PATH_SEPARATOR, array(get_include_path())).PATH_SEPARATOR
            .dirname(__FILE__) . '/lib/Epoch/src'.PATH_SEPARATOR //path to Epoch's src dir.
            .dirname(__FILE__) . '/lib'.PATH_SEPARATOR
            .dirname(__FILE__) . '/src'.PATH_SEPARATOR
);

/**********************************************************************************************************************
* php related settings
*/

ini_set('display_errors', false);

error_reporting(E_ALL);
