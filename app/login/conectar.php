<?php 
$conn = mysqli_connect("database-1.cxgs8m8wkp0r.us-east-1.rds.amazonaws.com","redges","oBll76w6o7<S","redgescl_cli_santiago");
 
if(!$conn){
	die("Connection error: " . mysqli_connect_error());	
}
?>
