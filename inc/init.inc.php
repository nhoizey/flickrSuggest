<?php
ob_start();
require_once 'inc/config.inc.php';

function cleanString($string)
{
    $string = str_replace(array('"', '>', '&'), array('\'', '&gt;', '&amp;'), $string);
    return $string;
}

require_once 'inc/database.inc.php';

if (PEAR::isError($db)) {
  include_once 'inc/layout_page_top.inc.php';
  ?>
  <div id="content">
      <h2>FlickrSuggest is having a massage</h2>
      <p>It seems FlickrSuggest's database is not reachable at this time, please come back later...</p>
      <p>PEAR Error: <?php echo $db->getMessage(); ?></p>
  </div>
  <?php
  include_once 'inc/layout_page_bottom.inc.php';
  exit;
}
?>