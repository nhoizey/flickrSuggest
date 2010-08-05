<?php
$success = 0;
if (isset($_GET['photo_id']) && preg_match("/^[0-9]+$/", $_GET['photo_id'])) {  
  require_once 'inc/init.inc.php';
  $nb = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM ignored WHERE photo_id='".$_GET['photo_id']."'");  
  if ($nb == 1) {
    $success = 'This photo is already ignored!';
  } else {
    $isFav = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM favorites WHERE user_nsid='".FLICKR_USER_NSID."' AND photo_id='".$_GET['photo_id']."'");
    if ($isFav == 1) {
      $success = 'Can\'t ignore a favorited photo!';
    } else {
      $return = $GLOBALS['db']->query("INSERT INTO ignored (photo_id) VALUES ('".$_GET['photo_id']."')");
      if (PEAR::isError($return)) {
        $success = $return->getMessage();
      } else {
        $return = $GLOBALS['db']->query("DELETE FROM favorites WHERE photo_id='".$_GET['photo_id']."'");
        if (PEAR::isError($return)) {
          $success = $return->getMessage();
        } else {
          $success = 1;
        }
      }
    }
  }
  require 'inc/close.inc.php';
}
if (isset($_GET['mode']) && $_GET['mode'] == 'ajax') {
  echo $success;
} elseif (1 === $success) {
  header('Location: '.(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/'));
} else {
  echo $success;
}
?>