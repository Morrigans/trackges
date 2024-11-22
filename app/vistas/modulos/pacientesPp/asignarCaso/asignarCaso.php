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
$idDerivacionPp = $_POST['idDerivacionPp'];
$slAsignarEnfermeriaDerivacion = $_POST['slAsignarEnfermeriaDerivacion'];

$query_qrAsignarEnfermeria= "SELECT * FROM $MM_oirs_DATABASE.login where ID = '$slAsignarEnfermeriaDerivacion'";
$qrAsignarEnfermeria = $oirs->SelectLimit($query_qrAsignarEnfermeria) or die($oirs->ErrorMsg());
$totalRows_qrAsignarEnfermeria = $qrAsignarEnfermeria->RecordCount();

$codRutPro = $qrAsignarEnfermeria->Fields('USUARIO');
$nomPro = $qrAsignarEnfermeria->Fields('NOMBRE');

$estado = 'pendiente';

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_pp SET ENFERMERA=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($codRutPro, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$nDerivacion = 'P0'.$idDerivacion;

// $query_qrDerivacion= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp where ID_DERIVACION = '$idDerivacion'";
// $qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
// $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

// $folio = $qrDerivacion->Fields('FOLIO');

$comentarioBitacora = 'Se asigna a '.$nomPro.' rut '.$codRutPro.' como gestor a derivación '.$nDerivacion;
$asunto= 'Gestor asignado';
$hora= date('G:i');



$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


$estadoNoti = 'nuevo';


$insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones_pp (USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, ID_DERIVACION) VALUES (%s, %s, %s, %s, %s, %s, %s)",    
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($estadoNoti, "text"),
    GetSQLValueString($idDerivacion, "int"));
$Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());




//#############################################################  guarda daTos ICRS##########################################################
if ($idDerivacionPp != '') {

    $query_qrDerivacion = "SELECT * FROM $MM_icrs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacionPp'";
    $qrDerivacion = $icrs->SelectLimit($query_qrDerivacion) or die($icrs->ErrorMsg());
    $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

    $gestoraPp = $qrDerivacion->Fields('ENFERMERA');

    $query_qrUltimaBitacora = ("SELECT max(ID_BITACORA) as ID_BITACORA FROM $MM_oirs_DATABASE.bitacora");
    $qrUltimaBitacora = $oirs->SelectLimit($query_qrUltimaBitacora) or die($oirs->ErrorMsg());
    $totalRows_qrUltimaBitacora = $qrUltimaBitacora->RecordCount();

    $ultimoIdBitacoraPp = $qrUltimaBitacora->Fields('ID_BITACORA');
    $nDerivacionPp = 'D0'.$idDerivacionPp;
    $comentarioBitacoraPp = 'Se asigna a '.$nomPro.' rut '.$codRutPro.' como gestor a derivación '.$nDerivacionPp;
    $asuntoPp = 'Derivacion asignada (D0'.$idDerivacionPp.')';


    $insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA,ID_BITACORA_REMOTO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacionPp, "int"),     
        GetSQLValueString('CRSS', "text"),
        GetSQLValueString($comentarioBitacoraPp, "text"),
        GetSQLValueString($asuntoPp, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($ultimoIdBitacoraPp, "int"));
    $Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());




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

echo 1;

