<?PHP
ini_set('memory_limit','105M');
error_reporting(E_ALL ^ E_NOTICE);
// ############################################################################
$valid_url = '/.*/';
$url = isset($_GET['url']) ? $_GET['url'] : false ;
$type = isset($_GET['type']) ? $_GET['type'] : null;
if ( !$url ) {
  
  $contents = 'ERROR: url not specified';
  $status = array( 'http_code' => 'ERROR' );
  
} else if ( !preg_match( $valid_url, $url ) ) {

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

$header_text = preg_split( '/[\r\n]+/', $header );
$jsonresp =false;
if ($type == 'html') {
  
  foreach ( $header_text as $header ) {
    if ( preg_match( '/^(?:Content-Type|Content-Language|Set-Cookie):/i', $header ) ) {
      header( $header );
    }
  }
  
  print $contents;
} else {
  $type == 'json' ? $jsonresp = true : false;
  $data = array();
    $data['status'] = array();
    $data['status']['http_code'] = $status['http_code'];
  $decoded_json = json_decode( $contents);
  $data['contents'] = $decoded_json ? $decoded_json : $contents;
( isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) ? 
  $is_xhr = strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' &&
  header( 'Content-type: application/' . ( $is_xhr ? 'json' : 'x-javascript' ) ) 
:  null ;

  $json = json_encode( $data );
  
  var_dump($jsonresp ? $json : $data);
  
}

?>