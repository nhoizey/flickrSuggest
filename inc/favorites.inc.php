<?php
function id2nsid($user_id) {
  include_once 'Flickr/API.php';
  static $flickr = null;

  if (is_null($flickr)) {
    $flickr = new Flickr_API(array('api_key' => FLICKR_APIKEY));
  }

	$userResponse = $flickr->callMethod('flickr.urls.lookupUser', array('url' => 'http://www.flickr.com/photos/'.$user_id.'/'));
	if ($userResponse && $userResponse->attributes['stat'] == 'ok') {
		$data = $userResponse->getNodeAt('user');
		$user_nsid = $data->attributes['id'];
	} elseif (ereg("^[0-9]+@N[0-9]+$", $user_id)) {
			$user_nsid = $user_id;
	}
	return $user_nsid;
}

function addUser($user_nsid) {
  global $db;

  if ($db->getOne("SELECT COUNT(user_nsid) FROM users WHERE user_nsid = '".$user_nsid."'") == 0) {
    $result = $db->query("INSERT INTO users (user_nsid, date_updated) VALUES ('".$user_nsid."', 0)");
    if (!PEAR::isError($result)) {
      return true;
    } else {
      if (SHOW_DEBUG) {
        $errorCode = $result->getCode();
        $errorMessage = $result->getMessage();
        echo '<p>Error '.$errorCode.': '.$errorMessage.'</p>';
      }
      return false;
    }
  } else {
    return false;
  }
}

function addMyFav($photo_id, $photographer_nsid, $date_faved) {
  global $db;

  if ($photo_id != ''
      && $photographer_nsid != ''
      && $db->getOne("SELECT COUNT(*) FROM favorites WHERE user_nsid = '".FLICKR_USER_NSID."' AND photo_id = '".$photo_id."'") == 0) {
    $db->query("INSERT INTO favorites (user_nsid, photo_id, photographer_nsid, date_faved) VALUES ('".FLICKR_USER_NSID."', '".$photo_id."', '".$photographer_nsid."', ".$date_faved.")");
    // Remove it from ignored
    $db->query("DELETE FROM ignored WHERE photo_id = '".$photo_id."'");
    return 1;
  } else {
    return 0;
  }
}

function addFav($user_nsid, $photo_id, $photographer_nsid, $date_faved) {
  global $db;

  if ($user_nsid == FLICKR_USER_NSID) {
    return addMyFav($photo_id, $photographer_nsid, $date_faved);
  } elseif ($user_nsid != ''
      && $photo_id != ''
      && $photographer_nsid != ''
      && $db->getOne("SELECT COUNT(*) FROM ignored WHERE photo_id = '".$photo_id."'") == 0
      && $db->getOne("SELECT COUNT(*) FROM favorites WHERE user_nsid = '".$user_nsid."' AND photo_id = '".$photo_id."'") == 0) {
    $result = $db->query("INSERT INTO favorites (user_nsid, photo_id, photographer_nsid, date_faved) VALUES ('".$user_nsid."', '".$photo_id."', '".$photographer_nsid."', ".$date_faved.")");
    $nb = $db->getOne("SELECT COUNT(*) AS nb FROM favorites WHERE photo_id = '".$photo_id."'");
    $result = $db->query("UPDATE favorites SET nb = ".$nb." WHERE photo_id = '".$photo_id."'");
    return 1;
  } else {
    return 0;
  }
}

