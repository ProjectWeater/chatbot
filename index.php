<?php 
	/*Get Data From POST Http Request*/
	$datas = file_get_contents('php://input');
	/*Decode Json From LINE Data Body*/
	$deCode = json_decode($datas,true);


	curl -v -X POST https://api.line.me/v2/bot/richmenu \
	-H 'Authorization: Bearer {channel access token}' \
	-H 'Content-Type: application/json' \
	-d \
	'{
	"size":{
		"width":2500,
		"height":1686
	},
	"selected": false,
	"name": "LINE Developers Info",
	"chatBarText": "Tap to open",
	"areas": [
		{
			"bounds": {
				"x": 34,
				"y": 24,
				"width": 169,
				"height": 193
			},
			"action": {
				"type": "uri",
				"uri": "https://developers.line.biz/en/news/"
			}
		},
		{
			"bounds": {
				"x": 229,
				"y": 24,
				"width": 207,
				"height": 193
			},
			"action": {
				"type": "uri",
				"uri": "https://www.line-community.me/en/"
			}
		},
		{
			"bounds": {
				"x": 461,
				"y": 24,
				"width": 173,
				"height": 193
			},
			"action": {
				"type": "uri",
				"uri": "https://engineering.linecorp.com/en/blog/"
			}
		}
	]
	}'

	{
		"richMenuId": "richmenu-88c05ef6921ae53f8b58a25f3a65faf7"
	}

	curl -v -X POST https://api-data.line.me/v2/bot/richmenu/richmenu-88c05ef6921ae53f8b58a25f3a65faf7/content \
	-H "Authorization: Bearer {channel access token}" \
	-H "Content-Type: image/jpeg" \
	-T image.jpg

	curl -v -X POST https://api.line.me/v2/bot/user/all/richmenu/richmenu-88c05ef6921ae53f8b58a25f3a65faf7 \
	-H "Authorization: Bearer {channel access token}"

	$replyToken = $deCode['events'][0]['replyToken'];
	$recv_msg = $deCode['events'][0]['message']['text'];



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