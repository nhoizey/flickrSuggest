<?php
function getPhoto($photoId)
{
    if (!is_numeric($photoId)) {
        $photo = 'bad_id: '.print_r($photoId, true);
    } else {
        include_once 'Cache/Lite.php';
        $cachePhoto = new Cache_Lite(array('cacheDir' => './cache/photos/', 'lifeTime' => CACHE_LIFETIME_PHOTO, 'automaticCleaningFactor' => 100, 'hashedDirectoryLevel' => 2));
        if ($photo = $cachePhoto->get('photo-'.$photoId)) {
            $photo = unserialize($photo);
        } else {
            include_once 'Flickr/API.php';
            $flickr = new Flickr_API(array('api_key' => FLICKR_APIKEY));
            $response = $flickr->callMethod('flickr.photos.getInfo', array('photo_id' => $photoId));
            if ($response) {
                if($response->attributes['stat'] == 'ok') {
                    $photo = array('id' => $photoId);
                    $data = $response->getNodeAt('photo/visibility');
                    $photo['ispublic'] = intval($data->attributes['ispublic']);
                    if ($photo['ispublic']) {
                        $data = $response->getNodeAt('photo');
                        $server = $data->attributes['server'];
                        $secret = $data->attributes['secret'];
                        $data = $response->getNodeAt('photo/owner');
                        $photo['owner'] = array();
                        $photo['owner']['NSID'] = $data->attributes['nsid'];
                        $photo['owner']['username'] = cleanString($data->attributes['username']);
                        $photo['owner']['realname'] = cleanString($data->attributes['realname']);
                        $data = $response->getNodeAt('photo/title');
                        $photo['title'] = cleanString($data->content);
                        $photo['75x75'] = 'http://static.flickr.com/'.$server.'/'.$photo['id'].'_'.$secret.'_s.jpg';
                        $photo['thumbnail'] = 'http://static.flickr.com/'.$server.'/'.$photo['id'].'_'.$secret.'_t.jpg';
                        $photo['small'] = 'http://static.flickr.com/'.$server.'/'.$photo['id'].'_'.$secret.'_m.jpg';
                        $photo['medium'] = 'http://static.flickr.com/'.$server.'/'.$photo['id'].'_'.$secret.'.jpg';
                        $photo['url'] = 'http://www.flickr.com/photos/'.$photo['owner']['NSID'].'/'.$photo['id'].'/';
                        $cachePhoto->save(serialize($photo));
                    } else {
                        include_once 'inc/database.inc.php';
                        $GLOBALS['db']->query("DELETE FROM favorites WHERE photo_id='".$photoId."'");
                        $GLOBALS['db']->query("DELETE FROM ignored WHERE photo_id='".$photoId."'");
                        $GLOBALS['db']->query("DELETE FROM photos WHERE photo_id='".$photoId."'");
                        $photo = 'private';
                    }
                } else {
                  $errorCode = $flickr->getErrorCode();
                  $errorMessage = $flickr->getErrorMessage();
                  if ($errorCode == 1) {
                    include_once 'inc/database.inc.php';
                    $GLOBALS['db']->query("DELETE FROM favorites WHERE photo_id='".$photoId."'");
                    $GLOBALS['db']->query("DELETE FROM ignored WHERE photo_id='".$photoId."'");
                    $GLOBALS['db']->query("DELETE FROM photos WHERE photo_id='".$photoId."'");
                    $photo = 'removed';
                  } else {
                    $photo = 'API Error : '.$errorMessage.'(code '.$errorCode.')';
                  }
                }
            } else {
              $errorCode = $flickr->getErrorCode();
              $errorMessage = $flickr->getErrorMessage();
              if ($errorCode == 1) {
                include_once 'inc/database.inc.php';
                $GLOBALS['db']->query("DELETE FROM favorites WHERE photo_id='".$photoId."'");
                $GLOBALS['db']->query("DELETE FROM ignored WHERE photo_id='".$photoId."'");
                $GLOBALS['db']->query("DELETE FROM photos WHERE photo_id='".$photoId."'");
                $photo = 'removed';
              } else {
                $photo = 'API Error : '.$errorMessage.'(code '.$errorCode.')';
              }
            }
        }
    }

    return $photo;
}

function getPhotoHTML($photo_id, $nb = 0, $size = 'small')
{
    $photo = getPhoto($photo_id);
    if (!is_array($photo)) {
        if ($photo == 'removed') {
            $str = '<li><img src="/img/photo_gone.gif" width="75" height="75" alt="Removed" /></li>';
        } else {
            $str = '<li><img src="/img/photo_error.gif" width="75" height="75" alt="Error" title="'.$photo.'" /></li>';
        }
    } else {
        $str = '<li>';
        $str .= '<a href="'.$photo['url'].'" title="'.$photo['title'].', by '.$photo['owner']['username'].'"><img src="'.$photo['small'].'" /></a>';
        if ($nb != 0) {
          $str .= '<p>Faved by <strong>'.$nb.'</strong> neighbours';
          $str .= ' - <a href="/ignore.php?photo_id='.$photo_id.'" class="ignore">ignore</a>';
//          $str .= ' - <a href="/favor.php?photo_id='.$photo_id.'" class="favor">favor</a></p>';
        }
        $str .= '</li>';
    }
    return $str;
}
?>