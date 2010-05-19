<?php
$success = 0;
if (isset($_GET['photo_id']) && preg_match("/^[0-9]+$/", $_GET['photo_id'])) {  
  require_once 'inc/init.inc.php';
  $nb = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM photos WHERE photo_id='".$_GET['photo_id']."'");
  if ($nb == 1) {
    $success = 'This photo is already faved!';
  } else {
    include_once 'Flickr/API.php';
    $flickr =& new Flickr_API(array('api_key' => FLICKR_APIKEY));
  	$response = $flickr->callMethod('flickr.favorites.add', array('email' => FLICKR_ACCOUNT_EMAIL, 'password' => FLICKR_ACCOUNT_PASSWORD, 'photo_id' => $_GET['photo_id']));
    if ($response && $response->attributes['stat'] == 'ok') {    
      $return = $GLOBALS['db']->query("INSERT INTO photos (photo_id) VALUES ('".$_GET['photo_id']."')");
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
    } else {
      $errorCode = $flickr->getErrorCode();
      $errorMessage = $flickr->getErrorMessage();
      $success = 'Error '.$errorCode.': '.$errorMessage;
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