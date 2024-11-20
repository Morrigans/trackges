<?php
//Connection statement
require_once '../Connections/oirs.php';
//Aditional Functions
require_once '../includes/functions.inc.php';

header("Content-Type: text/html;charset=utf-8");

$usuarioSesion = $_POST['usuarioSesion'];
$nuevaPass = $_POST['nuevaPass'];
$confirmaPass = $_POST['confirmaPass'];

$options = array("cost"=>4);
$hashPassword = password_hash($nuevaPass,PASSWORD_BCRYPT,$options);

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.login SET PASS='$hashPassword' WHERE USUARIO='$usuarioSesion'");
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

echo 1;
$Result1->Close(); 