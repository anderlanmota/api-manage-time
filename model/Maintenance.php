<?php
class Maintenance extends Database {
  // starts execution, identifying which method will call
  public function run() {
	$Sanitizer = new Sanitizer();  
	$method = strtolower($Sanitizer->alphabetic( $_SERVER[ 'REQUEST_METHOD' ], false, false, 20 ));
    if ( method_exists( $this, $method ) ) {
      return $this->$method();
    } else {
      http_response_code( 405 );
      return array( "message" => "Método $method indisponível." );
    }
  }
 
  // apaga log de arquivos
  // Api Public: YES
  private function delete(){
	  
  }
}
?>
