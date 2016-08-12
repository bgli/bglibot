<?php

require 'core.php';
date_default_timezone_set('UTC');

while (true)
{
	sleep(2);
 	$update_id  = 0; //mula-mula tepatkan nilai offset pada nol

	//cek file apakah terdapat file "last_update_id"
	if (file_exists("temp/last_update_id"))
	{
	       //jika ada, maka baca offset tersebut dari file "last_update_id"
	       $update_id = (int)file_get_contents("temp/last_update_id");
	}

	$updates = $bot->getUpdates($update_id);

	foreach ($updates as $message)
    {
		$update_id = $message['update_id'];
		$message_data = $message['message'];

		$bot->chat_id = isset($message_data['chat']['id'])?$message_data['chat']['id']:'';
		$bot->reply_id = isset($message_data['message_id'])?$message_data['message_id']:'';

	   // Membaca seluruh plugin dan function nya
			for ($i=0; $i < count($plugin_name) ; $i++)
	        {
				$plugin_function = 'call_' . $plugin_name[$i];
				if (function_exists($plugin_function))
				{
					$pResult = call_user_func_array($plugin_function,array($message_data, $bot));
					/*if ($pResult->error == false)
					{
						//untuk debuging, pengecekan apakah bot sudah benar-benar terkirim
						print_r($pResult->result);
					}
					*/
				}
			}

	}

	file_put_contents("last_update_id", $update_id + 1);
}