function updateFavsFromUser($user_nsid, $page = 1) {
  include_once 'Flickr/API.php';

  global $db;

  static $user_nsid_prev = null;
  static $flickr = null;
  static $lastFav = null;
  static $num_fav = null;
  static $nb_favs = null;

  if (!is_null($user_nsid_prev) && $user_nsid_prev != $user_nsid) {
    $lastFav = null;
    $num_fav = null;
    $nb_favs = null;
  }
  $user_nsid_prev = $user_nsid;
  
  if (is_null($flickr)) {
    $flickr = new Flickr_API(array('api_key' => FLICKR_APIKEY));
  }

  if (is_null($lastFav)) {
    // Get last favorite already in database
    $date_faved = $db->getOne("SELECT date_faved FROM favorites WHERE user_nsid = '".$user_nsid."' ORDER BY date_faved DESC LIMIT 0,1");
    if (is_null($date_faved) || PEAR::isError($date_faved)) {
      $lastFav = -1;
    } else {
      $lastFav = $date_faved;
    }
  }

  if (is_null($num_fav)) {
    $num_fav = 0;
  }

  if (is_null($nb_favs)) {
    $nb_favs = 0;
  }

  $response = $flickr->callMethod('flickr.favorites.getPublicList', array('email' => FLICKR_ACCOUNT_EMAIL, 'password' => FLICKR_ACCOUNT_PASSWORD, 'user_id' => $user_nsid, 'per_page' => 50, 'page' => $page, 'min_fave_date' => $lastFav));
  if ($response && $response->attributes['stat'] == 'ok') {
    $data = $response->getNodeAt('photos');
    $pages = intval($data->attributes['pages']);
    foreach ($data->children as $child) {
      $num_fav++;
      if ($child->name == 'photo') {
        if (isset($child->attributes['id']) && $child->attributes['id'] != '') {
          $photo_id = $child->attributes['id'];
          $photographer_nsid = $child->attributes['owner'];
          $date_faved = $child->attributes['date_faved'];
          if (addFav($user_nsid, $photo_id, $photographer_nsid, $date_faved)) {
            $nb_favs++;
          }
        } else {
          if (SHOW_DEBUG) {
            echo '<h4>Photo num '.$num_fav.' has no id! (previous one was '.$photo_id.')</h4>'."\n";
            echo '<p>user_id => '.$user_nsid.', per_page => 50, page => '.$page.', min_fave_date => '.$lastFav.'</p>'."\n";
            echo '<pre>'.print_r($child, true).'</pre><hr />'."\n"; flush();
          }
        }
      }
    }
    if ($page < $pages) {
      updateFavsFromUser($user_nsid, $page + 1);
    } else {
      $db->query("REPLACE INTO users (user_nsid, date_updated) VALUES ('".$user_nsid."', ".time().")");
      if ($user_nsid == FLICKR_USER_NSID
          || $db->getOne("SELECT COUNT(photo_id) FROM favorites  WHERE user_nsid = '".$user_nsid."' AND photo_id IN (SELECT photo_id FROM favorites WHERE user_nsid = '".FLICKR_USER_NSID."')") >= NEIGHBOURHOOD_DISTANCE) {
        $db->query("UPDATE favorites SET checked=1 WHERE user_nsid = '".$user_nsid."'");
      } else {
        if ($user_nsid != FLICKR_USER_NSID
            && $db->getOne("SELECT COUNT(photo_id) FROM favorites  WHERE user_nsid = '".$user_nsid."' AND photo_id IN (SELECT photo_id FROM ignored)") >= IGNORED_DISTANCE) {
          $db->query("UPDATE users SET ignored=1 WHERE user_nsid = '".$user_nsid."'");
        }
        $db->query("DELETE FROM favorites WHERE user_nsid = '".$user_nsid."'");
        $nb_favs = 0;
      }
    }
  } elseif (SHOW_DEBUG) {
    var_dump($response);
	}
  return $nb_favs;
}

