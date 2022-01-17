<?php
// define o fuso horário do servidor
date_default_timezone_set( 'UTC' );

// define algumas configurações do header
header( "Access-Control-Allow-Origin: *" );
header( 'Access-Control-Allow-Methods: *' );
header( "Access-Control-Allow-Headers: *" );
header( "Access-Control-Allow-Credentials: true" );
header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
header( "Cache-Control: post-check=0, pre-check=0", false );
header( "Pragma: no-cache" );

// permite a verificação se está acessível sem processar todo o sistema
if ( array_key_exists( 'REQUEST_METHOD', $_SERVER ) ) {
  if ( $_SERVER[ 'REQUEST_METHOD' ] == 'OPTIONS' ) {
    return 0;
  }
}

// antes de incluir um arquivo de uma classe instanciada, verifica se ele existe
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

// verifica se a url da requisição é valida
if ( !array_key_exists( 'PATH_INFO', $_SERVER ) ) {
  http_response_code( 400 );
  header( 'Content-Type: application/json' );
  echo json_encode( array( "message" => "Requisição inválida." ) );
  exit();
}

// verifica se a versão da api foi informada
$elements = explode( '/', $_SERVER[ 'PATH_INFO' ] );
if ( !array_key_exists( '1', $elements ) ) {
  http_response_code( 400 );
  header( 'Content-Type: application/json' );
  echo json_encode( array( "message" => "A versão da API não foi informada." ) );
  exit();
}

// verifica se a versão da api existe
$version = strtolower( preg_replace( '#[^A-Za-z0-9]#', '', $version ) );
if ( !file_exists( dirname( __FILE__ ) . "/resource/" . $version . '.php' ) ) {
  http_response_code( 400 );
  header( 'Content-Type: application/json' );
  echo json_encode( array( "message" => "A versão solicitada não foi encontrada." ) );
  exit();
}

// inclui o arquivo da versão
require_once dirname( __FILE__ ) . "/resource/" . $version . '.php';
?>