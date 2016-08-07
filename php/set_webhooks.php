<?php

require 'lib/token.php';
require 'lib/telebot.php';

$telebot = new Telebot(TOKEN, false);
$webhook=$telebot->setWebhook('');
echo isset($webhook['result'])?$webhook['description']:'Error!';
