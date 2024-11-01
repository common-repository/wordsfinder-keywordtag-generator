<?php

function parseHttpResponse($content=null) {
    if (empty($content)) { return false; }
    // split into array, headers and content.
    $hunks = explode("\r\n\r\n",trim($content));
    if (!is_array($hunks) or count($hunks) < 2) {
        return false;
        }
    $header  = $hunks[count($hunks) - 2];
    $body    = $hunks[count($hunks) - 1];
    $headers = explode("\n",$header);
    unset($hunks);
    unset($header);
    if (in_array('Transfer-Coding: chunked',$headers)) {
        return trim(unchunkHttpResponse($body));
        } else {
        return trim($body);
        }
    }

	$user = $_POST[ 'userid' ];
	$user_url = $_POST[ 'userurl' ];
	$url  = $_POST[ 'url' ];
	
if( function_exists( curl_init ) ) {

	$ch = curl_init( );
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );


	curl_setopt( $ch, CURLOPT_POSTFIELDS, '&userid='.$user.'&url='.$user_url ); 
	
	$response = curl_exec($ch);
	curl_close($ch);


} else {
	$url = substr($url, 7);
	$url = explode('/', $url);
	$postfields = '&userid='.$user.'&url='.$user_url;
	$postfields = $postfields;
	$postlength = strlen($postfields);
	$content = "POST /".$url[1]." HTTP/1.1
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
		
		$response = parseHttpResponse($response);
		
		$offset = strpos($response,'\r\n\r\n');
		
		$response = substr($response, $offset);
		$response = substr($response, 3, strlen($response)-6);
	}
}

echo $response;

?>