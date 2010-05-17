<?php
require_once 'inc/init.inc.php';
require_once 'inc/layout_page_top.inc.php';
require_once 'inc/photo.inc.php';
require_once 'inc/favorites.inc.php';

$nb_favs = defined('BROWSE_MIN_NEIGHBOURS') ? BROWSE_MIN_NEIGHBOURS : 5;
?>

<h2>Quick Review</h2>

<?php
$total = $db->getOne("SELECT COUNT(DISTINCT photo_id) FROM favorites WHERE nb >= ".$nb_favs);
$num = rand(0, $total - 1);
$data = $db->getRow("SELECT DISTINCT photo_id, nb FROM favorites WHERE nb >= ".$nb_favs." LIMIT ".$num.",1");
$photo_id = $data['photo_id'];
$nb = $data['nb'];
$photo = getPhoto($photo_id);
if (!is_array($photo)) {
  if ($photo == 'removed') {
    echo '<img src="/img/photo_gone.gif" width="75" height="75" alt="Removed" />';
  } else {
    echo '<img src="/img/photo_error.gif" width="75" height="75" alt="Error" title="'.$photo.'" />';
  }
} else {
  echo '<a href="'.$photo['url'].'"><img src="'.$photo['medium'].'" style="float: right;" /></a>';
  echo '<h3><a href="'.$photo['url'].'">'.(trim($photo['title']) != '' ? htmlspecialchars_decode(trim($photo['title'])) : 'untitled').'</a></h3>';
  echo '<p>By '.$photo['owner']['username'].'</p>';
  echo '<p>Faved by <strong>'.$nb.'</strong> neighbours</p>';
  echo '<p><a href="/ignore.php?photo_id='.$photo_id.'">ignore</a></p>';
}

require_once 'inc/layout_page_bottom.inc.php';
?>