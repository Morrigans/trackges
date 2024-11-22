<?php
//Connection statement
require_once '../../../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

$idDerivacion = $_REQUEST['idDerivacion'];
$comentarioCambioEstadoCanasta = $_REQUEST['comentarioCambioEstadoCanasta'];
$fechaFinCanasta = $_REQUEST['fechaFinCanasta'];
$observacion = $_REQUEST['comentarioCambioEstadoCanasta'];
$motivoFinCanasta = $_REQUEST['slMotivoFinCanasta'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$nderivacion = $qrDerivacion->Fields('N_DERIVACION');

$idDerivacionCanasta = $_REQUEST['canasta'];
$rutPrestador = $_REQUEST['prestador']; 

$query_qrPrestadores = "SELECT * FROM $MM_oirs_DATABASE.prestador WHERE RUT_PRESTADOR = '$rutPrestador'";
$qrPrestadores = $oirs->SelectLimit($query_qrPrestadores) or die($oirs->ErrorMsg());
$totalRows_qrPrestadores = $qrPrestadores->RecordCount();

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_canastas SET OBSERVACION=%s, MOTIVO_FIN_CANASTA=%s WHERE ID_CANASTA_PATOLOGIA = '$idDerivacionCanasta'",
            GetSQLValueString(utf8_decode($observacion), "text"),
            GetSQLValueString($motivoFinCanasta, "int"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$idEtapaPatologia = $_REQUEST['idEtapaPatologia'];
$codEtapaPatologia = $_REQUEST['codEtapaPatologia'];

$query_select = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA='$codEtapaPatologia'";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount(); 

$descEtapaPatologia = utf8_encode($select->Fields('DESC_ETAPA_PATOLOGIA'));

$query_qrDescCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_CANASTA_PATOLOGIA='$idDerivacionCanasta'";
$qrDescCanastaPatologia = $oirs->SelectLimit($query_qrDescCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrDescCanastaPatologia = $qrDescCanastaPatologia->RecordCount(); 

$fechaInicioCanasta = $qrDescCanastaPatologia->Fields('FECHA_CANASTA');
$codCanastapatologia = $qrDescCanastaPatologia->Fields('CODIGO_CANASTA_PATOLOGIA');

$query_qrCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA='$codCanastapatologia'";
$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount(); 

$descCanastaPatologia = utf8_encode($qrCanastaPatologia->Fields('DESC_CANASTA_PATOLOGIA'));

$query_qrMotivo = "SELECT * FROM $MM_oirs_DATABASE.motivos_fin_canastas WHERE ID_MOTIVO='$motivoFinCanasta'";
$qrMotivo = $oirs->SelectLimit($query_qrMotivo) or die($oirs->ErrorMsg());
$totalRows_qrMotivo = $qrMotivo->RecordCount(); 

$nomMotivo = $qrMotivo->Fields(DESC_MOTIVO);

    $comentarioBitacora = 'Se agrega motivo vencimiento canasta ['.$descCanastaPatologia.'] asignada a prestador ['.utf8_encode($qrPrestadores->Fields('DESC_PRESTADOR')).'] con fecha de inicio ['.date("d-m-Y", strtotime($fechaInicioCanasta)).'] perteneciente a la etapa ['.$descEtapaPatologia.'] de la derivación número '.$nderivacion.', comentario: '.$observacion.', Motivo canasta vencida: '.$nomMotivo;

$asunto= 'Motivo Vencimiento';
$hora= date('G:i');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


echo 1;

