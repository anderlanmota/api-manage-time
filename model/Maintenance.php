<?php
class Maintenance extends Database {
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

  // apaga log de arquivos
  // Api Public: YES
  private function delete() {
    $Sanitizer = new Sanitizer();
    $fileContents = file_get_contents( CONFIG_FOLDER . "/maintenance.json" );
    $contentArr = json_decode( $fileContents, true );
    $logRetention = intval( $contentArr[ 'logRetention' ] );
    $token = $contentArr[ 'token' ];
    $token_url = @$Sanitizer->alphanumeric( $_GET[ 'token' ], true, true, 55 );
    if ( strcasecmp( $token, $token_url ) != 0 ) {
      http_response_code( 401 );
      return array( "message" => "A solicitação não foi autorizada." );
    } else {
      if ( $logRetention > '0' ) {
		  $dateNow = date( 'Y-m-d H:i:s' );
		  $dateBack = date( 'Y-m-d H:i:s', strtotime( $dateNow . " -$logRetention seconds" ) );
		  $query[] = "DELETE FROM `tb_projects` WHERE `deleted` != '0' AND `deleted` <= '$dateBack';";
		  $query[] = "DELETE FROM `tb_projects_users` WHERE `deleted` != '0' AND `deleted` <= '$dateBack';";
		  $query[] = "DELETE FROM `tb_times` WHERE `deleted` != '0' AND `deleted` <= '$dateBack';";
		  $query[] = "DELETE FROM `tb_users` WHERE `deleted` != '0' AND `deleted` <= '$dateBack';";
		  $query[] = "DELETE FROM `tb_auth_error_log` WHERE `created` <= '$dateBack';";
          $result = $this->database_transaction( $query );
      }
      http_response_code( 200 );
      return array( "message" => "Log apagado com sucesso." );
    }
  }
}
?>
