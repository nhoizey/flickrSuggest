<?php
// Your identification
define('FLICKR_USER_NSID', '');
define('FLICKR_ACCOUNT_EMAIL', 'someone@example.com');
define('FLICKR_ACCOUNT_PASSWORD', 'password');

// Your Flickr API key
define('FLICKR_APIKEY', '');

// Your auth token, for direct favoring
define('FLICKR_AUTH_TOKEN', '');

// DSN for the database
define('DSN', 'mysql://login:password@host/database');

// Lifetime of cache for some of the calls to the API
define('CACHE_LIFETIME_PHOTO', 60*60*24*7);

// Parameters for browsing the suggestions
define('BROWSE_MIN_NEIGHBOURS', 20);
define('BROWSE_PER_PAGE', 15);

// Do you want to show technical errors?
define('SHOW_DEBUG', false);
?>