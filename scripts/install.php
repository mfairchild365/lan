<?php
if (file_exists(dirname(dirname(__FILE__)) . '/config.inc.php')) {
    require_once dirname(dirname(__FILE__)) . '/config.inc.php';
} else {
    require dirname(dirname(__FILE__)) . '/config.sample.php';
}

function exec_sql($db, $sql, $message, $fail_ok = false)
{
    echo $message.PHP_EOL;
    try {
        $result = true;
        if ($db->multi_query($sql)) {
            do {
                /* store first result set */
                if ($result = $db->store_result()) {
                    $result->free();
                }
            } while ($db->next_result());
        } else {
            echo "Query Failed: " . $db->error . PHP_EOL;
        }
    } catch (Exception $e) {
        $result = false;
        if (!$fail_ok) {
            echo 'The query failed:'.$result->errorInfo();
            exit();
        }
    }
    echo 'finished.'.PHP_EOL;
    echo '------------------------------------------'.PHP_EOL;
    return $result;
}

$db = \LAN\Util::getDB();

$sql = "";

if (isset($argv[1]) && $argv[1] == '-f') {
    echo "Deleting old install" . PHP_EOL;
    $sql .= "SET FOREIGN_KEY_CHECKS=0;
             DROP TABLE IF EXISTS users;
             SET FOREIGN_KEY_CHECKS=1;";
}
$sql .= file_get_contents(dirname(dirname(__FILE__)) . "/data/database.sql");

exec_sql($db, $sql, 'updatating database');