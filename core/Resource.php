<?php
class Resource extends Sanitizer {
  private $elements = array(); // deixar em branco
  private $resources = array(); // deixar em branco
  public $errormessage = ""; // deixar em branco
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
                $resource_name = $this->sanitizer_alphanumeric( $element_value, false, true, 55 );
              }
              if ( array_key_exists( $element_key + 1, $this->elements ) ) {
                $next_element_value = $this->elements[ $element_key + 1 ];
              } else {
                $next_element_value = "";
              }
              if ( empty( $next_element_value ) ) {
                $resource_id = "";
              } else {
                $resource_id = $this->sanitizer_alphanumeric( $next_element_value, false, true, 55 );
              }
              $this->resources[ $resource_name ] = "$resource_id";
            }
          }
        }
        if ( !array_key_exists( 'REQUEST_METHOD', $_SERVER ) ) {
          $this->errormessage = "Acesso negado. Método não permitido.";
        } else {
          $method = $_SERVER[ 'REQUEST_METHOD' ];
          if ( $method != "GET" && $method != "POST" && $method != "PUT" && $method != "PATCH" && $method != "DELETE" ) {
            $this->errormessage = "Acesso negado. Método não permitido.";
          } else {
            if ( array_key_exists( '_METHOD', $_POST ) ) {
              if ( $method == "POST" && $_POST[ '_METHOD' ] == "PATCH" ) {
                $_SERVER[ 'REQUEST_METHOD' ] = $_POST[ '_METHOD' ];
              }
            }
            $contentType = isset( $_SERVER[ "CONTENT_TYPE" ] ) ? trim( $_SERVER[ "CONTENT_TYPE" ] ) : '';
            if ( strcasecmp( $contentType, 'application/json' ) == 0 ) {
              $contentPost = trim( file_get_contents( "php://input" ) );
              $contentdecoded = json_decode( $contentPost, true );
              if ( is_array( $contentdecoded ) ) {
                $_POST = $contentdecoded;
              }
            }
          }
        }
      }
    }
    if ( empty( $this->errormessage ) ) { // não teve mensagem de erro
      define( 'RESOURCES', $this->resources );
      return true;
    } else {
      return false;
    }

  }
}
?>