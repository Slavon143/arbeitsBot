<?php

require __DIR__ . '/vendor/autoload.php';

use src\ArbeitsTelegramBot;
use src\ActionHandler;

$bot = new ArbeitsTelegramBot();
$bot->listen();


//$actionHandler = new ActionHandler('history.json');
////$actionHandler->addToHistory(['type' => 'showOneTranslate', 'translate_id' => 123454512345]);
////$actionHandler->addToHistory(['type' => 'showRegion']);
//
//
//$previousAction = $actionHandler->getPreviousAction();
////
//var_dump($previousAction);