<?php
class ManageTime extends Auth {
  public function run() {
    http_response_code( '200' );
    return array( "message" => "TIME Executado com sucesso" );
  }

}
?>
