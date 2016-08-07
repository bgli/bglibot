<?php

function call_welcome($data,$bot){
	
	 $result['error'] = true;
	 	 
	 //cek member yg join, left, dan pin dari group
	 $join_group = empty($data['new_chat_participant'])?false:true;
	 $left_group = empty($data['left_chat_participant'])?false:true;
	 $pinned_group = empty($data['pinned_message'])?false:true;
			
	 
	 if ($join_group)
	 {
		 $result['error'] = false;
		 $options =  array ('parse_mode' => 'Markdown');
		 		 
		 $first_name = $data['from']['first_name'];
		 $username = $data['from']['username'];
		 $id = $data['from']['id'];
		 		 
		 $bot->text = "Selamat Datang *{$first_name}* ({$username} - {$id})";
		 $result['result'] = $bot->send('message',$options);
	
	 }
	
	return (object)$result;
 }


