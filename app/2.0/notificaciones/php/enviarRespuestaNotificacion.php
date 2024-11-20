<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

// Solo se permite el ingreso con el inicio de sesion.
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: index.php');
exit; }

$usuario = $_SESSION['dni'];

$id = $_POST['id'];
$respuesta = $_POST['respuesta'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_notificaciones WHERE ID = '$id'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idDerivacion = $qrDerivacion->Fields('ID_DERIVACION');
$receptor = $qrDerivacion->Fields('USUARIO_EMISOR');
$idBitacora = $qrDerivacion->Fields('ID_BITACORA');
$asunto = utf8_encode($qrDerivacion->Fields('ASUNTO'));
date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');
$hora= date('G:i');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($respuesta), "text"),
    GetSQLValueString(utf8_decode($asunto), "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$query_select = ("SELECT max(ID_BITACORA) as ID_BITACORA FROM $MM_oirs_DATABASE.2_bitacora");
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();

$idBitacora = $select->Fields('ID_BITACORA');

$estadoNoti = 'nuevo';

 $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, ID_RESPUESTA, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"),
    GetSQLValueString($id, "int"),
    GetSQLValueString($receptor, "text"),// este es el rut del emisor original que recibira la respuesta
    GetSQLValueString(utf8_decode($asunto), "text"),
    GetSQLValueString(utf8_decode($respuesta), "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($estadoNoti, "text"),
    GetSQLValueString($usuario, "text"),// este es la sesion que esta respondiendo a emisor del mensaje original
    GetSQLValueString($idBitacora, "int"));
 $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());

 $query_qrIdUltNoti = ("SELECT max(ID) as ID_NOTIFICACION FROM $MM_oirs_DATABASE.2_notificaciones");
$qrIdUltNoti = $oirs->SelectLimit($query_qrIdUltNoti) or die($oirs->ErrorMsg());
$totalRows_qrIdUltNoti = $qrIdUltNoti->RecordCount();

$idNotificacion = $qrIdUltNoti->Fields('ID_NOTIFICACION');

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_notificaciones SET ID_RESPUESTA=%s WHERE ID= '$id'",
            GetSQLValueString($idNotificacion, "int"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg()); 