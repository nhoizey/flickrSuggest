# flickrSuggest

## About

flickrSuggest is an attempt to help Flickr users find photos they should like, based on their current favorites.

It lists photos that are the most favorited by users who have at least one favorite in common with the user. The user has the opportunity to ignore any photo he want neither to favorite nor to see anymore.

Unfortunately, I can't host such a heavy computing system, so this is only available as a free software you can download and install for yourself.

The development is [hosted on GitHub](http://github.com/nhoizey/flickrSuggest), so you can fork it and help me enhance it at will!

You can add this to your favorite Apps in Flickr's [App Garden](http://www.flickr.com/services/apps/72157623634339128/), and discuss it in it's own [Flickr Group](http://www.flickr.com/groups/flickrsuggest/).

## Requisites

flickrSuggest runs on PHP and MySQL, with the great help of some PEAR components:

* Cache_Lite
* DB
* [Flickr_API](http://code.iamcal.com/php/flickr/readme.htm)
* XML_Tree

The tables must be created in the database with the "flickrsuggest.sql" script.

After install, you need to copy "inc/config-sample.inc.php" to "inc/config.inc.php" and edit it with your info.

You may need to adjust the "Resource Limits" section of your php.ini PHP configuration file:

    max_execution_time = 600
    memory_limit = 128M


## Credits

flickrSuggest has been created by [Nicolas Hoizey](http://www.gasteroprod.com/), which is also (of course) a [Flickr user](http://www.flickr.com/photos/nicolas-hoizey/).

The design is mainly based on the [Blueprint CSS Framework](http://blueprintcss.org/) and uses the free [GraublauWeb](http://www.fonts.info/info/press/font-face-embedding-demo.htm) font for the logo and titles.
