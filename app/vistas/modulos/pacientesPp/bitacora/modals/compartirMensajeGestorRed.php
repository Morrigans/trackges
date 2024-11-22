<?php
//Connection statement
require_once '../../../../../Connections/oirs.php';
require_once '../../../../../Connections/icrs.php';

//Aditional Functions
require_once '../../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');
$hora= date('G:I');

$idDerivacion = $_REQUEST['idDerivacion'];
$idBitacora = $_REQUEST['idBitacora'];
$ruta = $_REQUEST['ruta'];
$comentarioBitacora = $_REQUEST['comentarioBitacora'];
$asunto = $_REQUEST['asunto'];
$rutaAudio = $_REQUEST['rutaAudio'];
$gestorRed = $_REQUEST['gestorRed'];

if ($gestorRed == 49) { //si gestor de red es icrs

    //obtengo el id de la derivacion del lado de icrs

    $query_qrDerivacionCrss = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
    $qrDerivacionCrss = $oirs->SelectLimit($query_qrDerivacionCrss) or die($oirs->ErrorMsg());
    $totalRows_qrDerivacionCrss = $qrDerivacionCrss->RecordCount();

    $idDerivacionIcrs = $qrDerivacionCrss->Fields('ID_DERIVACION_PP');

    $query_qrDerivacionIcrs = "SELECT * FROM $MM_icrs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacionIcrs'";
    $qrDerivacionIcrs = $icrs->SelectLimit($query_qrDerivacionIcrs) or die($icrs->ErrorMsg());
    $totalRows_qrDerivacionIcrs = $qrDerivacionIcrs->RecordCount();

    $receptor = $qrDerivacionIcrs->Fields('ENFERMERA');

  $insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, RUTA_DOCUMENTO, RUTA_AUDIO, AUDITORIA, HORA, ID_BITACORA_REMOTO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacionIcrs, "int"), 
        GetSQLValueString('CRSS', "text"),
        GetSQLValueString($comentarioBitacora, "text"),
        GetSQLValueString($asunto, "text"),
        GetSQLValueString($ruta, "text"),
        GetSQLValueString($rutaAudio, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($idBitacora, "text"));
    $Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());

    
    $estadoNoti = 'nuevo';
    $asunto= $asunto.' (D0'.$idDerivacionIcrs.')';

     $insertSQL2 = sprintf("INSERT INTO $MM_icrs_DATABASE.notificaciones (USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO) VALUES (%s, %s, %s, %s, %s, %s)",
         GetSQLValueString($receptor, "text"),
         GetSQLValueString($asunto, "text"),
         GetSQLValueString($comentarioBitacora, "text"),
         GetSQLValueString($auditoria, "date"),
         GetSQLValueString($hora, "date"),
         GetSQLValueString($estadoNoti, "text"));
     $Result2 = $icrs->Execute($insertSQL2) or die($icrs->ErrorMsg());


}

echo 1;

