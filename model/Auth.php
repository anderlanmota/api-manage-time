<?php
class Auth extends Database {
  // check if the requesting user is logged in
  protected function checkSession() {
	$auth = array("login" => "", "userId" => "", "role" => "", "status" => "");
    return array( "responseCode" => "200", "message" => "OK" );
  }

  // starts execution, identifying which method will call
  public function run() {
	$Sanitizer = new Sanitizer();  
	$method = strtolower($Sanitizer->alphabetic( $_SERVER[ 'REQUEST_METHOD' ], false, false, 20 ));
    if ( method_exists( $this, $method ) ) {
      return $this->$method();
    } else {
      http_response_code( 405 );
      return array( "message" => "Método $method indisponível." );
    }
  }

  // cria uma nova sessão
  // Api Public: YES
  private function post(){
	  // Request: { "login": STRING; "password": STRING }
	  // Return Success: { "token": JWT, "user": OBJECT }
	  // Return Fail: { "message" : STRING }
	  
	  return array( "message" => "AUTH POST OK" );
  }
 
  // apaga uma sessão
  // Api Public: NO
  private function delete(){
	  // Request: NULL
	  // Return Success: { "message": "" }
	  // Return Fail: { "message" : STRING }
	  
	  return array( "message" => "AUTH DELETE OK" );
  }
}
?>
