<?php
class ManageTime extends Auth {
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
 
  // cria um novo tempo / inicia um novo tempo
  // Api Public: NO
  private function post(){
	  // Requests: { "project_id": INT, "user_id": INT, "started_at": DATETIME, "ended_at": DATETIME, }
	  // Return Success: { "time" : OBJECT }
	  // Return Fail: { "message" : STRING }
	  
	  return array( "message" => "TIME POST OK" );
	  
  }

  // exibe os dados de um tempo ou uma lista de tempos
  // permite listar tempos referente a um usuário ou um projeto
  // Api Public: NO
  private function get(){
	  
	  return array( "message" => "TIME GET OK" );
  }

  // edita um tempo / para informar uma data final
  // Api Public: NO
  private function put(){
	  // permite editar dados ou confirmar o e-mail
	  
	  
	  return array( "message" => "TIME PUT OK" );
  }

  // apaga um tempo
  // Api Public: NO
  private function delete(){
	  
	  return array( "message" => "TIME DELETE OK" );
  }
}
?>
