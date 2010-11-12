<?php
// Your Flickr identification
define('FLICKR_USER_NSID', '');
define('FLICKR_ACCOUNT_EMAIL', 'someone@example.com');
define('FLICKR_ACCOUNT_PASSWORD', 'password');

// Your Flickr API key
define('FLICKR_APIKEY', '');

// DSN for the database
define('DSN', 'mysql://login:password@host/database');

// Lifetime of cache for some of the calls to the API
define('CACHE_LIFETIME_PHOTO', 60*60*24*7);

// Favorites from people with less than NEIGHBOURHOOD_DISTANCE favorites common with mine will not be taken into account
define('NEIGHBOURHOOD_DISTANCE', 10); // Number of common favorites

// People with more than IGNORED_DISTANCE ignored photos will not be taken into account (NEIGHBOURHOOD_DISTANCE is considered first)
define('IGNORED_DISTANCE', 20); // Number of ignored photos

// Contacts suggestions are limited to users from which you have favorited at least CONTACTS_SUGGESTIONS_TRIGGER photos.
define('CONTACTS_SUGGESTIONS_TRIGGER', 5);

// Parameters for browsing the suggestions
define('BROWSE_MIN_NEIGHBOURS', 10);
define('BROWSE_PER_PAGE', 18);

// Do you want to show technical errors?
define('SHOW_DEBUG', false);
?>