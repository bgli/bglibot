<?php


function call_narsum($bot)
{
    $result['error'] = true;
    $data = $bot->message;
	$text = isset($data['text'])?$data['text']:'';
	$options['parse_mode'] = 'Markdown'; //using markdown format
	
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
								 $str = "*Cara Menggunakan Perintah*\n\n";
								 $str .= "/narsum\n";
								 $str .= "Menampilkan nama narasumber saat ini\n\n";
								 $str .= "/narsum *help*\n";
								 $str .= "Menampilkan cara menggunakan perintah `/narsum`.\n\n";
								 $str .= "/narsum *set* _nama narsumber_ (Admin Only)\n";
								 $str .= "Mengeset nama narasumber.\n\n";
								 $str .= "/narsum *del* (Admin Only)\n";
								 $str .= "Menghapus nama narasumber saat ini";
								 $bot->text = $str;
							}
							else if ($param == 'set')
							{
								$bot->text = "Set *Narasumber* kepada: {$botMsg->text}";
								file_put_contents('temp/narasumber', $botMsg->text);

							}
							
						}
						else
						{
							if (file_exists('temp/narasumber'))
							{
								$narsum = file_get_contents('temp/narasumber');
								$str = "*Narasumber* kuliah: {$narsum}\n\n";
								$str .= "Informasi penggunaan `/narsum help`";
								$bot->text = $str;
							}
							else
							{
								$bot->text = "Tidak ada narasumber\n\nInformasi penggunaan `/narsum help`";
							}
						}
                        $result['error'] = false;
						$result['result'] = $bot->send('message',$options);
						break;
						
					case '/materi':
					
						if (count($botMsg->param) > 0)
						{
							$param = $botMsg->param[1];

							if ($param =='help')
							{
								$bot->text = "Cara Menggunakan";
							}
							else if ($param == 'set')
							{
								$bot->text = "Set *Materi*: {$botMsg->text}";
								file_put_contents('temp/materi', $botMsg->text);

							}
							
						}
						else
						{
							if (file_exists('temp/materi'))
							{
								$narsum = file_get_contents('temp/materi');
								$bot->text = "Materi kuliah: {$narsum}";
							}
							else
							{
								$bot->text = "Tidak ada materi kuliah saat ini";
							}
						}
                        $result['error'] = false;
						$result['result'] = $bot->send('message',$options);						
						break;
					case '/mulai':
						break;
					case '/selesai':
						break;
					case '/batal': //batal kuliah
						unlink('temp/materi');
						unlink('temp/narasumber');
						$bot->text = "Kuliah dibatalkan semua narasumber dan materi dihapus";
						$result['result'] = $bot->send('message');
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
