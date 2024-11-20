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
$hora= date('G:i');

$comentarioBitacora = $_POST['comentarioBitacora'];
$asunto= $_POST['slTipoRegistroBitacora'];



$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_administrativa (SESION, BITACORA, ASUNTO, AUDITORIA, HORA, EMISOR) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString(utf8_decode($asunto), "text"),
    GetSQLValueString($auditoria, "date"),
	GetSQLValueString($hora, "date"),
    GetSQLValueString($usuario, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 