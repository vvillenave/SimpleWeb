# SimpleWeb

A simplified web search, heavily based on [FrogFind](https://github.com/ActionRetro/FrogFind)
and [68knews](https://github.com/ActionRetro/68k-news).  

## Dependencies

This has three PHP dependencies, which you can install (in the `vendor/` subdir)  
by running `composer install`:  
* [readability.php](https://github.com/andreskrey/readability.php)
* [simplepie](https://simplepie.org/)
* [geoip2](https://github.com/maxmind/GeoIP2-php).

## Localization

Very basic localization support is available in the `locales/` subdirectory.  
Just copy one of the files there and translate the given messages.

## TODO

* Caching. Lots, lots of caching.
* Another nice thing to have would be, on simplified article pages, a  
“*share this page*” button that would save the URL and output a shortened  
URL (possibly involving yourls at some point).
* Contribute upstream to readability.php and readibility.js.

