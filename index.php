<?PHP

$enable_native   = false;
$valid_url_regex = '/.*/';

// #######################################fb.me/kmoz000##################################

$url = isset($_GET['url']) ? $_GET['url'] : false ;
$type = isset($_GET['type']) ? $_GET['type'] : false;
$url = isset($_POST['url']) ? $_POST['url'] : false ;
$type = isset($_POST['type']) ? $_POST['type'] : false;
if ( !$url ) {
  
  // Passed url not specified.
  $contents = 'ERROR: url not specified';
  $status = array( 'http_code' => 'ERROR' );
  
} else if ( !preg_match( $valid_url_regex, $url ) ) {
  
  // Passed url doesn't match $valid_url_regex.
  $contents = 'ERROR: invalid url';
  $status = array( 'http_code' => 'ERROR' );
  
} else {
  $ch = curl_init( $url );
  
  if ( strtolower($_SERVER['REQUEST_METHOD']) == 'post' ) {
    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $_POST );
  }

  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
  curl_setopt( $ch, CURLOPT_HEADER, true );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  
  curl_setopt( $ch, CURLOPT_USERAGENT, isset($_GET['user_agent']) ? $_GET['user_agent'] : $_SERVER['HTTP_USER_AGENT'] );
  
  list( $header, $contents ) = preg_split( '/([\r\n][\r\n])\\1/', curl_exec( $ch ), 2 );
  
  $status = curl_getinfo( $ch );
  
  curl_close( $ch );
}

// Split header text into an array.
$header_text = preg_split( '/[\r\n]+/', $header );
$jsonresp =false;
if ( !($type == 'html') ) {
  $type == 'json' ? $jsonresp = true : (
  $contents = 'ERROR: invalid mode'&&
  $status = array( 'http_code' => 'ERROR' ));
}
if ($type == 'html') {
  // Propagate headers to response.
  foreach ( $header_text as $header ) {
    if ( preg_match( '/^(?:Content-Type|Content-Language|Set-Cookie):/i', $header ) ) {
      header( $header );
    }
  }
  
  print $contents;
  
} else {

  $data = array();
    $data['status'] = array();
    $data['status']['http_code'] = $status['http_code'];
  $decoded_json = json_decode( $contents );
  $dom = new DOMDocument;
  function element_to_obj($element) {
    $obj = array( "tag" => $element->tagName );
    foreach ($element->attributes as $attribute) {
        $obj[$attribute->name] = $attribute->value;
    }
    foreach ($element->childNodes as $subElement) {
       ($subElement->nodeType == XML_TEXT_NODE) ? 
            $obj["html"] = $subElement->wholeText : $obj["children"][] = element_to_obj($subElement);
    }
    return $obj;
}
  $data['contents'] = $decoded_json ? $decoded_json :  ($dom->loadHTML($contents) ? element_to_obj($dom->documentElement) : false );
  
( isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) ? 
  $is_xhr = strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' &&
  header( 'Content-type: application/' . ( $is_xhr ? 'json' : 'x-javascript' ) ) 
:  null ;

  $json = json_encode( $data );
  
  print_r($jsonresp ? $json : $data);
  
}

?>