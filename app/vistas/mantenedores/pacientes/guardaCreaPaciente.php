<?php
//Connection statement
require_once '../../../Connections/oirs.php';
require_once '../../../Connections/bdu.php';

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

$rutPaciente = $_POST['rutPaciente'];
$nombrePaciente = $_POST['nombrePaciente'];
$sexoPaciente = $_POST['sexoPaciente'];
$nacimientoPaciente = $_POST['nacimientoPaciente'];
$nacimientoPaciente = date("Y-m-d", strtotime($nacimientoPaciente));
$fonoPaciente = $_POST['fonoPaciente'];
$direccionPaciente = $_POST['direccionPaciente'];
$regionPaciente = $_POST['regionPaciente'];
$provinciaPaciente = $_POST['provinciaPaciente'];
$comunaPaciente = $_POST['comunaPaciente'];
$mailPaciente = $_POST['mailPaciente'];
$ocupacionPaciente = $_POST['ocupacionPaciente'];
$previsionPaciente = $_POST['previsionPaciente'];
$planSaludPaciente = $_POST['planSaludPaciente'];
$seguroComplementarioPaciente = $_POST['seguroComplementarioPaciente'];
$companiaSeguroPaciente = $_POST['companiaSeguroPaciente'];

$codRutPac = explode(".", $rutPaciente);
$rut0 = $codRutPac[0]; // porción1
$rut1 = $codRutPac[1]; // porción2
$rut2 = $codRutPac[2]; // porción2
$codRutPac = $rut0.$rut1.$rut2;

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.pacientes (COD_RUTPAC, NOMBRE, SEXO, FEC_NACIMI, FONO, DIRECCION, REGION, PROVINCIA, COMUNA, MAIL, OCUPACION, PREVISION, PLAN_SALUD, SEGURO_COMPLEMENTARIO, COMPANIA_SEGURO, RUT_SESION, AUDITORIA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($codRutPac, "text"), 
    GetSQLValueString($nombrePaciente, "text"),
    GetSQLValueString($sexoPaciente, "text"),
    GetSQLValueString($nacimientoPaciente, "date"),
    GetSQLValueString($fonoPaciente, "text"),
    GetSQLValueString($direccionPaciente, "text"),
	GetSQLValueString($regionPaciente, "int"),
    GetSQLValueString($provinciaPaciente, "int"),
    GetSQLValueString($comunaPaciente, "int"),
    GetSQLValueString($mailPaciente, "text"),
    GetSQLValueString($ocupacionPaciente, "text"),
    GetSQLValueString($previsionPaciente, "text"),
    GetSQLValueString($planSaludPaciente, "text"),
    GetSQLValueString($seguroComplementarioPaciente, "text"),
    GetSQLValueString($companiaSeguroPaciente, "text"),
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($auditoria, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$insertSQL3 = sprintf("INSERT INTO $MM_bdu_DATABASE.pacientes (COD_RUTPAC, NOMBRE, SEXO, FEC_NACIMI, FONO, DIRECCION, REGION, PROVINCIA, COMUNA, MAIL, OCUPACION, PREVISION, PLAN_SALUD, SEGURO_COMPLEMENTARIO, COMPANIA_SEGURO, RUT_SESION, AUDITORIA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($codRutPac, "text"), 
    GetSQLValueString($nombrePaciente, "text"),
    GetSQLValueString($sexoPaciente, "text"),
    GetSQLValueString($nacimientoPaciente, "date"),
    GetSQLValueString($fonoPaciente, "text"),
    GetSQLValueString($direccionPaciente, "text"),
    GetSQLValueString($regionPaciente, "int"),
    GetSQLValueString($provinciaPaciente, "int"),
    GetSQLValueString($comunaPaciente, "int"),
    GetSQLValueString($mailPaciente, "text"),
    GetSQLValueString($ocupacionPaciente, "text"),
    GetSQLValueString($previsionPaciente, "text"),
    GetSQLValueString($planSaludPaciente, "text"),
    GetSQLValueString($seguroComplementarioPaciente, "text"),
    GetSQLValueString($companiaSeguroPaciente, "text"),
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($auditoria, "text"));
$Result3 = $bdu->Execute($insertSQL3) or die($bdu->ErrorMsg());

echo 1;

$Result1->Close(); 