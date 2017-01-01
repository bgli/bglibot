<?php

function call_getid($data,$bot)
{

	$result['error'] = true;
	$text = isset($data['text'])?$data['text']:'';
	
	if (!empty($text))
	{
		
		$botMsg= $bot->getBotMessage($text,1);	
		$options =  array ('parse_mode' => 'HTML','disable_web_page_preview'=> FALSE);
		
		
		if ($botMsg != false)
		{
			switch ($botMsg->command)
			{
				case '!id':
				case '/id':
				
					$from = $data['from'];
					
					if (!empty($bot->reply_to_message))
					{
						$from = $bot->reply_to_message['from'];	
					}
					
					$tele_id = $from['id'];
					$tele_username = isset($from['username'])?$from['username']:'';
					$tele_firstname = isset($from['first_name'])?$from['first_name']:'';
					
					$sendtext  = '<b>ID</b> : <code>'.$tele_id."</code> \n";
					$sendtext .= '<b>Username</b> : <a href="https://telegram.me/'.$tele_username. '">'.$tele_username."</a> \n";
					$sendtext .= '<b>Firstname</b> : <code>'. $tele_firstname. '</code>';
					
					$bot->text = $sendtext;
					$result['error'] = false;
					$result['result'] = $bot->send('message',$options);
					break;
				default:
					break;
			}
		}	
	}
	
	return (object)$result;
}