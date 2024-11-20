<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
require_once '../../../../Connections/icrs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

$idDerivacion = $_POST['idDerivacion'];
$prof = $_POST['rutPro'];
$id = $_POST['id'];


$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idDerivacionPp = $qrDerivacion->Fields('ID_DERIVACION_PP');

$insertSQL = sprintf("DELETE from $MM_oirs_DATABASE.team_gestion_pp where ID_TEAM='$id'");
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$query_qrProfesional = "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$prof'";
$qrProfesional = $oirs->SelectLimit($query_qrProfesional) or die($oirs->ErrorMsg());
$totalRows_qrProfesional = $qrProfesional->RecordCount();

$nomProfesional = utf8_encode($qrProfesional->Fields('NOMBRE'));
$tipoProfesion = $qrProfesional->Fields('TIPO');
$codRutPro = $qrProfesional->Fields('USUARIO');

$comentarioBitacora = 'El profesional '.$nomProfesional.' rut: '.$prof.' fue desasociado del team de gestión de la derivacion numero P0'.$idDerivacion;
$asunto= 'Profesional desasociado';
$hora= date('G:i');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());



//#############################################################  guarda daTos ICRS##########################################################



$query_qrDerivacionPp = "SELECT * FROM $MM_icrs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacionPp'";
$qrDerivacionPp = $icrs->SelectLimit($query_qrDerivacionPp) or die($icrs->ErrorMsg());
$totalRows_qrDerivacionPp = $qrDerivacionPp->RecordCount();

$gestora = $qrDerivacionPp->Fields('ENFERMERA');

$query_qrUltimaBitacora = ("SELECT max(ID_BITACORA) as ID_BITACORA FROM $MM_oirs_DATABASE.bitacora_pp");
$qrUltimaBitacora = $oirs->SelectLimit($query_qrUltimaBitacora) or die($oirs->ErrorMsg());
$totalRows_qrUltimaBitacora = $qrUltimaBitacora->RecordCount();

$ultimoIdBitacoraPp = $qrUltimaBitacora->Fields('ID_BITACORA');

$comentarioBitacoraPp = 'El profesional '.$nomProfesional.' rut: '.$prof.' fue desasociado del team de gestión de la derivacion numero D0'.$idDerivacionPp;

$insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA,ID_BITACORA_REMOTO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacionPp, "int"),     
    GetSQLValueString('CRSS', "text"),
    GetSQLValueString(utf8_decode($comentarioBitacoraPp), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($ultimoIdBitacoraPp, "int"));
$Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());


$asuntoPp= 'Profesional desasociado (D0'.$idDerivacionPp.')';
$estadoNoti='nuevo';

$insertSQL2 = sprintf("INSERT INTO $MM_icrs_DATABASE.notificaciones ( USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO,ORIGEN) VALUES ( %s, %s, %s, %s, %s, %s, %s)",
  
    GetSQLValueString($gestora, "text"),
    GetSQLValueString($asuntoPp, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacoraPp), "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($estadoNoti, "text"),
    GetSQLValueString('CRSS', "text"));
$Result2 = $icrs->Execute($insertSQL2) or die($icrs->ErrorMsg());




echo 1;

$Result1->Close();