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

$slTipoMotivo = $_POST['slTipoMotivo'];
$descripcionVence = $_POST['descripcionVence'];


$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.motivos_fin_canastas (TIPO_MOTIVO, DESC_MOTIVO) VALUES (%s, %s)",
   
    GetSQLValueString($slTipoMotivo, "text"),
    GetSQLValueString($descripcionVence, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 