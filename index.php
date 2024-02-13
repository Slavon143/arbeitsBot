<?php

require __DIR__ . '/vendor/autoload.php';

use src\ApiArbetsformedlingen;
use src\ArbeitsTelegramBot;

$bot = new ArbeitsTelegramBot();
$bot->listen();
////




//function calculatePageCount($totalRecords, $recordsPerPage) {
//    return ceil($totalRecords / $recordsPerPage)*5;
//}
//
//
//var_dump(calculatePageCount(31,5));