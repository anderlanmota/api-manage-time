<?php
class Maintenance extends Database {
  public function run() {
    http_response_code( '200' );
    return array( "message" => "MAINTENANCE Executado com sucesso" );
  }

}
?>
