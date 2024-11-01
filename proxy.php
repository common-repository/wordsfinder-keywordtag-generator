<?php
	
	$user = $_POST[ 'userid' ];
	$text = $_POST[ 'text' ];
	$url  = $_POST[ 'url' ];
	
if( function_exists( curl_init ) ) {

	$ch = curl_init( );
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );


	curl_setopt( $ch, CURLOPT_POSTFIELDS, '&user='.$user.'&text='.$text); 
	
	$response = curl_exec($ch);
	curl_close($ch);


} else {
	$url = substr($url, 7);
	$url = explode('/', $url);
	$postfields = '&user='.$user.'&text='.$text;
	$postfields = $postfields;
	$postlength = strlen($postfields);
	$content = "POST /".$url[1]."/".$url[2]."/".$url[3]." HTTP/1.1
Content-Type: application/x-www-form-urlencoded
User-Agent: php-fsockopen
Host: www.wordsfinder.com
Connection: close
Content-Length: ".$postlength."

".$postfields;
	
	$response = "";
	
	
	$fp = fsockopen($url[0], 80, $errno, $errstr, 30);
	if(!$fp) {
		echo $errstr;
	} else {
		fwrite($fp, $content);
		while(!feof($fp)) {
			$response .= fgets($fp, 128);
		}
		fclose($fp);

		$offset = strpos($response,'[');
		
		$response = substr($response, $offset);
		
		$offset = strpos($response,']');
		
		$response = substr($response, 0, ++$offset);
	}
}

echo $response;

?>