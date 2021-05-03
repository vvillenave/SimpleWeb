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

require_once('vendor/autoload.php');
include('i18n.php');

use andreskrey\Readability\Readability;
use andreskrey\Readability\Configuration;
use andreskrey\Readability\ParseException;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="./styles.css">
  <link rel="icon" href="https://www.hypra.fr/wp-content/uploads/2020/12/LOGO_RS-150x150.png" sizes="32x32" />
<?php
// If there’s an 'a' argument, print the given article
if( isset( $_GET['a'] ) && substr( $_GET['a'], 0, 4 ) == "http") {
    $article_url = $_GET["a"];
    $host = parse_url($article_url, PHP_URL_HOST);
    $error_text = "";

    $configuration = new Configuration();
    $configuration
        ->setArticleByLine(false)
        ->setFixRelativeURLs(true)
        ->setOriginalURL('http://' . $host);

    $readability = new Readability($configuration);

    if(!$article_html = file_get_contents($article_url)) {
        $error_text .=  "Failed to get the article :( <br>";
    }

    try {
        $readability->parse($article_html);
        $readable_article = strip_tags($readability->getContent(), '<a><ol><ul><li><br><p><small><font><b><strong><i><em><blockquote><h1><h2><h3><h4><h5><h6>');
        $readable_article = str_replace( 'href="http', 'href="./?a=http', $readable_article ); //route links through proxy

    } catch (ParseException $e) {
        $error_text .= 'Sorry! ' . $e->getMessage() . '<br>';
    }
?>
  <title><?php echo $readability->getTitle() . ' (' . __('sp_title') . ')';?></title>
  <meta name='original-source' content='<?php echo $article_url; ?>'>
</head>
<body class="article">
<header>
  <h1><?php echo $readability->getTitle();?></h1>
  <p>(<a href='<?php echo $article_url; ?>'><?=__('sp_source')?></a>)
</header>
<?php
    $img_num = 0;
    $imgline_html = __('sp_images_view');
    foreach ($readability->getImages() as $image_url):
        $imgline_html .= " <a href='" . $image_url . "'>[$img_num]</a> ";
    endforeach;
    if($img_num>0) {
        echo '<section>' . $imgline_html . '</section>';
    }
?>
<article>
  <?php if($error_text) { echo "<p class='error'>" . $error_text . "</p>"; } ?>
  <p><?php echo $readable_article;?></p>
</article>
<footer>
  <p><?=__('sp_disclaimer')?></p>
  <p><a href="./"><?=__('sp_back_link')?></a></p>
</footer>
 </body>
 </html>
<?php exit(); } ?>
  <title><?=__('sw_title')?></title>
  <meta name="description" content="<?=__('sw_slogan')?>">
</head>
<?php
// if there's a search query, show the results for it
if(isset( $_GET['q'])) {
    $results_html = "";
    $final_result_html = "<hr>";
    $query = urlencode($_GET["q"]);
    $search_url = "https://html.duckduckgo.com/html?q=" . $query;
    if(!$results_html = file_get_contents($search_url)) {
        $error_text .=  __("Failed to get results, sorry :( <br>");
    }
    $simple_results=$results_html;

    $result_blocks = explode('<h2 class="result__title">', $simple_results);
    $total_results = count($result_blocks)-1;

    for ($x = 1; $x <= $total_results; $x++) {
        if(strpos($result_blocks[$x], '<a class="badge--ad">')===false) { //only return non ads
            // result link, redirected through our proxy
            $result_link = explode('class="result__a" href="', $result_blocks[$x])[1];
            $result_topline = explode('">', $result_link);
            $result_link = str_replace( '//duckduckgo.com/l/?uddg=', './?a=', $result_topline[0]);
            // result title
            $result_title = str_replace("</a>","",explode("\n", $result_topline[1]));
            // result display url
            $result_display_url = explode('class="result__url"', $result_blocks[$x])[1];
            $result_display_url = trim(explode("\n", $result_display_url)[1]);
            // result snippet
            $result_snippet = explode('class="result__snippet"', $result_blocks[$x])[1];
            $result_snippet = explode('">', $result_snippet)[1];
            $result_snippet = explode('</a>', $result_snippet)[0];

            $final_result_html .= "<a class='result' href='" . $result_link . "'><h2>" . $result_title[0] . "</h2>"
                               . "<small>" . $result_display_url . "</small>"
                               . "<p>" . $result_snippet . "</p>"
                               . "</a>";
        }
    }
?>
<body class="results">
<header>
  <form action="./" method="get">
  <a href="./"><?=__('sw_title_fancy')?></a>
  <?=__('sw_new_search')?>
  <input autofocus type="text" size="30" name="q" value="<?php echo urldecode($query) ?>">
  <input type="submit" value='<?=__('sw_submit')?>'>
  </form>
</header>
<article>
  <h1><?=__('sw_results') . ' <em>' . strip_tags(urldecode($query))?></em></h1>
  <?=$final_result_html?>
</article>
<footer>
  <p><?=__('sp_disclaimer')?></p>
  <p><a href="./"><?=__('sp_back_link')?></a></p>
</footer>
</body></html>
<?php exit(); }
// If no `q' or `a' argument was passed, then show the standard home page.
?>
<body class="home">
<header>
  <h1><?=__('sw_title_fancy')?></h1>
  <p><?=__('sw_slogan')?></p>
</header>
<section>
  <p class="explainer"><?=__('sw_explainer')?></p>
  <form action="./" method="get">
    <input autofocus type="text" size="30" name="q">
    <input type="submit" value='<?=__('sw_submit')?>'>
  </form>
</section>
<section>
  <p class="explainer"><?=__('sw_article')?></p>
  <form action="./" method="get">
    <input type="text" size="30" name="a">
    <input type="submit" value='<?=__('sw_submit')?>'>
  </form>
</section>
<section>
  <p class="explainer"><?=__('sw_more')?></p>
  <ul>
    <li><a href=<?php echo '"./news/?lang=' . $lang . '"'?>><?=__('sn_title')?></a>, <?=__('sn_slogan')?>
    <li><a href="http://localisateur.org/">Le Localisateur</a>, <?=__('localisateur_slogan')?>
  </ul>
</section>
<footer>
  <p><?=__('sw_disclaimer_long')?></p>
</footer>
</body>
</html>
