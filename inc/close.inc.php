<?php
if (isset($GLOBALS['db']) && is_a($GLOBALS['db'], 'DB_mysql')) {
	$GLOBALS['db']->disconnect();
}
?>