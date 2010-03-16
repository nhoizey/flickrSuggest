<?php
require_once 'DB.php';
$db = &DB::connect(DSN);

//if (PEAR::isError($db)) {
//    die('could not connect');
//}

if (!PEAR::isError($db)) {
    $db->setFetchMode(DB_FETCHMODE_ASSOC);
}
?>