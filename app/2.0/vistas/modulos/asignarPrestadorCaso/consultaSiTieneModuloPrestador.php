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

$slAsignarPrestadorDerivacion = $_POST['slAsignarPrestadorDerivacion'];

$query_qrPrestador = "SELECT * FROM $MM_oirs_DATABASE.prestador WHERE RUT_PRESTADOR = '$slAsignarPrestadorDerivacion'";
$qrPrestador = $oirs->SelectLimit($query_qrPrestador) or die($oirs->ErrorMsg());
$totalRows_qrPrestador = $qrPrestador->RecordCount();

echo $qrPrestador->Fields('MODULO_PRESTADOR');