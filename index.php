<?php

require 'autoload.php';
require 'config.php';

use Medoo\Medoo;

use DeadComposers\API;
use DeadComposers\Database;
use DeadComposers\Wikidata\Constants\Country;
use DeadComposers\Wikidata\Constants\Occupation;

// Wikidata Query

$target_countries = [
    Country\CANADA,
    Country\BELGIUM,
    Country\SWITZERLAND,
    Country\GERMANY,
    Country\FRANCE
];

$occupations = [
    Occupation\COMPOSER
];

// Database Setup

$db = new Medoo([
    'charset' => 'utf8',
    'database_name' => MYSQL_DB_NAME,
    'database_type' => 'mysql',
    'password' => MYSQL_PASSWORD,
    'server' => MYSQL_SERVER,
    'username' => MYSQL_USERNAME
]);

// API Setup & Response

$api = new API($db, UPDATE_KEY, $target_countries, $occupations);

echo $api->handle_request(
    $_SERVER['REQUEST_METHOD'],
    $_REQUEST
);
