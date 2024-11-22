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

$idDerivacion = $_POST['idDerivacion'];
$tipoUsuario = $_POST['tipoUsuario'];
$idPrgPab = $_POST['idPrgPab'];
$slMotivo = $_POST['slMotivo'];
// $comentarioCenso = $_POST['comentarioCenso'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');


    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.api_prog_pabellones SET MOTIVO_RETRASO=%s WHERE ID= '$idPrgPab'",
            GetSQLValueString($slMotivo, "text"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());


$comentarioBitacora = 'Se ha ingresado motivo de retraso de programación de pabellón: '. $slMotivo;
$asunto= 'Motivo retraso prg pab';
    
    echo 1;

$hora= date('G:i');

$idUsuario = $_SESSION['idUsuario'];

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
 
