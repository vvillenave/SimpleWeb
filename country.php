<?php
// Poor man's geolocation.
$loc = 'US';

use GeoIp2\Database\Reader;
$db = new Reader('/var/www/moodledata/geoip/GeoLite2-City.mmdb');

function getIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
$entry = $db->city(getIP());
$loc = $entry->country->isoCode;
$country = $entry->country->name;
