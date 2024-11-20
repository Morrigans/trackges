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

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

$idDerivacion = $_POST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idEtapaPatologia = $_POST['slEtapaPatologiaDerivacion'];

$query_select = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE ID_ETAPA_PATOLOGIA='$idEtapaPatologia'";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount(); 

$descEtapaPatologia = utf8_encode($select->Fields('DESC_ETAPA_PATOLOGIA'));
$codEtapaPatologia = $select->Fields('CODIGO_ETAPA_PATOLOGIA');

$nderivacion = $qrDerivacion->Fields('N_DERIVACION');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones_etapas (ID_DERIVACION, N_DERIVACION, CODIGO_ETAPA_PATOLOGIA, SESION, AUDITORIA) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"), 
    GetSQLValueString($nderivacion, "text"), 
    GetSQLValueString($codEtapaPatologia, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($auditoria, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$comentarioBitacora = 'Se agrega Etapa ['.$descEtapaPatologia.'] a la derivación número '.$nderivacion;
$asunto= 'Etapa agregada';
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

