<?php
// this class checks if the requested URL has the valid features
// and save the information in const RESOURCES
class Resource extends Sanitizer {
  private $elements = array();
  private $resources = array();
  private $data = array();
  public $errormessage = "";

  // main method. responsible for processing the information received
  public function prepare() {
    if ( array_key_exists( 'PATH_INFO', $_SERVER ) ) {
      $this->elements = explode( '/', $_SERVER[ 'PATH_INFO' ] );
    }
    if ( !array_key_exists( '1', $this->elements ) ) {
      $this->errormessage = "Acesso negado. Link inválido.";
    } else {
      $elements_total = count( $this->elements );
      if ( $elements_total > '10' || $elements_total < '2' ) {
        $this->errormessage = "Acesso negado. Link inválido.";
      } else {
        $element_error = false;
        foreach ( $this->elements as $element_key => $element_value ) {
          if ( $element_key >= '2' ) {
            if ( $element_key % 2 == 0 ) {
              if ( empty( $element_value ) ) {
                $resource_name = "";
              } else {
                $resource_name = $this->alphanumeric( $element_value, false, true, 55 );
              }
              if ( array_key_exists( $element_key + 1, $this->elements ) ) {
                $next_element_value = $this->elements[ $element_key + 1 ];
              } else {
                $next_element_value = "";
              }
              if ( empty( $next_element_value ) ) {
                $resource_id = "";
              } else {
                $resource_id = $this->alphanumeric( $next_element_value, false, true, 55 );
              }
              $this->resources[ $resource_name ] = @intval($resource_i);
            }
          }
        }
        $contentType = isset( $_SERVER[ "CONTENT_TYPE" ] ) ? trim( $_SERVER[ "CONTENT_TYPE" ] ) : '';
        if ( strcasecmp( $contentType, 'application/json' ) == 0 ) {
          $contentReceived = trim( file_get_contents( "php://input" ) );
          $contentDecoded = json_decode( $contentReceived, true );
          if ( is_array( $contentDecoded ) ) {
			  $this->data = $contentDecoded;// save array with received data
          }
        }
      }
    }
    if ( empty( $this->errormessage ) ) {
      // there were no errors, save the information
      // example:
      // URL = /users/123/projects/456
	  //RESOURCES = array('users'=>123, 'projects'=>456)
      define( 'RESOURCES', $this->resources );
	
	  // example
      // JSON = { "name": "Joseph", "email": "joseph@email.com" }
	  // DATA = array('name'=> "Joseph", 'email'=>"joseph@email.com")
	  define( 'DATA', $this->data );
      return true;
    } else {
      return false;
    }

  }
}
?>