<?php

//site settings
$siteName = 'Red Goddess Oracle';
$siteEmail = 'redgoddessoracle@gmail.com';

//Database config
define('DB_HOST', "localhost");
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'Sigfried3!');
define('DB_NAME', 'redgoddesssubscribers');

$siteURL = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")?'https://':'http://';
$siteURL = $siteURL.$_SERVER["SERVER_NAME"].dirname($_SERVER['REQUEST_URI']).'/';