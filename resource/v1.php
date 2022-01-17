<?php 
##### VERSION 1 #####
$objResource = new Resource();
$prepare = $objResource->prepare();

$mainResource = @array_key_last( RESOURCES );

if ($prepare) {
	echo "s $mainResource";
} else {
	echo "error";
}
?>