<?php
//Connection statement
require_once '../../../Connections/oirs.php';
require_once '../../../Connections/icrs.php';
require_once '../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

$idBitacora = $_POST['idBitacora'];
$comparte = 'si';

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');
$hora= date('G:i');

$query_qrBitacora = "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE ID_BITACORA = '$idBitacora'";
$qrBitacora = $oirs->SelectLimit($query_qrBitacora) or die($oirs->ErrorMsg());
$totalRows_qrBitacora = $qrBitacora->RecordCount();

$idDerivacion = $qrBitacora->Fields('ID_DERIVACION');


$asunto = utf8_encode($qrBitacora->Fields('ASUNTO'));
$comentarioBitacora = utf8_encode($qrBitacora->Fields('BITACORA'));
$estadoNoti = 'nuevo';
$origen= 'CRSS';
$asuntoNoti = $asunto.' (R0'.$idDerivacion.')';

/////////ACTUALIZA ESTADO EN BITACORA///////////////
$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.bitacora SET COMPARTIDO_EXT=%s WHERE ID_BITACORA= '$idBitacora'",
            GetSQLValueString($comparte, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());


///////// GENERA NOTIFICACIONES EN EL INSTITUTO DEL CANCER ///////////////
//busco los perfiles de coordinador para asignarles la notificacion de nueva derivacion
   $query_qrBuscaCoordinador = "SELECT * FROM $MM_icrs_DATABASE.login WHERE (USUARIO='15.423.088-2' OR USUARIO='14.126.361-7')";
   $qrBuscaCoordinador = $icrs->SelectLimit($query_qrBuscaCoordinador) or die($icrs->ErrorMsg());
   $totalRows_qrBuscaCoordinador = $qrBuscaCoordinador->RecordCount(); 

   while (!$qrBuscaCoordinador->EOF) {

       $receptor = $qrBuscaCoordinador->Fields('USUARIO');
       
       $insertSQL2 = sprintf("INSERT INTO $MM_icrs_DATABASE.notificaciones (USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, ORIGEN) VALUES (%s, %s, %s, %s, %s, %s, %s)",
           GetSQLValueString($receptor, "text"),
           GetSQLValueString(utf8_decode($asuntoNoti), "text"),
           GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
           GetSQLValueString($auditoria, "date"),
           GetSQLValueString($hora, "date"),
           GetSQLValueString($estadoNoti, "text"),
           GetSQLValueString($origen, "text"));
       $Result2 = $icrs->Execute($insertSQL2) or die($icrs->ErrorMsg());

   $qrBuscaCoordinador->MoveNext(); }

echo 1;

$Result1->Close(); 