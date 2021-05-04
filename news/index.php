<?php
# ---------------------------------------------------------- #
#  SimpleWeb                                                 #
#  An easy web portal including web search, news and proxy.  #
#  2021, V. Villenave <vvillenave@hypra.fr>                  #
#  Based on work by `Action Retro' (frogfind.com, 68k.news)  #
#  and Andres K. Rey's PHP port of Mozilla's readability.js. #
# -----------------------------------------------------------#

# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.

require_once(__DIR__.'/../vendor/autoload.php');
include(__DIR__.'/../i18n.php');
include(__DIR__.'/../country.php');

$section="";
$feed_url="";

if(isset( $_GET['section'])) {
    $section = $_GET["section"];
}
if(isset( $_GET['loc'])) {
    $loc = strtoupper($_GET["loc"]);
}
if($section) {
	$feed_url="https://news.google.com/news/rss/headlines/section/topic/".strtoupper($section)."?ned=".$loc."&hl=".$lang;
} else {
	$feed_url="https://news.google.com/rss?gl=".$loc."&hl=".$lang."-".$loc."&ceid=".$loc.":".$lang;
}

//https://news.google.com/news/rss/headlines/section/topic/CATEGORYNAME?ned=in&hl=en
$feed = new SimplePie();
 
// Set the feed to process.
$feed->set_feed_url($feed_url);
 
// Run SimplePie.
$feed->init();
 
// This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
$feed->handle_content_type();

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="../styles.css">
  <link rel="icon" href="https://www.hypra.fr/wp-content/uploads/2020/12/LOGO_RS-150x150.png" sizes="32x32" />
  <title><?=__('sn_title')?></title>
  <meta name="description" content="<?=__('sn_slogan')?>">
</head>
<body class="news">
<header>
  <h1><?=__('sn_title_fancy')?></h1>
  <p><?=__('sn_slogan')?></p>
</header>
<nav>
  <ul>
    <li><a href="./?loc=<?php echo $loc ?>"><?=__('sn_home')?></a>
    <li><a href="./?section=world&loc=<?php echo strtoupper($loc) ?>"><?=__('sn_world')?></a>
    <li><a href="./?section=nation&loc=<?php echo strtoupper($loc) ?>"><?=$country?></a>
    <li><a href="./?section=business&loc=<?php echo strtoupper($loc) ?>"><?=__('sn_business')?></a>
    <li><a href="./?section=technology&loc=<?php echo strtoupper($loc) ?>"><?=__('sn_tech')?></a>
    <li><a href="./?section=entertainment&loc=<?php echo strtoupper($loc) ?>"><?=__('sn_entertainment')?></a>
    <li><a href="./?section=sports&loc=<?php echo strtoupper($loc) ?>"><?=__('sn_sports')?></a>
    <li><a href="./?section=science&loc=<?php echo strtoupper($loc) ?>"><?=__('sn_science')?></a>
    <li><a href="./?section=health&loc=<?php echo strtoupper($loc) ?>"><?=__('sn_health')?></a>
  </ul>
  <p><?php echo __('sn_edition').' '. $country ?>
    (<a href="#editions"><?=__('sn_change')?></a>)
  </p>
<?php
	if($section) {
		$section_title = explode(" - ", $feed->get_title());
		echo "<h2>" . $section_title[0]  . "</h2>";
	}
?>
</nav>
<article>
<?php
/*
Here, we'll loop through all of the items in the feed, and $item represents the current item in the loop.
*/
foreach ($feed->get_items() as $item):
?>
<a class='result' href="<?php echo '../?loc=' . $loc . '&a=' . $item->get_permalink(); ?>"><h3><?php
  echo $item->get_title();
?></h3></a><?php
$subheadlines = $item->get_description();
$remove_google_link = explode("<li><strong>", $subheadlines);
$no_blank = str_replace('target="_blank"', "", $remove_google_link[0]) . "</li></ol>"; 
$cleaned_links = str_replace('<a href="', '<a href="../?loc=' . $loc . '&a=', $no_blank);
$cleaned_links = strip_tags($cleaned_links, '<a><ol><ul><li><br><p><small><font><b><strong><i><em><blockquote><h1><h2><h3><h4><h5><h6>');
$cleaned_links = str_replace(__('sn_google_link'), "", $cleaned_links);
echo $cleaned_links;
?>
<small><?php echo __('sn_date') . ' ' . $item->get_date('j F Y | g:i a'); ?></small>
<?php endforeach; ?>
</article>
<section class='editions' id="editions">
<h2><?=__('sn_edition_choose')?></h2>
<ul>
  <li><a href='./?section=nation&loc=US'>United States</a>
  <li><a href='./?section=nation&loc=UK'>United Kingdom</a>
  <li><a href='./?section=nation&loc=FR'>France</a>
  <li>Spain (RIP)
  <li><a href='./?section=nation&loc=JP'>Japan</a>
  <li><a href='./?section=nation&loc=CA'>Canada</a>
  <li><a href='./?section=nation&loc=DE'>Deutschland</a>
  <li><a href='./?section=nation&loc=IT'>Italia</a>
  <li><a href='./?section=nation&loc=AU'>Australia</a>
  <li><a href='./?section=nation&loc=TW'>Taiwan</a>
  <li><a href='./?section=nation&loc=NL'>Nederland</a>
  <li><a href='./?section=nation&loc=BR'>Brasil</a>
  <li><a href='./?section=nation&loc=TR'>Turkey</a>
  <li><a href='./?section=nation&loc=BE'>Belgium</a>
  <li><a href='./?section=nation&loc=GR'>Greece</a>
  <li><a href='./?section=nation&loc=IN'>India</a>
  <li><a href='./?section=nation&loc=MX'>Mexico</a>
  <li><a href='./?section=nation&loc=DK'>Denmark</a>
  <li><a href='./?section=nation&loc=AR'>Argentina</a>
  <li><a href='./?section=nation&loc=CH'>Switzerland</a>
  <li><a href='./?section=nation&loc=CL'>Chile</a>
  <li><a href='./?section=nation&loc=AT'>Austria</a>
  <li><a href='./?section=nation&loc=KR'>Korea</a>
  <li><a href='./?section=nation&loc=IE'>Ireland</a>
  <li><a href='./?section=nation&loc=CO'>Colombia</a>
  <li><a href='./?section=nation&loc=PL'>Poland</a>
  <li><a href='./?section=nation&loc=PT'>Portugal</a>
  <li><a href='./?section=nation&loc=PK'>Pakistan</a>
</ul>
</section>
<footer>
  <p><?=__('sn_disclaimer')?>
  <p><a href="../"><?=__('sn_back_link')?></a>
</footer>
</body>
</html>
