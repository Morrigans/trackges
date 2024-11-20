<?php
//Connection statement
require_once '../Connections/oirs.php';
//Aditional Functions
require_once '../includes/functions.inc.php';

//capturo el usuario que inicio sesion desde validaLogin
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.login SET PASS_SIN='$password' WHERE USUARIO ='$username'");
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

echo 1;
?>