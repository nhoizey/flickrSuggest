<?php
$begin= time();
require_once 'inc/init.inc.php';
require_once 'inc/layout_page_top.inc.php';
require_once 'inc/photo.inc.php';
require_once 'inc/favorites.inc.php';
ob_end_flush();

echo '<h2>Updating data</h2>';
echo '<p>Current date/time: '.date("d/m/Y H:i").'</p>';

echo '<h3>Updating <a href="http://www.flickr.com/photos/'.FLICKR_USER_NSID.'/favorites/">my favorites</a>...</h3>'."\n"; flush();
$nb = updateFavsFromUser(FLICKR_USER_NSID);
echo '<p>'.$nb.' fav'.($nb > 1 ? 's' : '').' added!</p>'."\n"; flush();

if ($olderUser = $db->getOne("SELECT user_nsid FROM users WHERE user_nsid != '".FLICKR_USER_NSID."' ORDER BY date_updated LIMIT 0,1")) {
  echo '<h3>Updating <a href="http://www.flickr.com/photos/'.$olderUser.'/favorites/">'.$olderUser.'\'s favorites</a>...</h3>'."\n"; flush();
  $nb = updateFavsFromUser($olderUser);
  echo '<p>'.$nb.' fav'.($nb > 1 ? 's' : '').' added!</p>'."\n"; flush();
}

if ($olderPhoto = $db->getOne("SELECT photo_id FROM photos ORDER BY date_updated LIMIT 0,1")) {
  echo '<h3>Updating users who have favorited '.$olderPhoto.'...</h3>'."\n"; flush();
  echo '<ol class="gallery">'.getPhotoHTML($olderPhoto).'</ol>'."\n"; flush();
  $nb = updateUsersFromFav($olderPhoto);
  echo '<p style="clear: left">'.$nb.' user'.($nb > 1 ? 's' : '').' added!</p>'."\n"; flush();
}

require 'inc/close.inc.php';
echo '<p>Done in '.(time() - $begin).' seconds.</p>';
?>

<p>This page will self refresh in 30 seconds...</p>
<script language="javascript">
setTimeout("window.location.reload()", 1000*30);
</script>

<?php
require_once 'inc/layout_page_bottom.inc.php';
?>
