<?php
function call_rules($bot)
{
    $result['error'] = true;
    $data = $bot->message;
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
		$options =  array ('parse_mode' => 'HTML','disable_web_page_preview'=> TRUE);
		
		if ($botMsg != false)
		{
			switch ($botMsg->command)
			{
				case '!rules':
				case '/rules':
                    
                    $db = $bot->db;
                    
                    $group_id = $data['chat']['id'];
                    $user_id  = $data['from']['id'];
                    
                    $getRules = $db->GetRules($group_id);	
                    if (empty($getRules))
                    {
						$getRules = 'The rules have not been written on this group';
					}
					
                    if (count($botMsg->param) > 0)
                    {
						$userInfo = $db->GetUserInfoFromGroup($user_id,$group_id);
						$param = $botMsg->param[1];
						
						if ($param == 'set'){
							
							if ($userInfo == 'SU'){
								
								$editing_rules = false;
								if (!empty($getRules)){
									$editing_rules = true;
								}
								
								$the_rules = $db->SetRules($user_id,$group_id,$botMsg->text,$editing_rules);
								if ($the_rules == TRUE){
									if ($editing_rules == false)
									{
										$msg = 'The rules have been written';
									} else {
										$msg = 'The rules have been updated';
									}
									
											
								} else {
									$msg = $the_rules;
								}
								
								
							} else {
								$msg = 'Access denied. Only Super User can do that.';
							}
							$bot->text = $msg;
						}
						
					}
                    else
                    { 
						$bot->text = $getRules;
					}
					
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
