<?php 
	/*Get Data From POST Http Request*/
	$datas = file_get_contents('php://input');
	/*Decode Json From LINE Data Body*/
	$deCode = json_decode($datas,true);


	file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);

	$replyToken = $deCode['events'][0]['replyToken'];
	$recv_msg = $deCode['events'][0]['message']['text'];

	$messages = [];
	$messages['replyToken'] = $replyToken;
	if($recv_msg == "Toi") {
		$rep_msg = "OK, Hello Toi";
		$reply_type = "text";
	}else {
		$recv_msg = "https://i.imgur.com/ObxhSgt.png";
		$reply_type = "image";
	}
		



	
	$messages['messages'][0] = getFormatTextMessage($rep_msg, $reply_type);

	$encodeJson = json_encode($messages);

	$LINEDatas['url'] = "https://api.line.me/v2/bot/message/reply";
 	$LINEDatas['token'] = "VwWkOSS39IWXMExM5SASHLT8V7GJMCIaFeMWy3HI19fP28GZz8v/K2LpDHqmjWNuhZzUMLWe4sJGOcjLZAm2ofyv8/dtH0ILQPGaUeQgOMTdw35+o0ZbD7yDg1qu7AYw5rKb9HXJyZvu/tgX0UckrAdB04t89/1O/w1cDnyilFU=";

  	$results = sentMessage($encodeJson,$LINEDatas);

	/*Return HTTP Request 200*/
	http_response_code(200);

	function getFormatTextMessage($text, $type)
	{
		$datas = [];
		if($type == "text") {
			$datas['type'] = 'text';
			$datas['text'] = $text;
		}
		if($type == "image") {
			$datas['type'] = 'image';
			$datas['originalContenturl'] = $text;
			$datas['previewImageturl'] = $text;
		return $datas;
	}

	function sentMessage($encodeJson,$datas)
	{
		$datasReturn = [];
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $datas['url'],
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $encodeJson,
		  CURLOPT_HTTPHEADER => array(
		    "authorization: Bearer ".$datas['token'],
		    "cache-control: no-cache",
		    "content-type: application/json; charset=UTF-8",
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		    $datasReturn['result'] = 'E';
		    $datasReturn['message'] = $err;
		} else {
		    if($response == "{}"){
			$datasReturn['result'] = 'S';
			$datasReturn['message'] = 'Success';
		    }else{
			$datasReturn['result'] = 'E';
			$datasReturn['message'] = $response;
		    }
		}

		return $datasReturn;
	}
?>
