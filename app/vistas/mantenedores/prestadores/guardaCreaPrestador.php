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

//$rutNuevoPrestador = $_POST['rutNuevoPrestador'];
$nombrePrestador = $_POST['nombrePrestador'];

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.prestador (DESC_PRESTADOR) VALUES (%s)",
    GetSQLValueString(utf8_decode($nombrePrestador), "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

// $nombrePrestador = $_POST['nombrePrestador'];
// $nombrePrestador = $_POST['nombrePrestador'];
// $nombrePrestador = $_POST['nombrePrestador'];
// $nombrePrestador = $_POST['nombrePrestador'];
// $nombrePrestador = $_POST['nombrePrestador'];

// $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.prestador (DESC_PRESTADOR, DIRECCION, COMUNA, REGION, RUT_FACTURACION) VALUES (%s,%s,%s,%s,%s)",
//     GetSQLValueString(utf8_decode($nombrePrestador), "text"),
// 	GetSQLValueString(utf8_decode($direccion), "text"),
// 	GetSQLValueString(utf8_decode($comuna), "text"),
// 	GetSQLValueString(utf8_decode($region), "text"),
// 	GetSQLValueString(utf8_decode($rutFacturacion), "text"));
// $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$query_select = ("SELECT max(ID_PRESTADOR) as ID_PRESTADOR FROM $MM_oirs_DATABASE.prestador");
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();

$idPrestador = $select->Fields('ID_PRESTADOR');

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.prestador SET RUT_PRESTADOR='$idPrestador' WHERE ID_PRESTADOR='$idPrestador'");
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 