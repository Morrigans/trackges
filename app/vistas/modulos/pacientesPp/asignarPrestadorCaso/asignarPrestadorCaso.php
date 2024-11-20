<?php
//Connection statement
require_once '../../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

$idDerivacion = $_POST['idDerivacion'];
$slAsignarPrestadorDerivacion = $_POST['slAsignarPrestadorDerivacion'];
$canasta = $_POST['canasta'];

$estado = 'prestador';  

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_pp SET ESTADO=%s, RUT_PRESTADOR=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($estado, "text"),
            GetSQLValueString($slAsignarPrestadorDerivacion, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

if ($canasta > 0) {
    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_canastas_pp SET RUT_PRESTADOR=%s WHERE ID_DERIVACION = '$idDerivacion'",
            GetSQLValueString($slAsignarPrestadorDerivacion, "text"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());
}

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.prestador_asignado (ID_DERIVACION, RUT_PRESTADOR, SESION, AUDITORIA) VALUES (%s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"),
    GetSQLValueString($slAsignarPrestadorDerivacion, "text"),
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($auditoria, "date"));
$Result2 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$comentarioBitacora = $_POST['comentarioBitacoraAsignarPrestadorCaso']; 
$asunto= 'Prestador asignado';
$hora= date('G:i');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


echo 1;

