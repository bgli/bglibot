<?php

require 'inc/token.php';
require 'inc/telebot.php';
  
$telebot = new Telebot(TOKEN, false);
$webhook=$telebot->setWebhook('');
echo isset($webhook['result'])?$webhook['description']:'Error!';

