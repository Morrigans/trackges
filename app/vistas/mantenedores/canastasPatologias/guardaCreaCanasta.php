<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

$slTipoPatologia = $_POST['slTipoPatologia'];
$slPatologia = $_POST['slPatologia'];
$slEtapaPatologia = $_POST['slEtapaPatologia'];
$nombreCanastaPatologia = $_POST['nombreCanastaPatologia'];
$diasLimiteCanasta = $_POST['diasLimiteCanasta'];
$obsCanastaPatologia = $_POST['obsCanastaPatologia'];

$query_canasta = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_ETAPA_PATOLOGIA='$slEtapaPatologia'";
$canasta = $oirs->SelectLimit($query_canasta) or die($oirs->ErrorMsg());
$totalRows_canasta = $canasta->RecordCount();

$codigoCanasta= $slEtapaPatologia.'.'.($totalRows_canasta+1);

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.patologia (CODIGO_CANASTA_PATOLOGIA, DESC_CANASTA_PATOLOGIA, CODIGO_ETAPA_PATOLOGIA, CODIGO_PATOLOGIA, TIEMPO_LIMITE, OBSERVACION) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($codigoCanasta, "text"),
    GetSQLValueString($nombreCanastaPatologia, "text"),
    GetSQLValueString($slEtapaPatologia, "text"),
	GetSQLValueString($slPatologia, "text"),
	GetSQLValueString($diasLimiteCanasta, "int"),
	GetSQLValueString($obsCanastaPatologia, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 