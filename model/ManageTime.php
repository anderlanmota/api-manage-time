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
	  $Sanitizer = new Sanitizer();
	  $started = @$Sanitizer->datetime(DATA['started']);
	  $ended = @$Sanitizer->datetime(DATA['started']);
	  $userId = @RESOURCES['users'];
	  $projectId = @RESOURCES['projects'];
	  $timeId = @RESOURCES['times'];
	  
	  
	  // Requests: { "project_id": INT, "user_id": INT, "started_at": DATETIME, "ended_at": DATETIME, }
	  // Return Success: { "time" : OBJECT }
	  // Return Fail: { "message" : STRING }
	  
	  return array( "message" => "TIME POST $started / $ended / $userId / $projectId / $timeId", "data" => DATA );
	  
  }

  // exibe os dados de um tempo ou uma lista de tempos
  // permite listar tempos referente a um usuário ou um projeto
  // Api Public: NO
  private function get(){
	  $timeId = @RESOURCES['times'];
	  $userId = @RESOURCES['users'];
	  $projectId = @RESOURCES['projects'];
	  
	  return array( "message" => "TIME GET OK" );
  }

  // edita um tempo / para informar uma data final
  // Api Public: NO
  private function put(){
	  $Sanitizer = new Sanitizer();
	  $ended = @$Sanitizer->datetime(DATA['started']);
	  $timeId = @RESOURCES['times'];
	  $userId = @RESOURCES['users'];
	  $projectId = @RESOURCES['projects'];

	  
	  return array( "message" => "TIME PUT OK" );
  }

  // apaga um tempo
  // Api Public: NO
  private function delete(){
	  $timeId = @RESOURCES['times'];
	  $userId = @RESOURCES['users'];
	  $projectId = @RESOURCES['projects'];
	  
	  return array( "message" => "TIME DELETE OK" );
  }
}
?>
