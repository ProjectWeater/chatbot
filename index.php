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
	$rep_msg = [];

	if($recv_msg == "Toi") {
		$url = "http://api.thingspeak.com/channels/1486243/feeds.json?results=1";
		$strRet = file_get_contents($url);
		$strRet = json_decode($strRet);
		$test = $strRet->channel->field4;
		$rep_msg['text'] = $test;
		$rep_msg['type']='text';
	}else if($recv_msg == "อยู่ไหน"){
		$rep_msg['title']='My HOme';
		$rep_msg['address']='1-6-1 Yotsuya, Shinjuku-ku, Tokyo, 160-0004, Japan';
		$rep_msg['latitude']= 35.687574;
		$rep_msg['longitude']= 139.72922;
		$rep_msg['type']='location';
	}
	else{
		$rep_msg['originalContentUrl'] = "https://i.imgur.com/ObxhSgt.png";
		$rep_msg['previewImageUrl'] = "https://i.imgur.com/ObxhSgt.png";
		$rep_msg['type']='image';
	}
		

	$messages['messages'][0] =  $rep_msg;

	$encodeJson = json_encode($messages);

	$LINEDatas['url'] = "https://api.line.me/v2/bot/message/reply";
 	$LINEDatas['token'] = "1WXhWz3PtaV5ZjkMkZJFL15BvPJGcZesK+51a+K75Klua+yb2BEXpiSsL+eBMDcC2ygdIdegLR4CLihRsOZhcAGsdp92ui0HtDTKQ/JdYOEEu6YtEbEIj5X8+vTpd4RGZhH2Ke7GbLeBV01NFWl1+QdB04t89/1O/w1cDnyilFU=";
  	$results = sentMessage($encodeJson,$LINEDatas);

	/*Return HTTP Request 200*/
	http_response_code(200);


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