<?php 
$conn = mysqli_connect("localhost","root","tu_clave","tu_base_de_datos");
 
if(!$conn){
	die("Connection error: " . mysqli_connect_error());	
}
?>
