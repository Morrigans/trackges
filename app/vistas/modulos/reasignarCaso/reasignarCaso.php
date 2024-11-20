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
$slAsignarEnfermeriaDerivacion = $_POST['slAsignarEnfermeriaDerivacion'];

$query_qrAsignarEnfermeria= "SELECT * FROM $MM_oirs_DATABASE.login where ID = '$slAsignarEnfermeriaDerivacion'";
$qrAsignarEnfermeria = $oirs->SelectLimit($query_qrAsignarEnfermeria) or die($oirs->ErrorMsg());
$totalRows_qrAsignarEnfermeria = $qrAsignarEnfermeria->RecordCount();

$codRutPro = $qrAsignarEnfermeria->Fields('USUARIO');

$estado = 'pendiente';
$reasignada = 'si';

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET ESTADO=%s, ENFERMERA=%s, REASIGNADA=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($estado, "text"),
            GetSQLValueString($slAsignarEnfermeriaDerivacion, "text"),
            GetSQLValueString($reasignada, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$comentarioBitacora = $_POST['comentarioBitacoraReasignarCaso'];
$asunto= 'Reasignada';
$hora= date('G:i');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$asunto = 'Derivacion reasignada';
$estadoNoti = 'nuevo';

$insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"),
    GetSQLValueString($codRutPro, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($estadoNoti, "text"));
$Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
echo 1;

