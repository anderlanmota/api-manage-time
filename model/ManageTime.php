<?php
class ManageTime extends Auth { 
  // starts execution, identifying which method will call
  public function run() {
    $Sanitizer = new Sanitizer();
    $method = strtolower( $Sanitizer->alphabetic( $_SERVER[ 'REQUEST_METHOD' ], false, false, 20 ) );
    if ( method_exists( $this, $method ) ) {
      return $this->$method();
    } else {
      http_response_code( 405 );
      return array( "message" => "Método $method indisponível." );
    }
  }

  // cria um novo tempo / inicia um novo tempo
  // Api Public: NO
  private function post() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $started = @$Sanitizer->datetime( DATA[ 'started' ] );
      $ended = @$Sanitizer->datetime( DATA[ 'ended' ] );
      $description = @$Sanitizer->text( DATA[ 'description' ], 1024 );
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];
      $timeId = @RESOURCES[ 'times' ];

      //$datetime1 = strtotime( '2022-01-18 08:56:45' );
      //$datetime2 = strtotime( '2022-01-18 09:58:46' );
      //$secs = round( $datetime2 - $datetime1 );
 	
      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {

		  
		  
      }
    }
  }

  // exibe os dados de um tempo ou uma lista de tempos
  // permite listar tempos referente a um usuário ou um projeto
  // Api Public: NO
  private function get() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];
      $timeId = @RESOURCES[ 'times' ];
 
      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {

		  
		  
      }
    }
  }

  // edita um tempo / para informar uma data final
  // Api Public: NO
  private function put() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $ended = @$Sanitizer->datetime( DATA[ 'ended' ] );
      $description = @$Sanitizer->text( DATA[ 'description' ], 1024 );
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];
      $timeId = @RESOURCES[ 'times' ];
 
      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {

		  
		  
      }
    }
  }

  // apaga um tempo
  // Api Public: NO
  private function delete() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];
      $timeId = @RESOURCES[ 'times' ];
		
      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {

		  
		  
      }
    }
  }
}
?>