function updateUsersFromFav($photo_id, $page = 1)
{
  include_once 'Flickr/API.php';

  global $db;

  static $photo_id_prev = null;
  static $flickr = null;
  static $lastFav = null;
  static $num_user = null;
  static $nb_users = null;
  static $photographer_nsid = null;

  if (!is_null($photo_id_prev) && $photo_id_prev != $photo_id) {
    $lastFav = null;
    $num_user = null;
    $nb_users = null;
    $photographer_nsid = null;
  }
  $photo_id_prev = $photo_id;

  if (is_null($flickr)) {
    $flickr = new Flickr_API(array('api_key' => FLICKR_APIKEY));
  }

  if (is_null($lastFav)) {
    // Get last favorite already in database
    $date_faved = $db->getOne("SELECT date_updated FROM favorites WHERE user_nsid = '".FLICKR_USER_NSID."' AND photo_id = '".$photo_id."'");
    if (is_null($date_faved) || PEAR::isError($date_faved)) {
      $lastFav = -1;
    } else {
      $lastFav = $date_faved;
    }
  }

  if (is_null($num_user)) {
    $num_user = 0;
  }

  if (is_null($nb_users)) {
    $nb_users = 0;
  }

  if (is_null($photographer_nsid)) {
    $photographer_nsid = $db->getOne("SELECT photographer_nsid FROM favorites WHERE photo_id = '".$photo_id."' LIMIT 0,1");
  }

  if (SHOW_DEBUG) {
    echo '<p><strong>API Request</strong></p><dl><dt>method</dt><dd>flickr.favorites.getPublicList</dd><dt>photo_id<dt><dd>'.$photo_id.'</dd><dt>per_page</dt><dd>50</dd><dt>page</dt><dd>'.$page.'</dd></dl>';
  }
  $response = $flickr->callMethod('flickr.photos.getFavorites', array('email' => FLICKR_ACCOUNT_EMAIL, 'password' => FLICKR_ACCOUNT_PASSWORD, 'photo_id' => $photo_id, 'per_page' => 50, 'page' => $page));
  if ($response) {
    if ($response->attributes['stat'] == 'ok') {
      $data = $response->getNodeAt('photo');
      $pages = intval($data->attributes['pages']);
      foreach ($data->children as $child) {
        if ($child->name == 'person') {
          $num_user++;
          $user_nsid = $child->attributes['nsid'];
          $date_faved = $child->attributes['favedate'];
          if ($date_faved < $lastFav) {
            $db->query("UPDATE favorites SET date_updated = ".time()." WHERE user_nsid = '".FLICKR_USER_NSID."' AND photo_id = '".$photo_id."'");
            return $nb_users;
          } else {
            if (addUser($user_nsid)) {
      				$nb_users++;
            }
    				addFav($user_nsid, $photo_id, $photographer_nsid, $date_faved);
      		}
        }
      }
      if ($page < $pages) {
        updateUsersFromFav($photo_id, $page + 1);
      } else {
        $db->query("UPDATE favorites SET date_updated = ".time()." WHERE user_nsid = '".FLICKR_USER_NSID."' AND photo_id = '".$photo_id."'");
      }
  	} else {
      if (SHOW_DEBUG) {
    	  $errorCode = $flickr->getErrorCode();
    	  $errorMessage = $flickr->getErrorMessage();
        echo '<p>Error '.$errorCode.': '.$errorMessage.'</p>';
      }
    }
  } elseif ($flickr->getErrorCode() == 1) {
    // Photo not found
    $db->query("DELETE FROM favorites WHERE photo_id = '".$photo_id."'");
    $db->query("DELETE FROM ignored WHERE photo_id = '".$photo_id."'");
  } else {
    if (SHOW_DEBUG) {
      echo '<p>Error: No response for flickr.photos.getFavorites with per_page=50 and page='.$page.'</p>';
      echo '<p>HTTP code: '.$flickr->_http_code.'</p>';
      echo '<p>HTTP head: '.print_r($flickr->_http_head, true).'</p>';
      echo '<p>HTTP body: '.htmlspecialchars($flickr->_http_body).'</p>';
      $errorCode = $flickr->getErrorCode();
      $errorMessage = $flickr->getErrorMessage();
      echo '<p>Error '.$errorCode.': '.$errorMessage.'</p>';
      var_dump($response);
    }
    // We should count errors and remove photo after nth
    $db->query("UPDATE photos SET date_updated = ".time()." WHERE photo_id = '".$photo_id."'");
    return 0;
  }
  
  return $nb_users;
}
?>