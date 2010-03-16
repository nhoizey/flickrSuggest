<!DOCTYPE html>
<html>
<head>
<title>flickSuggest - by Nicolas Hoizey</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="index, follow" />
<meta name="author" content="Nicolas Hoizey" />
<!--[if IE]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" href="design/css/blueprint/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="design/css/blueprint/print.css" type="text/css" media="print">	
<!--[if lt IE 8]><link rel="stylesheet" href="design/css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->
<link rel="stylesheet" type="text/css" media="screen, projection" href="design/css/screen.css" title="flickrate" />
<link rel="stylesheet" type="text/css" media="print" href="design/css/print.css" />
<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
</head>

<body>
<div class="container">
  <header role="banner" class="span-16 column">
    <h1><a href="/">flickrSuggest</a></h1>
    <p class="beta">beta</p>
  </header>
  <div id="disclaimer" class="span-8 last">
    Disclaimer: this product uses the <a href="http://www.flickr.com/services/api/">Flickr API</a> but is not endorsed or certified by <a href="http://www.flickr.com/">Flickr</a>.
  </div>
  <nav role="navigation" class="span-24 last">
      <ul>
          <?php
          $menu = array(
              '/index.php' => 'Home',
              '/update.php' => 'Update',
              'http://www.flickr.com/groups/flickrsuggest/' => 'flickSuggest group',
              'http://flickrate.gasteroprod.com/' => 'Enhance Flickr favorites with flickRate'
              );
          foreach($menu as $url => $label) {
              if ($url == '-') {
                  echo '<li>'.$label.'</li>';
              } else {
                  if ($url == $_SERVER['SCRIPT_NAME']) {
                      echo '<li class="current"><a href="'.$url.'">'.$label.'</a></li>';
                  } else {
                      echo '<li><a href="'.$url.'">'.$label.'</a></li>';
                  }
              }
          }
          ?>
      </ul>
  </nav>
  <section role="main" id="content" class="span-24">
  