<?php
class Project extends Auth {
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
 
  // cria um novo projeto
  // Api Public: NO
  private function post(){
	  // Request: { "title": STRING, "description": STRING, "user_id": ARRAY }
	  // Return Success: { "project" : OBJECT }
	  // Return Fail: { "message" : STRING }
	  
	  
	  return array( "message" => "PROJECT POST OK" );
  }

  // exibe os dados de um projeto ou uma lista de projetos
  // permite listar projetos referente a um usuário
  // Api Public: NO
  private function get(){
	  
	  return array( "message" => "PROJECT GET OK" );
  }

  // edita um projeto
  // Api Public: NO
  private function put(){
	  
	  return array( "message" => "PROJECT PUT OK" );
  }

  // apaga um projeto
  // Api Public: NO
  private function delete(){
	  
	  return array( "message" => "PROJECT DELETE OK" );
  }
}
?>
