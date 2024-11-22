<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
require_once '../../../../Connections/icrs.php';

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
$idDerivacionPp = $_POST['idDerivacionPp'];
$fechaAgendamiento = $_POST['fechaAgendamiento'];
$horaAgendamiento = $_POST['horaAgendamiento'];
$obsAgendamientoEvents = $_POST['obsAgendamientoEvents'];
$tipoAtencion = $_POST['tipoAtencion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idDerivacionRedGes = $qrDerivacion->Fields('ID_DERIVACION_REDGES');
$idPaciente = $qrDerivacion->Fields('ID_PACIENTE');
$idLogin = $qrDerivacion->Fields('RUT_PRESTADOR');
$nDerivacion = $qrDerivacion->Fields('N_DERIVACION');

$query_qrBuscaDoc= "SELECT * FROM $MM_oirs_DATABASE.login WHERE ID = '$idLogin'";
$qrBuscaDoc = $oirs->SelectLimit($query_qrBuscaDoc) or die($oirs->ErrorMsg());
$totalRows_qrBuscaDoc = $qrBuscaDoc->RecordCount();

$codRutPro = $qrBuscaDoc->Fields('USUARIO');
$nomProfesional = $qrBuscaDoc->Fields('NOMBRE');
$tipoProfesion = $qrBuscaDoc->Fields('TIPO');

$query_qrBuscaPac= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$idPaciente'";
$qrBuscaPac = $oirs->SelectLimit($query_qrBuscaPac) or die($oirs->ErrorMsg());
$totalRows_qrBuscaPac = $qrBuscaPac->RecordCount();

$rutPaciente = $qrBuscaPac->Fields('COD_RUTPAC');
$nomPaciente = $qrBuscaPac->Fields('NOMBRE');
$estado='CITA';
$color='#04B404';


$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.events_pp (ID_DERIVACION, TIPO_ATENCION, cod_rutpac, ID_PACIENTE, cod_rutpro, ID_PROFESIONAL, start, hora, color, ESTADO_CITA, COMENTARIO, FECHA_REGISTRO, HORA_REGISTRO, RUT_SESION) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
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
    GetSQLValueString($obsAgendamientoEvents, "text"),
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

$comentarioBitacora = $cons.' de la derivación N°: '.$nDerivacion.'<br/> Fecha agendamiento '.$fechaAgendamiento.' a las '.$horaAgendamiento.'<br/> Para el paciente: '.$nomPaciente.'<br/> Con el profesional: '.$profesionSinGenero.' / '.$nomProfesional.'.<br/> Comentario: '.$obsAgendamientoEvents;


$asunto= 'Cita asignada';
$hora= date('G:i');

$idUsuario = $_SESSION['idUsuario'];

//inserto estado en tabla de estados**********************************************************************************************************************************
    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.estados_derivacion_pp (ID_DERIVACION, ESTADO, FECHA_REGISTRO, HORA_REGISTRO, SESION) VALUES (%s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacion, "int"), 
        GetSQLValueString($estado, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($idUsuario, "int"));
        $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
//********************************************************************************************************************************************************************

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result2 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_pp SET ESTADO=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($estado, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$Result1->Close();




//#############################################################  guarda daTos ICRS##########################################################
if ($idDerivacionPp != '') {
    $asuntoPp= 'Cita asignada  (D0'.$idDerivacionPp.')';
    $estadoNoti='nuevo';

    $query_qrDerivacion = "SELECT * FROM $MM_icrs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacionPp'";
    $qrDerivacion = $icrs->SelectLimit($query_qrDerivacion) or die($icrs->ErrorMsg());
    $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

    $gestoraPp = $qrDerivacion->Fields('ENFERMERA');

    $query_qrUltimaBitacora = ("SELECT max(ID_BITACORA) as ID_BITACORA FROM $MM_oirs_DATABASE.bitacora_pp");
    $qrUltimaBitacora = $oirs->SelectLimit($query_qrUltimaBitacora) or die($oirs->ErrorMsg());
    $totalRows_qrUltimaBitacora = $qrUltimaBitacora->RecordCount();

    $ultimoIdBitacoraPp = $qrUltimaBitacora->Fields('ID_BITACORA');

    $comentarioBitacoraPp = $cons.' de la derivación N° D0'.$idDerivacionPp.'<br/> Fecha agendamiento '.$fechaAgendamiento.' a las '.$horaAgendamiento.'<br/> Para el paciente: '.$nomPaciente.'<br/> Con el profesional: '.$profesionSinGenero.' / '.$nomProfesional.'.<br/> Comentario: '.$obsAgendamientoEvents;

    $insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA,ID_BITACORA_REMOTO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacionPp, "int"),     
        GetSQLValueString('CRSS', "text"),
        GetSQLValueString($comentarioBitacoraPp, "text"),
        GetSQLValueString($asuntoPp, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($ultimoIdBitacoraPp, "int"));
    $Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());

    $insertSQL2 = sprintf("INSERT INTO $MM_icrs_DATABASE.notificaciones (USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO,ORIGEN) VALUES ( %s, %s, %s, %s, %s, %s, %s)",
      
        GetSQLValueString($gestoraPp, "text"),
        GetSQLValueString($asuntoPp, "text"),
        GetSQLValueString($comentarioBitacoraPp, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($estadoNoti, "text"),
        GetSQLValueString('CRSS', "text"));
    $Result2 = $icrs->Execute($insertSQL2) or die($icrs->ErrorMsg());

}



echo 1;



