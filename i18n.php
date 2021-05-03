<?php
// Poor man's localization.
$lang = 'fr';
$languages = array_map(
  function($str) { return basename($str, '.ini'); },
  glob('./locales/*.ini')
);

if (isset($_GET['lang']) && in_array($_GET['lang'], $languages)) {
    $lang = $_GET['lang'];
    setcookie('lang', $lang);
} elseif (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $languages)) {
    $lang = $_COOKIE['lang'];
} elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    array_walk($langs, function (&$lang) {
     $lang = strtr(strtok($lang, ';'), ['-' => '_']);
 });
    foreach ($langs as $browser_lang) {
        if (in_array($browser_lang, $languages)) {
            $lang = $browser_lang;
            break;
        }
    }
}

$trans = parse_ini_file('locales/'.$lang.'.ini');

function __($item) {
    global $trans;
    if (array_key_exists($item, $trans)) {
        return $trans[$item];
    } else {
        return 'Untranslated string: '.$item;
    }
}

