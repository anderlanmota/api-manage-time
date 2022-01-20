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
    $fileContents = file_get_contents( dirname( __FILE__ ) . "/../config/maintenance.json" );
    $contentArr = json_decode( $fileContents, true );
    $logRetention = intval( $contentArr[ 'logRetention' ] );
    $token = intval( $contentArr[ 'token' ] );
    $token_url = @$Sanitizer->alphanumeric( $_GET[ 'token' ], true, true, 55 );
    if ( strcasecmp( $token, $token_url ) != 0 ) {
      http_response_code( 401 );
      return array( "message" => "A solicitação não foi autorizada $token, $token_url." );
    } else {
      if ( $logRetention > '0' ) {


      }
      $http_response_code( 200 );
      return array( "message" => "Log apagado com sucesso." );
    }
  }
}
?>
