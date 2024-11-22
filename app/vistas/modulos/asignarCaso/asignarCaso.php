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
$nomPro = $qrAsignarEnfermeria->Fields('NOMBRE');

$estado = 'pendiente';

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET ENFERMERA=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($slAsignarEnfermeriaDerivacion, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$nDerivacion = 'R'.$idDerivacion;

$query_qrDerivacion= "SELECT * FROM $MM_oirs_DATABASE.derivaciones where ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$folio = $qrDerivacion->Fields('FOLIO');

$comentarioBitacora = 'Se asigna a '.$nomPro.' rut '.$codRutPro.' como gestor a derivación '.$nDerivacion.' Folio Rigth Now '.$folio;
$asunto= 'Gestor asignado';
$hora= date('G:i');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$asunto = 'Derivacion asignada';
$estadoNoti = 'nuevo';

$insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"),
    GetSQLValueString($codRutPro, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($estadoNoti, "text"));
$Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
echo 1;

