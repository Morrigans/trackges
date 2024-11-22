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

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$folio = $qrDerivacion->Fields('FOLIO');

$idDerivacionRedGes = $qrDerivacion->Fields('ID_DERIVACION_REDGES');

$prof = $_POST['prof'];

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.team_gestion (ID_DERIVACION, ID_PROFESIONAL) VALUES (%s, %s)",
    GetSQLValueString($idDerivacion, "text"),
    GetSQLValueString($prof, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$query_qrProfesional = "SELECT * FROM $MM_oirs_DATABASE.login WHERE ID = '$prof'";
$qrProfesional = $oirs->SelectLimit($query_qrProfesional) or die($oirs->ErrorMsg());
$totalRows_qrProfesional = $qrProfesional->RecordCount();

$nomProfesional = $qrProfesional->Fields('NOMBRE');
$tipoProfesion = $qrProfesional->Fields('TIPO');
$codRutPro = $qrProfesional->Fields('USUARIO');

if ($tipoProfesion == '6') {
    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET TENS=%s WHERE ID_DERIVACION= '$idDerivacion'",
                GetSQLValueString($prof, "int"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());
}

if ($tipoProfesion == '4') {
    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET ADMINISTRATIVA=%s WHERE ID_DERIVACION= '$idDerivacion'",
                GetSQLValueString($prof, "int"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());
}

$query_qrProfesion = "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '$tipoProfesion'";
$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
$totalRows_qrProfesion = $qrProfesion->RecordCount();

$profesionSinGenero = $qrProfesion->Fields('PROFESION');


$comentarioBitacora = 'El profesional '.$nomProfesional.' rut: '.$codRutPro.' de profesion: '.$profesionSinGenero.', fue asignado a la derivacion numero D0'.$idDerivacion.' Folio Rigth Now '.$folio;
$asunto= 'Profesional Asignado';
$hora= date('G:i');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

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

$Result1->Close(); 

