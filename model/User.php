<?php
class User extends Auth {
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
	
  // cria um novo usuário
  // Api Public: YES
  private function post(){
	  // Request: { "name": STRING, "email": STRING, "login": STRING; "password": STRING }
	  // Return Success: { "user" : OBJECT }
	  // Return Fail: { "message" : STRING }
	  
	  return array( "message" => "USER POST OK" );
  }

  // exibe os dados de um usuário ou uma lista de usuários
  // permite listar usuários referente a um projeto
  // Api Public: NO
  private function get(){
	  
	  return array( "message" => "USER GET OK" );
  }

  // edita um usuário
  // Api Public: YES
  private function put(){
	  
	  return array( "message" => "USER PUT OK" );
  }

  // apaga um usuário
  // Api Public: YES
  private function delete(){
	  
	  return array( "message" => "USER DELETE OK" );
  }
}
?>
