<?php
abstract class Sanitizer {

  protected function sanitizer_email( $email ) {
    $email = strtolower( $email );
    $email = trim( $email );
    $email = substr( $email, 0, 255 );
    $email = filter_var( $email, FILTER_SANITIZE_EMAIL );
    return $email;
  }

  protected function sanitizer_alphabetic( $string, bool $allow_accents = true, bool $allow_spaces = false, int $limit = 255 ) {
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

  protected function sanitizer_alphanumeric( $valor, bool $allow_accents = true, bool $allow_spaces = false, int $limit = 255 ) {
    $valor = str_replace( array( '"', "'", '`', '´', '¨' ), '', trim( $valor ) );
    $valor = substr( $valor, 0, $limit );
    if ( !$allow_accents && !$allow_spaces ) {
      return preg_replace( '#[^A-Za-z0-9]#', '', $valor );
    }
    if ( $allow_accents && !$allow_spaces ) {
      return preg_replace( '#[^A-Za-zà-źÀ-Ź0-9]#', '', $valor );
    }
    if ( !$allow_accents && $allow_spaces ) {
      return preg_replace( '#[^A-Za-z0-9 \-_]#', '', $valor );
    }
    if ( $allow_accents && $allow_spaces ) {
      return preg_replace( '#[^A-Za-zà-źÀ-Ź0-9 \-_,]#', '', $valor );
    }
  }

  protected function sanitizer_number( $valor, int $limit = 255 ) {
    $valor = preg_replace( '#[^0-9]#', '', substr( $valor, 0, $limit ) );
    return $valor;
  }

  protected function sanitizer_date_br( $valor ) { // dd/mm/yyyy
    $valor = preg_replace( '#[^0-9\/]#', '', substr( $valor, 0, 10 ) );
    return $valor;
  }

  protected function sanitizer_hour( $string ) { // hh:mm
    $string = preg_replace( '#[^0-9\:]#', '', substr( $string, 0, 5 ) );
    return $string;
  }

  protected function sanitizer_text( $valor, int $limit = 255 ) {
    $valor = str_replace( array( '"', "'", '`', '´', '¨' ), '', trim( $valor ) );
    $valor = substr( $valor, 0, $limit );
    $valor = strip_tags( $valor );
    return $valor;
  }

  protected function sanitizer_base64( $valor ) {
    $valor = str_replace( array( '"', "'", '`', '´', '¨' ), '', trim( $valor ) );
    $valor = substr( $valor, 0, $limit );
    $valor = strip_tags( $valor );
    return $valor;
  }

  protected function sanitizer_money( $valor ) {
    $valor = preg_replace( '/\D/', '', $valor );
    if ( strlen( $valor ) < 3 ) {
      $valor = substr( $valor, 0, strlen( $valor ) ) . '.00';
      return ( float )$valor;
    }
    if ( strlen( $valor ) > 2 ) {
      $valor = substr( $valor, 0, ( strlen( $valor ) - 2 ) ) . '.' . substr( $valor, ( strlen( $valor ) - 2 ) );
      return ( float )$valor;
    }
  }

  protected function sanitizer_url( $valor ) {
    $valor = strip_tags( str_replace( array( '"', "'", '`', '´', '¨' ), '', trim( $valor ) ) );
    return filter_var( $valor, FILTER_SANITIZE_URL );
  }
}
?>