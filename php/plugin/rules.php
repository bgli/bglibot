<?php
function call_rules($data,$bot)
{
    echo "Calling Narsum Plugin\n";

    $result['error'] = true;
	$text = isset($data['text'])?$data['text']:'';

    $rules = <<<EOF
    *Belajar GNU/Linux Indonesia*

Membantu pengguna pemula *GNU/Linux* untuk memahami sistem operasi GNU/Linux dengan prinsip kejujuran, gotong royong dan persaudaraan.

*Aturan Grup*
1. Menggunakan bahasa yang baik, benar, sopan dan santun.
2. Menghargai setiap pertanyaan saudara kita, sesederhana apa pun pertanyaan itu.


_Gunakan Fitur Hashtag untuk pencarian_
#bookmark #tandai
#kuliah
EOF;

	if (!empty($text))
	{

		$botMsg= $bot->getBotMessage($text,2);

		if ($botMsg != false)
		{
			switch ($botMsg->command)
			{

				case '/rules':
                    $options =  array ('parse_mode' => 'Markdown');
                    $bot->text = $rules;
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
