<?php
class Auth extends Database {
  public function run() {
    http_response_code( '200' );
    return array( "message" => "AUTH Executado com sucesso" );
  }

}
?>
