<?php
	
	class DeCrypt {
		
		const APIURL = "https://api.decry.pt/";
		const USERNAME = "1047";
		const PASSWORD = "eK31l4yEI9";
		
		public static $CURL_OPTIONS = array(
    			CURLOPT_CONNECTTIMEOUT => 5,
    			CURLOPT_RETURNTRANSFER => TRUE,
    			CURLOPT_TIMEOUT => 10,
    			CURLOPT_USERAGENT => 'DeCryptAPI/1.0',
  		);
		
		public function sendAPI($endpoint, $data, $multipart = FALSE) {
    			$ch = curl_init();

    			if (!$multipart) {
      				$data = http_build_query($data);
    			}

    			$options = self::$CURL_OPTIONS + array(
      				CURLOPT_POST => TRUE,
      				CURLOPT_POSTFIELDS => $data,
      				CURLOPT_URL => self::APIURL . $endpoint,
    			);
    			curl_setopt_array($ch, $options);
    			$result = curl_exec($ch);

    			if (curl_errno($ch) == 60) {
      				curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/ca_bundle.crt');
      				$result = curl_exec($ch);
    			}

    			if ($result === FALSE || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
      				curl_close($ch);
      				return FALSE;
    			}
    			curl_close($ch);
    			$data = json_decode($result);
    			return json_last_error() == JSON_ERROR_NONE ? $data : $result;
  		}
		
		public function uploadFile($filename, $filecontent) {
			$result = self::sendAPI(
      				'/SendFile.php',
      				array(
        				'username' => self::USERNAME,
        				'password' => self::PASSWORD,
        				'filename' => $filename,
        				'filecontent' => $filecontent
	      			)
    			);
    			switch($result->code) {
    				case "ok":
    					//file ID for future download
    					return $result->info;
    				break;
    				case "login-failed":
    					//login failed
    					return "0";
    				break;
    			}
		}
		
		public function browseUploads($limit = 20, $only_success = 1, $min_ts = strtotime('-1 year',time()), $max_ts = time()) {
			$result = self::sendAPI(
      				'/SearchList.php',
      				array(
        				'username' => self::USERNAME,
        				'password' => self::PASSWORD,
        				'limit' => $limit,
        				'only_success' => $only_success,
        				'min_ts' => $min_ts,
        				'max_ts' => $max_ts
	      			)
    			);
    			switch($result->code) {
    				case "ok":
    					//file ID for future download
    					return $result->info;
    				break;
    				case "login-failed":
    					//login failed
    					return "0";
    				break;
    			}
		}
		
	}

?>