<?php
//Connection statement
require_once '../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

$idBitacora = $_POST['idBitacora'];
$fechaRecordatorio = $_POST['fechaRecordatorio'];
$usuarioReceptor = $_POST['usuarioReceptor'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');
$hora= date('G:i');

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_bitacora SET PROGRAMADO=%s  WHERE ID_BITACORA= '$idBitacora'",
            GetSQLValueString('si', "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());


$query_qrBitacora= "SELECT * FROM $MM_oirs_DATABASE.2_bitacora WHERE ID_BITACORA = '$idBitacora'";
$qrBitacora = $oirs->SelectLimit($query_qrBitacora) or die($oirs->ErrorMsg());
$totalRows_qrBitacora = $qrBitacora->RecordCount();

$mensaje = $qrBitacora->Fields('BITACORA');
$estado = 'activa';


$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_alarmas (ID_BITACORA, MENSAJE, USUARIO_EMISOR, USUARIO_RECEPTOR, ESTADO, FECHA_ALARMA, FECHA_REGISTRO, HORA_REGISTRO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idBitacora, "int"), 
    GetSQLValueString($mensaje, "text"),
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($usuarioReceptor, "text"),
    GetSQLValueString($estado, "text"),
    GetSQLValueString($fechaRecordatorio, "date"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 