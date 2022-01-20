<?php
// set server timezone
date_default_timezone_set( 'UTC' );
 
// set some header settings
header( "Access-Control-Allow-Origin: *" );
header( 'Access-Control-Allow-Methods: *' );
header( "Access-Control-Allow-Headers: *" );
header( "Access-Control-Allow-Credentials: true" );
header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
header( "Cache-Control: post-check=0, pre-check=0", false );
header( "Pragma: no-cache" );

// allows checking if it is accessible without processing the entire system
if ( array_key_exists( 'REQUEST_METHOD', $_SERVER ) ) {
  if ( $_SERVER[ 'REQUEST_METHOD' ] == 'OPTIONS' ) {
    return 0;
  }
}

// before including a file of an instantiated class, check if it exists 
spl_autoload_register( function ( $classname ) {
  $classincluded = false;
  if ( !$classincluded ) {
    if ( file_exists( dirname( __FILE__ ) . "/core/" . $classname . '.php' ) ) {
      require_once dirname( __FILE__ ) . "/core/" . $classname . '.php';
      $classincluded = true;
    }
  }
  if ( !$classincluded ) {
    if ( file_exists( dirname( __FILE__ ) . "/model/" . $classname . '.php' ) ) {
      require_once dirname( __FILE__ ) . "/model/" . $classname . '.php';
      $classincluded = true;
    }
  }
} );

// check if the request url is valid
if ( !array_key_exists( 'PATH_INFO', $_SERVER ) ) {
  http_response_code( 400 );
  header( 'Content-Type: application/json' );
  echo json_encode( array( "message" => "Requisição inválida." ) );
  exit();
}

// check if the api version was informed
$elements = explode( '/', $_SERVER[ 'PATH_INFO' ] );
if ( !array_key_exists( '1', $elements ) ) {
  http_response_code( 400 );
  header( 'Content-Type: application/json' );
  echo json_encode( array( "message" => "A versão da API não foi informada." ) );
  exit();
}

// check if the api version exists
$version = strtolower( preg_replace( '#[^A-Za-z0-9]#', '', $elements[1] ) );
if ( !file_exists( dirname( __FILE__ ) . "/resource/" . $version . '.php' ) ) {
  http_response_code( 400 );
  header( 'Content-Type: application/json' );
  echo json_encode( array( "message" => "A versão solicitada não foi encontrada." ) );
  exit();
}

// include version file
require_once dirname( __FILE__ ) . "/resource/" . $version . '.php';
?>