<?php
require_once '../../../../Connections/oirs.php';
require_once '../../../../includes/functions.inc.php';

session_start();
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');
$hrAuditoria= date('G:i');

$idEvents = $_POST['idEvents'];
$obsQuitarCita = $_POST['obsQuitarCita'];
$idDerivacion = $_POST['idDerivacion'];

$query_qrDerivaciones= "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivaciones = $oirs->SelectLimit($query_qrDerivaciones) or die($oirs->ErrorMsg());
$totalRows_qrDerivaciones = $qrDerivaciones->RecordCount();

$folio = $qrDerivaciones->Fields('FOLIO');

$estadoDerivacion = $qrDerivaciones->Fields('ESTADO');

if ($estadoDerivacion == 'primeraConsultaAgendada') {
	$estadoDerivacion='prestador';
}
if ($estadoDerivacion == 'segundaConsultaAgendada') {
	$estadoDerivacion='primeraConsultaAtendida';
}
if ($estadoDerivacion == 'otraConsultaAgendada') {
	$estadoDerivacion='segundaConsultaAtendida';
}


$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_derivaciones SET ESTADO=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($estadoDerivacion, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$estadoEvents='ELIMINADO';

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_events SET ESTADO_CITA=%s, OBS_ELIMINAR_CITA=%s,FECHA_ELIMINACION=%s WHERE id= '$idEvents'",
            GetSQLValueString($estadoEvents, "text"),
            GetSQLValueString($obsQuitarCita, "text"),
            GetSQLValueString($auditoria, "date"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$query_qrEvents= "SELECT * FROM $MM_oirs_DATABASE.2_events WHERE id = '$idEvents'";
$qrEvents = $oirs->SelectLimit($query_qrEvents) or die($oirs->ErrorMsg());
$totalRows_qrEvents = $qrEvents->RecordCount();

$rutPro= $qrEvents->Fields('cod_rutpro');
$rutPac= $qrEvents->Fields('cod_rutpac');
$fechaCita= $qrEvents->Fields('start');
//$rutSesion= $qrBuscaDoc->Fields('RUT_SESION');

$query_qrBuscaDoc= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$rutPro'";
$qrBuscaDoc = $oirs->SelectLimit($query_qrBuscaDoc) or die($oirs->ErrorMsg());
$totalRows_qrBuscaDoc = $qrBuscaDoc->RecordCount();

$nomProfesional = $qrBuscaDoc->Fields('NOMBRE');
$tipoProfesion = $qrBuscaDoc->Fields('TIPO');

$query_qrProfesion = "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '$tipoProfesion'";
$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
$totalRows_qrProfesion = $qrProfesion->RecordCount();

$profesionSinGenero = $qrProfesion->Fields('PROFESION');

$query_qrBuscaPac= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$rutPac'";
$qrBuscaPac = $oirs->SelectLimit($query_qrBuscaPac) or die($oirs->ErrorMsg());
$totalRows_qrBuscaPac = $qrBuscaPac->RecordCount();

$nomPaciente = $qrBuscaPac->Fields('NOMBRE');

$comentarioBitacora = 'Se ha eliminado la cita del dia '.date("d-m-Y",strtotime($fechaCita)).' del paciente: '.$nomPaciente.' con el profesional: '.$profesionSinGenero.': '.$nomProfesional.' con motivo de '.$obsQuitarCita;


$asunto= 'Cita eliminada';
$hora= date('G:i');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"), 
    GetSQLValueString($folio, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result2 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());



echo 1;



