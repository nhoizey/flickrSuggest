<?php
require_once 'inc/init.inc.php';
require_once 'inc/layout_page_top.inc.php';
require_once 'inc/photo.inc.php';
require_once 'inc/favorites.inc.php';

$nb = (isset($_GET['nb']) && is_numeric($_GET['nb'])) ? $_GET['nb'] : (defined('BROWSE_PER_PAGE') ? BROWSE_PER_PAGE : 18);

if (isset($_GET['page']) && intval($_GET['page']) > 0) {
    $page = intval($_GET['page']);
} else {
    $page = 1;
}

$total = $db->getOne("SELECT COUNT(photo_id) FROM favorites WHERE user_nsid = '".FLICKR_USER_NSID."'");

$nbPages = max(ceil($total / $nb), 1);
if ($page > $nbPages) {
	$page = $nbPages;
}
$pager = '<div class="pager">Pages: ';
if ($nbPages > 1) {
  if ($page == 1) {
  	$pager .= ' <strong>«</strong>';
  } else {
		$pager .= ' <a href="?nb='.$nb.'&page='.($page - 1).'">«</a>';
  }
}
for ($i = 1; $i <= $nbPages; $i++) {
	if ($i == 1 || $i == $nbPages || in_array(abs($i - $page), array(0, 1, 2, 3, 15, 75, 375))) {
		if ($i == $page) {
			$pager .= ' <strong>'.$i.'</strong>';
		} else {
			$pager .= ' <a href="?nb='.$nb.'&page='.$i.'">'.$i.'</a>';
		}
	} else {
			$pager .= '#';
	}
}
if ($nbPages > 1) {
  if ($page == $nbPages) {
  	$pager .= ' <strong>»</strong>';
  } else {
		$pager .= ' <a href="?nb='.$nb.'&page='.($page + 1).'">»</a>';
  }
}
$pager = preg_replace("/#+/", " ...", $pager);
$pager .= ' <span class="num">('.$total.' photos)</span></div>';
?>

<h2>Favorites</h2>
<p class="params">
  How many photos per page:
  <?php
  foreach(array(6, 12, 18, 24, 30) AS $nbProp) {
    if ($nbProp == $nb) {
      echo '<strong>'.$nbProp.'</strong> ';
    } else {
      echo '<a href="?nb='.$nbProp.'&page='.$page.'">'.$nbProp.'</a> ';
    }
  }
  ?>
</p>
<?php
echo $pager;
$favorites = $db->getAll("SELECT photo_id FROM favorites WHERE user_nsid = '".FLICKR_USER_NSID."' ORDER BY date_faved DESC LIMIT ".(($page - 1) * $nb).",".$nb);
if ($total > 0) {
  echo '<ol class="gallery">';  
  foreach($favorites as $data) {
  	echo getPhotoHTML($data['photo_id'], 0);
  }
  echo '</ol>'.$pager;
}

require_once 'inc/layout_page_bottom.inc.php';
?>