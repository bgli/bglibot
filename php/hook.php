<?php

require 'core.php';

$up = $bot->getUpdates();
$msg = $up['message'];
$txt = $msg['text'];
$bot->chat_id = $msg['chat']['id'];
$bot->reply_id = $msg['message_id'];

	   // Membaca seluruh plugin dan function nya
			for ($i=0; $i < count($plugin_name) ; $i++)
	        {
				$plugin_function = 'call_' . $plugin_name[$i];
				if (function_exists($plugin_function))
				{
					$pResult = call_user_func_array($plugin_function,array($msg, $bot));
					/*if ($pResult->error == false)
					{
						//untuk debuging, pengecekan apakah bot sudah benar-benar terkirim
						print_r($pResult->result);
					}
					*/
				}
			}
