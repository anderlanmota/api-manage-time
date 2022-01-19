<?php
class User extends Auth {
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

  // cria um novo usuário
  // Api Public: YES
  private function post() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $name = @$Sanitizer->alphanumeric( DATA[ 'name' ], true, true, 55 );
      $email = @$Sanitizer->email( DATA[ 'email' ] );
      $login = @strtolower( $Sanitizer->alphanumeric( DATA[ 'login' ], false, false, 30 ) );
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];

      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {
		  
		  $Database = new Database();

		  $data_insert = array('projectId' => "11", 'userId' => "22", 'created' => "2022-01-18 08:56:45");
  $result = $Database->database_insert("tb_projects_users", $data_insert); 
		  
		  http_response_code( 200 );
        return array( "message" => "BD OK." );
		  
      }
    }
  }

  // exibe os dados de um usuário ou uma lista de usuários
  // permite listar usuários referente a um projeto
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

  // edita um usuário
  // Api Public: NO
  private function put() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $name = @$Sanitizer->alphanumeric( DATA[ 'name' ], true, true, 55 );
      $email = @$Sanitizer->email( DATA[ 'email' ] );
      $login = @strtolower( $Sanitizer->alphanumeric( DATA[ 'login' ], false, false, 30 ) );
      $userId = @RESOURCES[ 'users' ];
 
      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {

		  
		  
      }
    }
  }

  // apaga um usuário
  // Api Public: NO
  private function delete() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $userId = @RESOURCES[ 'users' ];

      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {

		  
		  
      }
    }
  }
}
?>
