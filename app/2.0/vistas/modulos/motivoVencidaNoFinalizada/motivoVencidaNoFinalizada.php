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
$slMotivoVencidaNoFinalizada = $_POST['slMotivoVencidaNoFinalizada'];

$estado = 'cerrada';

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET MOTIVO_VENCIDA_NO_FINALIZADA=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($slMotivoVencidaNoFinalizada, "int"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());


$query_qrMotivo= "SELECT * FROM $MM_oirs_DATABASE.motivo_vencida_no_finalizada WHERE ID_MOTIVO = '$slMotivoVencidaNoFinalizada'";
$qrMotivo = $oirs->SelectLimit($query_qrMotivo) or die($oirs->ErrorMsg());
$totalRows_qrMotivo = $qrMotivo->RecordCount();

$motivo = utf8_encode($qrMotivo->Fields('MOTIVO'));

$comentarioBitacora = 'A la derivacion R0'.$idDerivacion.' se le asigna el motivo de vencimiento: '.$motivo;
$asunto= 'Cierre pendiente';
$hora= date('G:i');

$idUsuario = $_SESSION['idUsuario'];

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


echo 1;

