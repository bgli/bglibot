<?php

require 'token.php';
require 'roles.php';
require 'telebot.php';

$bot = new Telebot(TOKEN);

while (true)
{
	sleep(2);
 	$update_id  = 0; //mula-mula tepatkan nilai offset pada nol
         
	//cek file apakah terdapat file "last_update_id"
	if (file_exists("last_update_id")) 
	{
	       //jika ada, maka baca offset tersebut dari file "last_update_id"
	       $update_id = (int)file_get_contents("last_update_id");
	}

	$updates = $bot->getUpdates($update_id);

	foreach ($updates as $message)
        {
		$update_id = $message["update_id"];;
		$message_data = $message["message"];
	                    
		if (isset($message_data["text"]))
	        {
				
			$bot->chat_id = $message_data["chat"]["id"];
			$bot->reply_id = $message_data["message_id"];
	                            
			$text = $message_data["text"];
			$botMsg= $bot->getBotMessage($text,2);
	
			if ($botMsg != false)
			{ 
				switch ($botMsg->command)
				{
					case '/narsum':

						if (count($botMsg->param) > 0) {
							$bot->text = "Set <b>Narasumber</b> kepada: {$botMsg->text}";
							file_put_contents('narasumber', $botMsg->text);
						} else {
							$narsum = file_get_contents('narasumber');
							$bot->text = "<b>Narasumber</b> kuliah: {$narsum}";
						}
						$bot->send('message');	
						break;
						
					default:
				}
				
			}


		}
	                    
	}
            
	file_put_contents("last_update_id", $update_id + 1);
} 

