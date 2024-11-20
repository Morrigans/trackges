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

$idDerivacion = $_POST['idDerivacion'];
$slAsignarPrestadorDerivacion = $_POST['slAsignarPrestadorDerivacion'];
$canasta = $_POST['canasta'];

$estado = 'prestador';  

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET ESTADO=%s, RUT_PRESTADOR=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($estado, "text"),
            GetSQLValueString($slAsignarPrestadorDerivacion, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

if ($canasta > 0) {
    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_canastas SET RUT_PRESTADOR=%s WHERE ID_DERIVACION = '$idDerivacion'",
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

$idUsuario = $_SESSION['idUsuario'];

//inserto estado en tabla de estados**********************************************************************************************************************************
    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.estados_derivacion (ID_DERIVACION, ESTADO, FECHA_REGISTRO, HORA_REGISTRO, SESION) VALUES (%s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacion, "int"), 
        GetSQLValueString($estado, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($idUsuario, "int"));
        $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
//********************************************************************************************************************************************************************

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


echo 1;

