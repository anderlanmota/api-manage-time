<?php
##### VERSION 1 #####
$objResource = new Resource();
$prepare = $objResource->prepare();
$mainResource = @array_key_last( RESOURCES );

// verifica o recurso e chama a classe correta
switch ( $mainResource ) {
  case 'auth':
		$obj = new Auth();
		$arr = $obj->run();
		header( 'Content-Type: application/json' );
		echo json_encode( $arr );
    break;
  case 'users':
		$obj = new User();
		$arr = $obj->run();
		header( 'Content-Type: application/json' );
		echo json_encode( $arr );
    break;
  case 'projects':
		$obj = new Project();
		$arr = $obj->run();
		header( 'Content-Type: application/json' );
		echo json_encode( $arr );
    break;
  case 'times':
		$obj = new ManageTime();
		$arr = $obj->run();
		header( 'Content-Type: application/json' );
		echo json_encode( $arr );
    break;
case 'maintenance':
		$obj = new Auth();
		$arr = $obj->run();
		header( 'Content-Type: application/json' );
		echo json_encode( $arr );
    break;
  default:
    http_response_code( 404 );
    header( 'Content-Type: application/json' );
    echo json_encode( array( "message" => "Recurso não encontrado." ) );
}
?>