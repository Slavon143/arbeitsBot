<?php

require __DIR__ . '/vendor/autoload.php';

use src\ArbeitsTelegramBot;

$bot = new ArbeitsTelegramBot();
$bot->listen();

