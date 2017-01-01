<?php

/*  Bing Search
 *  Author: Ali
 *   
*/


/* 
 * First, you must set The BING_APPID before using this plugin on the config file.
 * The BING_APPID is a value that enables the API to validate that a request is from a registered Bing application developer.
 * Getting an BING_APPID is a straightforward process. First, go to the Bing Developer Center and sign in with your Windows Live ID. 
 * After signing in, you will be presented with a link to create a new AppID. 
*/
//define('BING_APPID','');

function BingSearch($search,$count=5,$safesearch='Moderate')
{

	$url='https://api.cognitive.microsoft.com/bing/v5.0/search';
 
	$data=array(
		'q' => urlencode($search),
		'count' => $count,
		'offset' => 0,
		'safesearch' => $safesearch
	);
 
	$params = '?';
	foreach($data as $key => $value)
	{
		$params .= $key.'='.$value.'&';
	} 
	$url .= rtrim($params,'&');
 
	$header = [
		'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
		'Cache-Control: max-age=0',
		'Connection: keep-alive',
		'Keep-Alive: 300',
		'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
		'Accept-Language: en-us,en;q=0.5',
		'Pragma: ',
		'Content-Length: 16853',
		'Ocp-Apim-Subscription-Key: ' . BING_APPID
	];
			
	$ch = curl_init(); 
	
	curl_setopt($ch, CURLOPT_URL, $url);    // Setting cURL's URL option with the $url variable passed into the function
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (; rv:26.0) Gecko/20100101 Firefox/26.0');
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POST, FALSE); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Setting cURL's option to return the webpage data
    
    $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
    curl_close($ch);    // Closing cURL
    
    return $data;   // Returning the data from the function
}



function BingUrlExtract($url){
	
	foreach (explode('&', $url) as $chunk) {
		$param = explode("=", $chunk);
		if (urldecode($param[0]) == 'r'){
			return urldecode($param[1]);
		 } 
	} 
}


function call_bing($bot){

	$result['error'] = true;
	$data = $bot->message;
	$text = isset($data['text'])?$data['text']:'';
	
	if (!empty($text))
	{

	
		$botMsg= $bot->getBotMessage($text);	
		$options =  array ('parse_mode' => 'HTML','disable_web_page_preview'=> TRUE);
		
		
		if ($botMsg != false)
		{
			switch ($botMsg->command)
			{

				case '!b':
				case '!bing':
				case '/bing':
				case '/b':
				case '!bnsfw':
				case '!bingnsfw':
				case '/bingnsfw':
				case '/bnsfw':
				
						$search = $botMsg->text;
						
						if (!empty($bot->reply_to_message))
						{
							$search = $bot->reply_to_message['text'];	
						}
						
						if (!empty($search))
						{
							$bingsafe = 'Strict';
						
						
							//for NSFW command
							$nsfw = array('!bnsfw','!bingnsfw','/bnsfw','/bingnsfw');
							if (in_array($botMsg->command,$nsfw))
							{
								$bingsafe = 'off';	
							}
							
							$bing = BingSearch($search,5,$bingsafe);
							$bing_result = json_decode($bing);
							
							$bing_string = '';
							if (!empty($bing_result->webPages->value))
							{
								$bing_values = $bing_result->webPages->value;
								$c = 0;
									foreach($bing_values as $value)
									{
										$c++;
										$bing_string .= $c . '. <a href="'. BingUrlExtract($value->url).'">'. $value->name."</a>\n";
									}						
							} 	
					
							if ($bing_string == ''){
								$bing_string = '<b>'. $search . '</b> not found';
							}
					
							$bot->text = $bing_string;
							$result['error'] = false;
							$result['result'] = $bot->send('message',$options);
						}
					break;
				default:
                    break;
			}
		}
	}
		
	return (object)$result;
}