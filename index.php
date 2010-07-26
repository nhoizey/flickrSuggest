<?php
require_once 'inc/init.inc.php';
require_once 'inc/layout_page_top.inc.php';
require_once 'inc/photo.inc.php';
require_once 'inc/favorites.inc.php';

$nb_favs = defined('BROWSE_MIN_NEIGHBOURS') ? BROWSE_MIN_NEIGHBOURS : 5;
$nb = defined('BROWSE_PER_PAGE') ? BROWSE_PER_PAGE : 15;

if (isset($_GET['page']) && intval($_GET['page']) > 0) {
    $page = intval($_GET['page']);
} else {
    $page = 1;
}

$myFavs = implode(', ', $db->getCol("SELECT photo_id FROM favorites WHERE user_nsid = '".FLICKR_USER_NSID."'"));

$total = $db->getOne("SELECT COUNT(DISTINCT photo_id) FROM favorites WHERE nb >= ".$nb_favs." AND photo_id NOT IN (".$myFavs.")");

$nbPages = max(ceil($total / $nb), 1);
if ($page > $nbPages) {
	$page = $nbPages;
}
$pager = '<div class="pager">Pages: ';
for ($i = 1; $i <= $nbPages; $i++) {
	if ($i == 1 || $i == $nbPages || in_array(abs($i - $page), array(0, 1, 2, 3, 15, 75, 375))) {
		if ($i == $page) {
			$pager .= ' <strong>'.$i.'</strong>';
		} else {
			$pager .= ' <a href="?page='.$i.'">'.$i.'</a>';
		}
	} else {
			$pager .= '#';
	}
}
$pager = ereg_replace("#+", " ...", $pager);
$pager .= ' <span class="num">('.$total.' photos)</span></div>';
?>

<h2>Suggestions</h2>
<p>These suggestions are based on favorites from <?php echo ($db->getOne("SELECT COUNT(user_nsid) FROM users WHERE ignored=0") - 1); ?> users that share at least <?php echo NEIGHBOURHOOD_DISTANCE; ?> favorites with me. <?php echo $db->getOne("SELECT COUNT(user_nsid) FROM users WHERE ignored=1"); ?> users are ignored because they have at least <?php echo IGNORED_DISTANCE; ?> favorites that I ignored.</p>
<?php
echo $pager;
$suggestions = $db->getAll("SELECT DISTINCT photo_id, nb FROM favorites WHERE nb >= ".$nb_favs." AND photo_id NOT IN (".$myFavs.") ORDER BY nb DESC, photo_id LIMIT ".(($page - 1) * $nb).",".$nb);
if ($total > 0) {
  echo '<ol class="gallery">';  
  foreach($suggestions as $data) {
  	echo getPhotoHTML($data['photo_id'], $data['nb']);
  }
  echo '</ol>'.$pager;
}

require_once 'inc/layout_page_bottom.inc.php';
?>