<?php 
##### VERSION 1 #####
$objResource = new Resource();
$prepare = $objResource->prepare();


if ($prepare) {
	echo "sucesso";
} else {
	echo "error";
}
?>