<?php

namespace src;

use Telegram\Bot\Api;
use Symfony\Component\Dotenv\Dotenv;
class ArbeitsTelegramBot
{
    protected $token;
    protected $incomingRequest;
    protected $bot;
    public function __construct()
    {
        $env = new Dotenv();
        $env->load(__DIR__. '/../.env');
        $this->token = $_ENV['TELEGRAM_BOT_TOKEN'];

        $this->incomingRequest =  json_decode(file_get_contents('php://input'), true);

        $this->bot = new Api($this->token);

    }

    public function listen(){

    }

}