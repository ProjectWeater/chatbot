<?php 
	/*Get Data From POST Http Request*/
	$datas = file_get_contents('php://input');
	/*Decode Json From LINE Data Body*/
	$deCode = json_decode($datas,true);

	$messages = [];
	$messages['replyToken'] = $replyToken;
	$rep_msg = [];

	if($recv_msg == "สวัสดี") {
		$rep_msg ['text'] = "สวัสดีครับ";
		$rep_msg ['type'] = 'text';
	}else if($recv_msg == "อุณหภูมิ") {
		$url = "https://api.thingspeak.com/channels/1483851/feeds.json?results=1";
		$strRet = file_get_contents($url);
		$strRet = json_decode($strRet);
		$temp = $strRet->feeds[0]->field2;
		$rep_msg['text'] = $temp;
		$rep_msg['type']='text';
	}else if($recv_msg == "ความชื้น") {
		$url = "https://api.thingspeak.com/channels/1483851/feeds.json?results=1";
		$strRet = file_get_contents($url);
		$strRet = json_decode($strRet);
		$temp = $strRet->feeds[0]->field1;
		$rep_msg['text'] = $temp;
		$rep_msg['type']='text';
	}else if($recv_msg == "รูปภาพ"){
		$url = "http://api.thingspeak.com/channels/1486243/feeds.json?results=1";
		$strRet = file_get_contents($url);
		$strRet = json_decode($strRet);
		$pic = $strRet->feeds[0]->field4;
		$rep_msg['image'] = "https://i.imgur.com/"+$pic".png";
		$rep_msg['type']='image';
	}
	else{
		$rep_msg['originalContentUrl'] = "https://i.imgur.com/ObxhSgt.png";
		$rep_msg['previewImageUrl'] = "https://i.imgur.com/ObxhSgt.png";
		$rep_msg['type']='image';
	}
		

	$messages['messages'][0] =  $rep_msg;

	$encodeJson = json_encode($messages);

	$LINEDatas['url'] = "https://api.line.me/v2/bot/message/reply";
 	$LINEDatas['token'] = "VwWkOSS39IWXMExM5SASHLT8V7GJMCIaFeMWy3HI19fP28GZz8v/K2LpDHqmjWNuhZzUMLWe4sJGOcjLZAm2ofyv8/dtH0ILQPGaUeQgOMTdw35+o0ZbD7yDg1qu7AYw5rKb9HXJyZvu/tgX0UckrAdB04t89/1O/w1cDnyilFU=";
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