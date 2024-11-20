<?php
//Connection statement
require_once '../../../../../Connections/oirs.php';

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

$idDerivacion = $_REQUEST['idDerivacion'];
$motivoEliminaCanasta = $_REQUEST['motivoEliminaCanasta'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$nderivacion = $qrDerivacion->Fields('N_DERIVACION');

$idDerivacionCanasta = $_REQUEST['canasta'];
$rutPrestador = $_REQUEST['prestador']; 
$idBitacora = $_REQUEST['idBitacora']; 

$query_qrPrestadores = "SELECT * FROM $MM_oirs_DATABASE.prestador WHERE RUT_PRESTADOR = '$rutPrestador'";
$qrPrestadores = $oirs->SelectLimit($query_qrPrestadores) or die($oirs->ErrorMsg());
$totalRows_qrPrestadores = $qrPrestadores->RecordCount();

$idEtapaPatologia = $_REQUEST['idEtapaPatologia'];
$codEtapaPatologia = $_REQUEST['codEtapaPatologia'];

$query_select = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA='$codEtapaPatologia'";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount(); 

$descEtapaPatologia = utf8_encode($select->Fields('DESC_ETAPA_PATOLOGIA'));

$query_qrDescCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_CANASTA_PATOLOGIA='$idDerivacionCanasta'";
$qrDescCanastaPatologia = $oirs->SelectLimit($query_qrDescCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrDescCanastaPatologia = $qrDescCanastaPatologia->RecordCount(); 

$fechaInicioCanasta = $qrDescCanastaPatologia->Fields('FECHA_CANASTA');
$codCanastapatologia = $qrDescCanastaPatologia->Fields('CODIGO_CANASTA_PATOLOGIA');

$query_qrCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA='$codCanastapatologia'";
$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount(); 

$descCanastaPatologia = utf8_encode($qrCanastaPatologia->Fields('DESC_CANASTA_PATOLOGIA'));

$comentarioBitacora = 'Se Elimina canasta ['.$descCanastaPatologia.'] asignada a prestador ['.$qrPrestadores->Fields('DESC_PRESTADOR').'] con fecha de inicio ['.$fechaInicioCanasta.'] y fecha fin ['.$fechaFinCanasta.'] perteneciente a la etapa ['.$descEtapaPatologia.'] de la derivación número '.$nderivacion.', comentario: '.$motivoEliminaCanasta;
$asunto= 'Canasta Eliminada';
$hora= date('G:i');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$insertSQL = sprintf("DELETE FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_CANASTA_PATOLOGIA = '$idDerivacionCanasta'");
$Result1   = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());  

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.bitacora_pp SET RUTA_DOCUMENTO=%s WHERE ID_BITACORA = '$idBitacora'",
            GetSQLValueString("", "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg()); 

echo 1;

