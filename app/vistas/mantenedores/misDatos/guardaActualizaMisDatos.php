<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

$nomProfesionalEd = $_POST['nomProfesionalEd'];
$correoProfesionalEd = $_POST['correoProfesionalEd'];
$fonoProfesionalEd = $_POST['fonoProfesionalEd'];
$nuevaPassEd = $_POST['nuevaPassEd'];
$confirmaPassEd = $_POST['confirmaPassEd'];

$options = array("cost"=>4);
$hashPassword = password_hash($nuevaPassEd,PASSWORD_BCRYPT,$options);

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.login SET NOMBRE='$nomProfesionalEd', MAIL='$correoProfesionalEd', FONO='$fonoProfesionalEd', PASS='$hashPassword' WHERE USUARIO='$usuario'");
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

// $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.login (NOMBRE, MAIL, FONO, PASS) VALUES (%s, %s, %s, %s)",
//     GetSQLValueString($nomProfesionalEd, "text"),
//     GetSQLValueString($correoProfesionalEd, "text"),
//     GetSQLValueString($fonoProfesionalEd, "text"),
// 	GetSQLValueString($hashPassword, "text"));
// $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 