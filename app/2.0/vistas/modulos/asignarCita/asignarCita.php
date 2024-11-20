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
$hrAuditoria= date('G:i');

$idDerivacion = $_POST['idDerivacion'];
$fechaAgendamiento = $_POST['fechaAgendamiento'];
$horaAgendamiento = $_POST['horaAgendamiento'];
$obsAgendamientoEvents = $_POST['obsAgendamientoEvents'];
$tipoAtencion = $_POST['tipoAtencion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$folio = $qrDerivacion->Fields('FOLIO');

$idDerivacionRedGes = $qrDerivacion->Fields('ID_DERIVACION_REDGES');
$rutPaciente = $qrDerivacion->Fields('COD_RUTPAC');
$codRutPro = $qrDerivacion->Fields('MEDICO');
$nDerivacion = $qrDerivacion->Fields('N_DERIVACION');

$query_qrBuscaDoc= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$codRutPro'";
$qrBuscaDoc = $oirs->SelectLimit($query_qrBuscaDoc) or die($oirs->ErrorMsg());
$totalRows_qrBuscaDoc = $qrBuscaDoc->RecordCount();

$nomProfesional = $qrBuscaDoc->Fields('NOMBRE');
$tipoProfesion = $qrBuscaDoc->Fields('TIPO');

$query_qrBuscaPac= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$rutPaciente'";
$qrBuscaPac = $oirs->SelectLimit($query_qrBuscaPac) or die($oirs->ErrorMsg());
$totalRows_qrBuscaPac = $qrBuscaPac->RecordCount();

$nomPaciente = $qrBuscaPac->Fields('NOMBRE');
$estado='CITA';
$color='#04B404';


$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_events (ID_DERIVACION, TIPO_ATENCION, cod_rutpac, ID_PACIENTE, cod_rutpro, ID_PROFESIONAL, start, hora, color, ESTADO_CITA, COMENTARIO, FECHA_REGISTRO, HORA_REGISTRO, RUT_SESION) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"),
    GetSQLValueString($tipoAtencion, "text"),
    GetSQLValueString($rutPaciente, "text"),
    GetSQLValueString($idPaciente, "int"),
    GetSQLValueString($codRutPro, "text"),
    GetSQLValueString($idLogin, "int"),
    GetSQLValueString($fechaAgendamiento, "date"),
    GetSQLValueString($horaAgendamiento, "date"),
    GetSQLValueString($color, "text"),
    GetSQLValueString($estado, "text"),
    GetSQLValueString(utf8_decode($obsAgendamientoEvents), "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hrAuditoria, "date"),
    GetSQLValueString($usuario, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$query_qrProfesion = "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '$tipoProfesion'";
$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
$totalRows_qrProfesion = $qrProfesion->RecordCount();

$profesionSinGenero = $qrProfesion->Fields('PROFESION');

if($tipoAtencion=='primeraConsulta'){
    $cons='Primera consulta';
    $estado='primeraConsultaAgendada';
}
if($tipoAtencion=='segundaConsulta'){
    $cons='Segunda consulta';
    $estado='segundaConsultaAgendada';
}
if($tipoAtencion=='otraConsulta'){
    $cons='Otra consulta';
    $estado='otraConsultaAgendada';
}

$comentarioBitacora = $cons.' del Folio N°: '.$folio.'<br/> Fecha agendamiento '.$fechaAgendamiento.' a las '.$horaAgendamiento.'<br/> Para el paciente: '.utf8_encode($nomPaciente).'<br/> Con el profesional: '.utf8_encode($profesionSinGenero).' / '.utf8_encode($nomProfesional).'.<br/> Comentario: '.$obsAgendamientoEvents;
$asunto= 'Cita asignada';
$hora= date('G:i');

$idUsuario = $_SESSION['idUsuario'];

//inserto estado en tabla de estados**********************************************************************************************************************************
    // $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.estados_derivacion (ID_DERIVACION, ESTADO, FECHA_REGISTRO, HORA_REGISTRO, SESION) VALUES (%s, %s, %s, %s, %s)",
    //     GetSQLValueString($idDerivacion, "int"), 
    //     GetSQLValueString($estado, "text"),
    //     GetSQLValueString($auditoria, "date"),
    //     GetSQLValueString($hora, "date"),
    //     GetSQLValueString($idUsuario, "int"));
    //     $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
//********************************************************************************************************************************************************************

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"), 
    GetSQLValueString($folio, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result2 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


// $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_derivaciones SET ESTADO=%s WHERE ID_DERIVACION= '$idDerivacion'",
//             GetSQLValueString($estado, "text"));
// $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$Result1->Close();

echo 1;



