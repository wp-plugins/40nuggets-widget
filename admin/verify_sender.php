<?php	
		$email = isset($_GET["email"]) ? $_GET["email"] : "";
		if (empty($email)){
			echo '{"error":{"message":"invalid email"}}';
			exit;
		}
		
		$url = '/clients/me';
		$data["client"] = array("is_use_email" => false);
		$stop_sender_request["request"] = array(
												"url" => $url,
												"method" => "put",
												"body" => $data
												);
		
		$data["client"]["is_use_email"] = true;
		$data["client"]["from_email"] = $email;
		$start_sender_request["request"] = array(
												"url" => $url,
												"method" => "put",
												"body" => $data
												);
		
		$get_me_request["request"] = array("url" => $url);
		
		$requests["requests"] = array (
										$stop_sender_request,
										$start_sender_request,
										$get_me_request);
											
		$data_string = json_encode($requests);	
		
		$result = httpCall("http://40nuggets.com/api1/40nm/batch", "POST", $data_string);
				
		$result = json_decode($result);
		$me = $result->responses[1]->response->data;
		echo json_encode($me);



	function httpCall($url, $method=null, $data_string=null){

		//error_log ("Calling:$method $url With data $data_string");
	
		$cookie = dirname(__FILE__) . '/../fortynuggets.fnm';

		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
		
		if (isset($method)) 		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);                                                                     
		if (isset($data_string)) 	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		
		$result = curl_exec($ch);
				
		curl_close($ch);
		//error_log ("Result: $result");
		
		return $result;
	}

?>