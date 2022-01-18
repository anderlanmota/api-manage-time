<?php
class Project extends Auth {
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

  // cria um novo projeto
  // Api Public: NO
  private function post() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $title = @$Sanitizer->alphanumeric( DATA[ 'title' ], true, true, 55 );
      $description = @$Sanitizer->text( DATA[ 'description' ], 1024 );
      $status = @strtolower( $Sanitizer->alphabetic( DATA[ 'status' ], false, false, 30 ) );
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];
 
      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {

		  
		  
      }
    }
  }

  // exibe os dados de um projeto ou uma lista de projetos
  // permite listar projetos referente a um usuário
  // Api Public: NO
  private function get() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];
 
      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {

		  
		  
      }
    }
  }

  // edita um projeto
  // Api Public: NO
  private function put() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $title = @$Sanitizer->alphanumeric( DATA[ 'title' ], true, true, 55 );
      $description = @$Sanitizer->text( DATA[ 'description' ], 1024 );
      $status = @strtolower( $Sanitizer->alphabetic( DATA[ 'status' ], false, false, 30 ) );
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];
 
      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {

		  
		  
      }
    }
  }

  // apaga um projeto
  // Api Public: NO
  private function delete() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];
		
      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {

		  
		  
      }
    }
  }
}
?>
