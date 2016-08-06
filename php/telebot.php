<?php

class Telebot
{
	private $token,$isLongPolling;
	public $error_msg = '';

	public $bot;
	public $chat_id;
	public $reply_id = 0;
	public $file_id = null;
	public $latitude, $longitude;
	public $disable_notification = false;

	
	public function __construct($token,$longpolling = true)
	{
		$this->token = $token;
		$this->isLongPolling = $longpolling;
		$getMe = $this->getMe();
		$this->bot = isset($getMe['id'])?(object)$getMe:'';
	}

	public function getMe()
	{
		$result = $this->curl_post('getMe');
		return isset($result['ok'])?$result['result']:false;
	}
	
	public function getUpdates($offset=0,$limit=100,$timeout=0)
	{
		if ($this->isLongPolling)
		{
			$data = array(
				'offset' => $offset,
				'limit' => $limit,
				'timeout' => $timeout
				);
			$json = $this->curl_post('getUpdates',$data);
			$this->error_msg = isset($json['error_code'])?$json['description']:'';
			return empty($this->error_msg)?$json['result']:false;
		} 

		else
		{
			$json = json_decode(file_get_contents('php://input'), true);
			if (is_null($json)) throw new Exception('Error invalid JSON');
			return $json;
		}
	}

	public function setWebhook($url,$certificate='')
	{
		$data = array(
			'url' => $url, 
			'certificate'=>empty($certificate)?'':$this->curlFile($certificate)
			);
		$json = $this->curl_post('setWebhook',$data,empty($certificate)?false:true);
		$this->error_msg = isset($json['error_code'])?$json['description']:'';
		return empty($this->error_msg)?$json:false;
	}

	/* ================
	 * Get Bot Message
	 * ================
	 * @param string $text
	 * @param integer $maxofcmd.
	 *
	 * 
	 * 	$maxofcmd is a maximum of the command. Default: 1
	 * 	example:
	 * 		with one command:
	 * 			/test command text
	 * 			$result = $bot->getBotMessage($text);
	 *		with two commands:
	 * 			/test command1 command2 text
	 * 			$result = $bot->getBotMessage($text,2);
	 * 
	 * @return as an object
	 *  command = string
	 *  param = array, param[0],param[1], etc
	 *  text = string
	*/
	public function getBotMessage($text,$maxofcmd=1)
	{
		$getCommand = explode(' ', $text, $maxofcmd+1);
 	
		if (count($getCommand) > 1)
		{
		
			if (count($getCommand) >= $maxofcmd)
			{	
				$params=$getCommand;
				unset($params[0]);
				unset($params[$maxofcmd]);
			}	
					$result = array(
						'command' => str_replace('@'.$this->bot->username,'',$getCommand[0]),
						'param' => $params,
						'text'	=> $getCommand[$maxofcmd]
						);				 
			
		}

		else
		{
			$result = array(
					'command' => str_replace('@'.$this->bot->username,'',$getCommand[0]),
					'param' => [],
					'text'	=> ''
				);			
		}
		
		return (object)$result;
	}

	public function getUserProfilePhotos($offset=0,$limit=100)
	{
		$data['user_id'] = $this->chat_id;
		$data['offset'] = $offset;
		$data['limit'] = $limit;
		$json=$this->curl_post('getUserProfilePhotos',$data);
		$this->error_msg = isset($json['error_code'])?$json['description']:'';
		return empty($this->error_msg)?$json['result']:false;
	}
	
	public function forwardMessage($from,$to,$message_id)
	{
		$data['chat_id'] = $to;
		$data['from_chat_id'] = $from;
		$data['message_id'] = $message_id;
		$data['disable_notification'] = $this->disable_notification;
		$json = $this->curl_post('forwardMessage',$data);
		$this->error_msg = isset($json['error_code'])?$json['description']:'';
		return empty($this->error_msg)?$json['result']:false;
	}

	public function adminAction($command,$chat_id,$user_id)
	{
		switch ($command)
		{
			case 'kick': $action = 'kickChatMember'; break;
			case 'unban': $action = 'unbanChatMember'; break;
		}

		$json = $this->curl_post($action,array('chat_id' => $chat_id, 'user_id'=>$user_id));
		$this->error_msg = isset($json['error_code'])?$json['description']:'';
		return empty($this->error_msg)?$json['result']:false;
	}
 
	public function getFile($file_id)
	{
		$json = $this->curl_post('getFile',array('file_id' => $file_id));
		$this->error_msg = isset($json['error_code'])?$json['description']:'';
		return empty($this->error_msg)?$json['result']:false;
	}

	public function send($command,$options=array())
	{
		$data['chat_id'] = $this->chat_id;
		$data['reply_to_message_id'] = $this->reply_id;
		$data['disable_notification'] = $this->disable_notification;

		switch ($command) {
			case 'message':
				$multipart = false;
				$data['text'] = $this->text;
				$data['parse_mode'] = isset($options['parse_mode'])?$options['parse_mode']:'HTML';
				$data['disable_web_page_preview']=isset($options['disable_web_preview'])?$options['disable_web_preview']:false;
			case 'photo':
			case 'document':
			case 'audio':
			case 'video':
			case 'voice':
			case 'sticker':
				$multipart = true;
				$data['caption'] = isset($options['title'])?$options['title']:'';
				$data['title'] = isset($options['title'])?$options['title']:'';
				$data[$command] = is_file($this->file_id)?$this->curlFile($this->file_id):$this->file_id;
				break;
			case 'location':
			case 'venue':
				$multipart = false;
				$data['latitude'] = $this->latitude;
				$data['longitude'] = $this->longitude;
				if ($command=='venue')
				{
					$data['title'] = isset($options['title'])?$options['title']:'';
					$data['address'] = isset($options['address'])?$options['address']:'';
					if (isset($options['foursquare_id']))$data['foursquare_id']=$options['foursquare_id'];
				}
				break;
			case 'contact':
					$multipart = false;
					$data['phone_number'] = isset($options['phone_number'])?$options['phone_number']:'';
					$data['first_name'] = isset($options['first_name'])?$options['first_name']:'';
					$data['last_name'] = isset($options['last_name'])?$options['last_name']:'';
					break;	
		}

		$json = $this->curl_post('send'.ucfirst($command),$data,$multipart);
		$this->error_msg = isset($json['error_code'])?$json['description']:'';
		return empty($this->error_msg)?$json['result']:false;
	}


	private function curl_post($command,$data=array(),$multipart=false)
	{
		$ch = curl_init();
		$config = array(
			CURLOPT_URL => 'https://api.telegram.org/bot'.$this->token . '/' . $command,
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true
		);
		if ($multipart)
			$config[CURLOPT_HTTPHEADER] = array('Content-Type: multipart/form-data');
		if (!empty($data))
			$config[CURLOPT_POSTFIELDS] = $data;
		curl_setopt_array($ch, $config);
		$result = curl_exec($ch);
		curl_close($ch);
		// return and decode json
		return !empty($result) ? json_decode($result, true) : false;
	}


	/**
	 * create curl file
	 *
	 * @param string $fileName
	 * @return string
	 */
	private function curlFile($fileName) {
		// set realpath
		$filename = realpath($fileName);
		// check file
		if (!is_file($filename))
			throw new Exception('File does not exists');
		// PHP 5.5 introduced a CurlFile object that deprecates the old @filename syntax
		// See: https://wiki.php.net/rfc/curl-file-upload
		if (function_exists('curl_file_create'))
				return curl_file_create($filename);
 
		// Use the old style if using an older version of PHP
		return "@$filename";
	}

}

