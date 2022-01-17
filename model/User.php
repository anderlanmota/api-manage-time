<?php
class User extends Auth {
  public function run() {
    http_response_code( '200' );
    return array( "message" => "USER Executado com sucesso" );
  }

}
?>
