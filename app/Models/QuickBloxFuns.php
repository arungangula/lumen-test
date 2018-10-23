<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//Model to work with QuickBlox Messaging API 

class QuickBloxFuns extends Model {

	const DEBUG=0;

	private static function parseResponse($response , $curl)
	{
	    $body = substr($response, curl_getinfo($curl, CURLINFO_HEADER_SIZE));

	    $headers = array();

	    $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

	    foreach (explode("\r\n", $header_text) as $i => $line)
		{
	        if ($i === 0)
	            $headers['http_code'] = $line;
	        else
	        {
	            list ($key, $value) = explode(': ', $line);

	            $headers[$key] = $value;
	        }
		}
		if(self::DEBUG==1){
			echo 'Header:<br>';
			print_r($headers);
		}

		return array('header' => $headers, 'body' => json_decode($body));

	} 

	public static function get($url, $header=array(), $params=array()) 
	{	
		if(self::DEBUG == 1)
		{
			echo("In get<br>");
			echo("Param url=" . $url . "<br>");
		}
		
		$url = $url.'?'.http_build_query($params, '', '&');

		if(self::DEBUG == 1)
			echo("Query url=" . $url . "<br>");
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch,CURLOPT_HTTPHEADER, $header );
		curl_setopt($ch,CURLOPT_HEADER, true );
			
		$response = curl_exec($ch);
		
		// Check errors
		if ($response) {
			if(self::DEBUG == 1)
						echo "Response=" . $response . "<br><br>";
			$response=self::parseResponse($response , $ch);
		} else {
				$error = curl_error($ch). '(' .curl_errno($ch). ')';
				echo $error . "<br>";
				$response = $error;
		}

		// Close connection
		curl_close($ch);
		
		//Return response/error
		return $response;

	}



	public static function post($url, $header=array(), $params=array())
	{
		// Build post body
		$post_body = http_build_query($params);


		if(self::DEBUG == 1)
			echo "postBody: " . $post_body . "<br><br>" ;

		// Configure cURL
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url); // Full path 
		curl_setopt($curl, CURLOPT_POST, true); // Use POST
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_body); // Setup post body
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Receive server response
		curl_setopt($curl,CURLOPT_HTTPHEADER, $header );
		curl_setopt($curl, CURLOPT_HEADER, true );
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		// Execute request and read responce
		$response = curl_exec($curl);

		// Check errors
		if ($response) {
			if(self::DEBUG == 1)
						echo "Response=" . $response . "<br><br>";
			$response=self::parseResponse($response , $curl);
		} else {
				$error = curl_error($curl). '(' .curl_errno($curl). ')';
				echo $error . "<br>";
				$response = $error;
		}

		// Close connection
		curl_close($curl);
		
		//Return response/error
		return $response;
	}
		
	public static function customRequest($url, $request , $header=array(), $params=array())
	{
		// Build post body
		$post_body = http_build_query($params);

//		$url = $url.'?'.http_build_query($params, '', '&');

		if(self::DEBUG == 1)
			echo "Query url: " . $url . "<br><br>";

		// Configure cURL
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url); // Full path 
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_body); // Setup post body
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Receive server response
		curl_setopt($curl,CURLOPT_HTTPHEADER, $header );
		curl_setopt($curl,CURLOPT_HEADER, true );
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		// Execute request and read responce
		$response = curl_exec($curl);

		// Check errors
		if ($response) {
			if(self::DEBUG == 1)
						echo "Response=" . $response . "<br><br>";
			$response=self::parseResponse($response , $curl);
		} else {
				$error = curl_error($curl). '(' .curl_errno($curl). ')';
				echo $error . "<br>";
				$response = $error;
		}

		// Close connection
		curl_close($curl);
		
		//Return response/error
		return $response;
	}	
}

?>