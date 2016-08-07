<?php

function call_narsum($data,$bot)
{

    $result['error'] = true;
	$text = isset($data['text'])?$data['text']:'';
	 
	if (!empty($text))
	{
				
					
			/* Dapatkan tiga kriteria perpesanan dari bot
			 *  Kriteria satu:
			 *    /narsum, mendapatkan nilai default dari nama narasumber
			 *  Kriteria dua:
			 *    /narsum help, menyajikan nilai balik dari (callback) cara penggunaan
			 *  Kriteria ketiga:
			 *    /narsum set nama_narsum, mengeset nama narasumber
			 * 
			 *  Di sini kita menggunakan function getBotMessage dengan nilai MaxOfCMD = 2
			 *  artinya Anda diberikan 2 buah paramenter.
			 * 
			 *  /narsum set nama_narsum
			 *    1      2    3
			 * 
			 * 1. Nama perintah
			 * 2. Paramenter pertama
			 * 3. Paramenter kedua 
			 * 
			 */ 		
			 
		$botMsg= $bot->getBotMessage($text,2);	
	
			if ($botMsg != false)
			{ 
				switch ($botMsg->command)
				{
					case '/narsum':

						if (count($botMsg->param) > 0)
						{
							$param = $botMsg->param[1];
							
							if ($param =='help')
							{
								$bot->text = "Cara Menggunakan";
							}
							else if ($param == 'set')
							{
								$bot->text = "Set <b>Narasumber</b> kepada: {$botMsg->text}";
								file_put_contents('narasumber', $botMsg->text);

							}
							//else if ($param == 'e')
						}
						else
						{
							if (file_exists('narasumber'))
							{
								$narsum = file_get_contents('narasumber');
								$bot->text = "<b>Narasumber</b> kuliah: {$narsum}";
							}
							else
							{
								$bot->text = "Tidak ada narasumber";
							}	
						}
						$result['result'] = $bot->send('message');	
						break;
					
					case '/mulai':
						break;
					case '/selesai':
						break;
					case '/rules':
						break;
					case '/help':
						break;
						
					default:
				}	
			}
	}
	
	return (object)$result;
}
