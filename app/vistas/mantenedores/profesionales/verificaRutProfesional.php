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

$rutProfesional = $_REQUEST['rutProfesional'];



$query_qrVerificaRut= "SELECT * FROM $MM_oirs_DATABASE.login where  USUARIO='$rutProfesional'";
$qrVerificaRut = $oirs->SelectLimit($query_qrVerificaRut) or die($oirs->ErrorMsg());
$totalRows_qrVerificaRut = $qrVerificaRut->RecordCount();

echo $totalRows_qrVerificaRut;

$Result1->Close(); 