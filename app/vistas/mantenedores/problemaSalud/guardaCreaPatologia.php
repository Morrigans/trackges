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

$descripcionPatologia = $_POST['descripcionPatologia'];
$codigoPatologia = $_POST['codigoPatologia'];
$slTipoPatologia = $_POST['slTipoPatologia'];
$diasDeVigencia = $_POST['diasDeVigencia'];

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.patologia (DESC_PATOLOGIA, CODIGO_PATOLOGIA, ID_TIPO_PATOLOGIA,DIAS_VIGENCIA) VALUES (%s, %s, %s, %s)",
    GetSQLValueString($descripcionPatologia, "text"),
    GetSQLValueString($codigoPatologia, "text"),
    GetSQLValueString($slTipoPatologia, "int"),
    GetSQLValueString($diasDeVigencia, "int"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 