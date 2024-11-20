<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idBitacora = $_POST['idBitacora'];
$rutProfesional = $_POST['rut'];
$usuario = $_POST['usuario'];

$query_qrBitacoraAdm = ("SELECT * FROM $MM_oirs_DATABASE.bitacora_administrativa WHERE ID_BITACORA = '$idBitacora'");
$qrBitacoraAdm = $oirs->SelectLimit($query_qrBitacoraAdm) or die($oirs->ErrorMsg());
$totalRows_qrBitacoraAdm = $qrBitacoraAdm->RecordCount();

$comentarioBitacora = utf8_encode($qrBitacoraAdm->Fields('BITACORA'));
$asunto = utf8_encode($qrBitacoraAdm->Fields('ASUNTO'));
$ruta = utf8_encode($qrBitacoraAdm->Fields('RUTA_DOCUMENTO'));
$programado = $qrBitacoraAdm->Fields('PROGRAMADO');
$fechaProgramacion = $qrBitacoraAdm->Fields('FECHA_PROGRAMACION');
$emisor = utf8_encode($qrBitacoraAdm->Fields('SESION'));//es quien esta compartiendo el mensaje

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');
$hora= date('G:i');

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.bitacora_administrativa SET COMPARTIDO=%s WHERE ID_BITACORA= '$idBitacora'",
            GetSQLValueString('si', "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_administrativa (SESION, BITACORA, ASUNTO, AUDITORIA, HORA, RUTA_DOCUMENTO, EMISOR, PROGRAMADO, FECHA_PROGRAMACION, ID_BITACORA_COMPARTIDO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
GetSQLValueString($rutProfesional, "text"),
GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
GetSQLValueString(utf8_decode($asunto), "text"),
GetSQLValueString($auditoria, "date"),
GetSQLValueString($hora, "date"),
GetSQLValueString($ruta, "text"),
GetSQLValueString($emisor, "text"),
GetSQLValueString($programado, "text"),
GetSQLValueString($fechaProgramacion, "date"),
GetSQLValueString($idBitacora, "int"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());



$asunto = 'Mensaje compartido';
$estadoNoti = 'nuevo';
$comentarioNotificacion = $usuario.' le ha compartido un mensaje, revise su bitacora administrativa';
$insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($rutProfesional, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString(utf8_decode($comentarioNotificacion), "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($estadoNoti, "text"));
$Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 
$Result2->Close(); 
$qrBitacoraAdm->Close(); 