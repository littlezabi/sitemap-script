<?php
define('HOST', 'localhost');
define('USER', 'root');
define('PASSWORD', '');
define('DBNAME', '');
function prt($str)
{
    echo '<br />';
    echo '<pre>';
    print_r($str);
    echo '</pre>';
    echo '<br />';
}
$ROOT_URL = 'https://www.truehomes24.com/';
$SITEMAPS_FOLDER = 'sitemaps';
$ROOT_PATH = __DIR__ . '/';
$ROOT_SITEMAP_URL = $ROOT_URL . $SITEMAPS_FOLDER . '/';

$STATIC_URLS = [
    'https://www.truehomes24.com/property/sale',
    'https://www.truehomes24.com/property/rent',
    'https://www.truehomes24.com/agent/real-estate-agents-in-india',
];

$PROIRITY = '0.64';
$DATE = date('c');
$WRITING_LIMITS = 35000;
$indexSitemapsList = [];
$tables = [
    'property' => 'SELECT `property`.`ID`, `property`.`ProjectName`, `project_society_list`.`ProjectSocietyName`,`city_master`.`CityName` FROM `property`,`project_society_list`, `city_master`
        WHERE `property`.`ProjectName` = `project_society_list`.`ID` AND `property`.`City` = `city_master`.`ID` ',
    'city_master' => "SELECT `CityName` FROM `city_master`",
    'users' => "SELECT ID, Name FROM users WHERE 1"
];

$urls_grabbing_methods = [
    'property' => 'CityName',
    'city_master' => 'CityName',
    'users' => 'Name'
];
function filter($text)
{
    $t = str_replace(' ', '-', $text);
    $t = str_replace('&nbsp;', '-', $t);
    $t = strtolower($t);
    $t = htmlentities($t);
    return $t;
}
function handleURLsMethod($data, $grabbing_method)
{
    global $ROOT_URL, $urls_grabbing_methods;

    $url = '';
    if ($grabbing_method == 'property') {
        $url = 'property/' . $data['ProjectSocietyName'] . '-in-' . $data['CityName'] . '/1000-' . $data['ID'];
    }
    if ($grabbing_method == 'city_master') {
        $url = 'properties-for-sale-in-' . $data['CityName'];
    }
    if ($grabbing_method == 'users') {
        $url = 'user/detail/' . $data['ID'] . '/' . $data['Name'];
    }
    $url = filter($url);
    return htmlentities($ROOT_URL . $url);
}
