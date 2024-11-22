|<?php
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

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idDerivacionPp = $qrDerivacion->Fields('ID_DERIVACION_PP');
$folio = $qrDerivacion->Fields('FOLIO');
$idDerivacionRedGes = $qrDerivacion->Fields('ID_DERIVACION_REDGES');
$prof = $_POST['prof'];

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.team_gestion_pp (ID_DERIVACION, ID_PROFESIONAL) VALUES (%s, %s)",
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
    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_pp SET TENS=%s WHERE ID_DERIVACION= '$idDerivacion'",
                GetSQLValueString($prof, "int"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());
}

if ($tipoProfesion == '4') {
    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_pp SET ADMINISTRATIVA=%s WHERE ID_DERIVACION= '$idDerivacion'",
                GetSQLValueString($prof, "int"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());
}

$query_qrProfesion = "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '$tipoProfesion'";
$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
$totalRows_qrProfesion = $qrProfesion->RecordCount();

$profesionSinGenero = $qrProfesion->Fields('PROFESION');


$comentarioBitacora = 'El profesional '.$nomProfesional.' rut: '.$codRutPro.' de profesion: '.$profesionSinGenero.', fue asignado a la derivacion numero D0'.$idDerivacion;
$asunto= 'Profesional Asignado';

$hora= date('G:i');



$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION,ID_DERIVACION_PRESTADOR, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"), 
    GetSQLValueString($idDerivacionPp, "int"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$estadoNoti = 'nuevo';

$insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones_pp ( USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, ID_DERIVACION) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    
    GetSQLValueString($codRutPro, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($estadoNoti, "text"),
    GetSQLValueString($idDerivacion, "int"));
$Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());

//###############################INSERTA DATOS EN ICRS    ##############################################################
if ($idDerivacionPp != '') {
    $comentarioBitacoraPp = 'El profesional '.$nomProfesional.' rut: '.$codRutPro.' de profesion: '.$profesionSinGenero.', fue asignado a la derivacion numero P0'.$idDerivacionPp;
    $asunto= 'Profesional Asignado';
    $hora= date('G:i');

    $query_qrDerivacion = "SELECT * FROM $MM_icrs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacionPp'";
    $qrDerivacion = $icrs->SelectLimit($query_qrDerivacion) or die($icrs->ErrorMsg());
    $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

    $gestoraPp = $qrDerivacion->Fields('ENFERMERA');

    $query_qrUltimaBitacora = ("SELECT max(ID_BITACORA) as ID_BITACORA FROM $MM_oirs_DATABASE.bitacora_pp");
    $qrUltimaBitacora = $oirs->SelectLimit($query_qrUltimaBitacora) or die($oirs->ErrorMsg());
    $totalRows_qrUltimaBitacora = $qrUltimaBitacora->RecordCount();

    $ultimoIdBitacoraPp = $qrUltimaBitacora->Fields('ID_BITACORA');

    $insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA,ID_BITACORA_REMOTO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacionPp, "int"),     
        GetSQLValueString('CRSS', "text"),
        GetSQLValueString($comentarioBitacoraPp, "text"),
        GetSQLValueString($asunto, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($ultimoIdBitacoraPp, "int"));
    $Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());


    $asuntoPp= 'Profesional Asignado  (D0'.$idDerivacionPp.')';

    $insertSQL2 = sprintf("INSERT INTO $MM_icrs_DATABASE.notificaciones ( USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO,ORIGEN) VALUES ( %s, %s, %s, %s, %s, %s, %s)",
      
        GetSQLValueString($gestoraPp, "text"),
        GetSQLValueString($asuntoPp, "text"),
        GetSQLValueString($comentarioBitacoraPp, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($estadoNoti, "text"),
        GetSQLValueString('CRSS', "text"));
    $Result2 = $icrs->Execute($insertSQL2) or die($icrs->ErrorMsg());
}
    //

echo 1;

$Result1->Close(); 

