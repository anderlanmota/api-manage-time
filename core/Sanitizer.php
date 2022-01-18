<?php
// class responsible for filtering input data
class Sanitizer {

  public function email( $string ) {
    $string = filter_var( substr( trim( strtolower( $string ) ), 0, 155 ), FILTER_SANITIZE_EMAIL );
    return $string;
  }

  public function alphabetic( $string, bool $allow_accents = true, bool $allow_spaces = false, int $limit = 255 ) {
    $string = str_replace( array( '"', "'", '`', '´', '¨' ), '', trim( $string ) );
    $string = substr( $string, 0, $limit );
    if ( !$allow_accents && !$allow_spaces ) {
      $string = preg_replace( '#[^A-Za-z]#', '', $string );
    }
    if ( $allow_accents && !$allow_spaces ) {
      $string = preg_replace( '#[^A-Za-zà-źÀ-Ź]#', '', $string );
    }
    if ( !$allow_accents && $allow_spaces ) {
      $string = preg_replace( '#[^A-Za-z ]#', '', $string );
    }
    if ( $allow_accents && $allow_spaces ) {
      $string = preg_replace( '#[^A-Za-zà-źÀ-Ź ]#', '', $string );
    }
    return $string;
  }

  public function alphanumeric( $string, bool $allow_accents = true, bool $allow_spaces = false, int $limit = 255 ) {
    $string = substr( str_replace( array( '"', "'", '`', '´', '¨' ), '', trim( $string ) ), 0, $limit );
    if ( !$allow_accents && !$allow_spaces ) {
      return preg_replace( '#[^A-Za-z0-9]#', '', $string );
    }
    if ( $allow_accents && !$allow_spaces ) {
      return preg_replace( '#[^A-Za-zà-źÀ-Ź0-9]#', '', $string );
    }
    if ( !$allow_accents && $allow_spaces ) {
      return preg_replace( '#[^A-Za-z0-9 \-_]#', '', $string );
    }
    if ( $allow_accents && $allow_spaces ) {
      return preg_replace( '#[^A-Za-zà-źÀ-Ź0-9 \-_,]#', '', $string );
    }
  }

  public function number( $string, int $limit = 255 ) {
    $string = preg_replace( '#[^0-9]#', '', substr( $string, 0, $limit ) );
    return $string;
  }

  public function date_br( $string ) {
    $string = preg_replace( '#[^0-9\/]#', '', substr( $string, 0, 10 ) );
    return $string; // return number and /
  }
	
  public function datetime( $string ) {
    $string = preg_replace( '#[^0-9 -:]#', '', substr( $string, 0, 20 ) );
    return $string; // return number, - and :
  }

  public function hour( $string ) {
    $string = preg_replace( '#[^0-9\:]#', '', substr( $string, 0, 5 ) );
    return $string; // return number and :
  }

  public function text( $string, int $limit = 255 ) {
    $string = strip_tags( substr( str_replace( array('`', '´', '¨'), '', trim( $string ) ), 0, $limit ) );
    return $string;
  }

  public function base64( $string ) {
    $string = strip_tags( substr( str_replace( array( '"', "'", '`', '´', '¨' ), '', trim( $string ) ), 0, $limit ) );
    return $string;
  }
}
?>