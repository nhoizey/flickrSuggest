<?php
require_once 'inc/init.inc.php';
require_once 'inc/layout_page_top.inc.php';
require_once 'inc/photo.inc.php';
require_once 'inc/favorites.inc.php';
require_once 'Flickr/API.php';

?>
<h2>flickrSuggest</h2>
<?php
$myFavs = implode(', ', $db->getCol("SELECT photo_id FROM favorites WHERE user_nsid = '".FLICKR_USER_NSID."'"));
if ($myFavs == '') {
  ?>
  <p>You need to go to the <a href="/update.php">update page</a> to start retrieving your favorites.</p>
  <?php
  require_once 'inc/layout_page_bottom.inc.php';
  exit(0);
}
$total = $db->getOne("SELECT COUNT(DISTINCT photo_id) FROM favorites WHERE photo_id NOT IN (".$myFavs.")");
$nbNeighbours = $db->getOne("SELECT COUNT(user_nsid) FROM users WHERE ignored=0") - 1;
$nbIgnoredUsers = $db->getOne("SELECT COUNT(user_nsid) FROM users WHERE ignored=1");
?>
<p>These suggestions are based on favorites from <?php echo $nbNeighbours; ?> users that share at least <?php echo NEIGHBOURHOOD_DISTANCE; ?> favorites with me.
<?php echo $nbIgnoredUsers; ?> users are ignored because they have at least <?php echo IGNORED_DISTANCE; ?> favorites that I ignored.</p>
<h3>Favorites suggestions</h3>
<?php
$favoritesSuggestions = $db->getAll("SELECT DISTINCT photo_id, nb FROM favorites WHERE photo_id NOT IN (".$myFavs.") ORDER BY nb DESC, photo_id LIMIT 0,3");
if ($total > 0) {
  echo '<ol class="gallery">';
  foreach($favoritesSuggestions as $data) {
  	echo getPhotoHTML($data['photo_id'], $data['nb']);
  }
  echo '</ol>';
}
?>
<h3>Contacts suggestions</h3>
<p>These contacts suggestions are users from which you have favorited the most photos, with a minimum of <?php echo CONTACTS_SUGGESTIONS_TRIGGER; ?>.</p>
<?php
$flickr = new Flickr_API(array('api_key' => FLICKR_APIKEY));
$contactsListResponse = $flickr->callMethod('flickr.contacts.getPublicList', array('email' => FLICKR_ACCOUNT_EMAIL, 'password' => FLICKR_ACCOUNT_PASSWORD, 'user_id' => FLICKR_USER_NSID, 'per_page' => 1000, 'page' => 1));
$contactsString = '';
if ($contactsListResponse && $contactsListResponse->attributes['stat'] == 'ok') {
  $contactsList = $contactsListResponse->getNodeAt('contacts');
  foreach ($contactsList->children as $contact) {
    if (isset($contact->attributes['nsid'])) {
      $contactsString .= ', "'.$contact->attributes['nsid'].'"';
    }
  }
  $contactsString = substr($contactsString, 2);
}

$contactsSuggestionsQuery = "SELECT DISTINCT photographer_nsid, COUNT(photo_id) AS nb FROM favorites WHERE user_nsid = '".FLICKR_USER_NSID."' AND photographer_nsid NOT IN (".$contactsString.") GROUP BY photographer_nsid HAVING nb >= ".CONTACTS_SUGGESTIONS_TRIGGER." ORDER BY nb DESC, photographer_nsid LIMIT 0,10";
$contactsSuggestions = $db->getAll($contactsSuggestionsQuery);
echo '<ol>';
foreach($contactsSuggestions as $contactNsid) {
  $contactInfoResponse = $flickr->callMethod('flickr.people.getInfo', array('email' => FLICKR_ACCOUNT_EMAIL, 'password' => FLICKR_ACCOUNT_PASSWORD, 'user_id' => $contactNsid['photographer_nsid']));
  if ($contactInfoResponse && $contactInfoResponse->attributes['stat'] == 'ok') {
    $contactInfo = $contactInfoResponse->getNodeAt('person');
    if ($contactInfo->attributes['iconserver'] > 0) {
      $icon = 'http://farm'.$contactInfo->attributes['iconfarm'].'.static.flickr.com/'.$contactInfo->attributes['iconserver'].'/buddyicons/'.$contactNsid['photographer_nsid'].'.jpg';
    } else {
      $icon = 'http://www.flickr.com/images/buddyicon.jpg';
    }
    $contactString = '<li><img src="'.$icon.'" /> <a href="http://flickr.com/photos/'.$contactNsid['photographer_nsid'].'/">';
    $contactUsername = $contactInfo->getNodeAt('username')->get();
    if ($contactRealName = $contactInfo->getNodeAt('realname')) {
      $contactRealName = $contactRealName->get();
      $contactString .= $contactRealName.($contactRealName != $contactUsername ? ' ('.$contactUsername.')' : '');
    } else {
      $contactString .= $contactUsername;
    }
    $contactString .= '</a>: '.$contactNsid['nb'].' favorites)</li>';
    echo $contactString;
  }
}
echo '</ol>';
?>
<?php
require_once 'inc/layout_page_bottom.inc.php';
?>