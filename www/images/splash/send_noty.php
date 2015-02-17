<?php

$message = "حسنين هادي";

$androidIDs = array("APA91bFznriTmKJunAnAS9WdHwuZ-Ppu9xkNFBd1Txxv_cpiHc5ruYZUKAG9fiHXUBHWVjDLPwtn3dl4OzXKcZyaS7meVzWKLvPDRwWqDkktIDs198nR-vIOasVaid2VyNdxJvjFGHVIVwxz_y-mycJU5YUgXBWkVDRS01ISG7hdor9y8VlNW-8");
$iosIDs = array();
define("GOOGLE_API_KEY", "AIzaSyB8besyagHO5l0Fn4KV6RHIoqy1oq1quzs"); // Place your Google API Key


$androidIDs[] =  "APA91bGf6evZ-av2s7jtUkzT8xcD8ujxHoZOspfRV0QIdlRHSecRUHHTyfzCQ3blL9GbcWtwQ-RW2toqPabvaWEiA1PDO-6RWCdapgHoIhHQfG7XnFUp5JQcA9m-BDfTunJjE5xToCAZJt0lDdAZXtYDcJNQFXRSVw";
send_android_notification($androidIDs,$message);




function send_android_notification($deviceIDs,$message) {
	$message = array( "message" => $message,"msgcnt" => "1","timeStamp" => time() , "title" => "انتشر حديثاً");
    
    // Set POST variables
    $url = 'https://android.googleapis.com/gcm/send';
 
    $fields = array(
        'registration_ids' => $deviceIDs,
        'data' => $message,
    );
 
    $headers = array(
        'Authorization: key=' . GOOGLE_API_KEY,
        'Content-Type: application/json'
    );
    // Open connection
    $ch = curl_init();
    
    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
    // Execute post
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    //echo $result;
 
    // Close connection
    curl_close($ch);
}

function send_ios_notification($deviceIDs,$message) {
	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
	stream_context_set_option($ctx, 'ssl', 'passphrase', '12345675'); // Add your own ck.pem passphrase here
	
	foreach($deviceIDs as $deviceToken) {
		// Open a connection to the APNS server
		// 1th : ssl://gateway.sandbox.push.apple.com:2195
		// 2th : ssl://gateway.push.apple.com:2195
		$fp = stream_socket_client(
			'ssl://gateway.push.apple.com:2195', $err,
			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		if (!$fp) exit("Failed to connect: $err $errstr" . PHP_EOL);
		
		echo 'Connected to APNS' . PHP_EOL . "<br/>";
		
		// Create the payload body
		$body['aps'] = array(
			'alert' => $message,
			'sound' => 'default'
			);
		$payload = json_encode($body);
		//{"aps":{"alert":"\u0686\u06a9 \u0646\u0648\u0646\u06cc\u0641\u06cc\u06a9\u06cc\u0634\u0646","sound":"default"}}
		$payload = '{"aps":{"alert":"' . $message . '","sound":"default"}}';

		//$payload = json_encode($body, JSON_UNESCAPED_UNICODE);
		
		// Build & send notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		$result = fwrite($fp, $msg, strlen($msg));
		
		if (!$result)
			echo 'Message not delivered' . PHP_EOL . "<br/>";
		else
			echo 'Message successfully delivered' . PHP_EOL . "<br/>";
	}	
	// Close the connection to the server
	fclose($fp);
}


