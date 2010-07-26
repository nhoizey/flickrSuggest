<?php
require_once 'inc/init.inc.php';

$GLOBALS['delai'] = 1;
$GLOBALS['nb'] = 0;

echo '<p>Cleaning cache files... '."\n"; flush();
cleanDir('./cache/');
echo $GLOBALS['nb'].' files removed!</p>'."\n"; flush();

echo '<p>Cleaning favorites from ignored photos... '."\n"; flush();
$GLOBALS['db']->query("DELETE FROM favorites WHERE photo_id IN (SELECT photo_id FROM ignored)");

$favs = $GLOBALS['db']->getAll("SELECT DISTINCT photo_id, count(*) AS nb2 FROM favorites WHERE user_nsid != '".FLICKR_USER_NSID."' AND nb=1 GROUP BY photo_id HAVING nb2 > 1");
echo '<p>Updating '.count($favs).' favorites counts... '."\n"; flush();
foreach($favs as $fav) {
  $GLOBALS['db']->query("UPDATE favorites SET nb=".$fav['nb2']." WHERE photo_id='".$fav['photo_id']."'");
}
echo ' done!</p>'."\n"; flush();

function cleanDir($path) {
  $dir = opendir($path);
  while ($item = readdir($dir)) {
    if ($item != '.' && $item != '..') {
      if (is_dir($path.$item)) {
        cleanDir($path.$item.'/');
      } elseif ((time() - filemtime($path.$item)) > $GLOBALS['delai']) {
        unlink($path.$item);
        $GLOBALS['nb']++;
      }
    }
  }
}
?>