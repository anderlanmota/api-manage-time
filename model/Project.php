<?php
class Project extends Auth {
  public function run() {
    http_response_code( '200' );
    return array( "message" => "PROJECT Executado com sucesso" );
  }
	
}
?>
