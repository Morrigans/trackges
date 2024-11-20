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

$profesion = $_POST['profesion'];
$pagoProfesional = $_POST['pagoProfesional'];
$colorProfesion = $_POST['colorProfesion'];


$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.profesion (PROFESION, VALOR_PAGO, COLOR) VALUES (%s, %s, %s)",
    GetSQLValueString(utf8_decode($profesion), "text"),
    GetSQLValueString($pagoProfesional, "int"),
    GetSQLValueString($colorProfesion, "text")); 
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 