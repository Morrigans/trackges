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
$hora= date('G:i');

$idDerivacion = $_POST['idDerivacion'];
$idDerivacionPp = $_POST['idDerivacionPp'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idPaciente = $qrDerivacion->Fields('ID_PACIENTE');

$slTipoContacto = $_POST['slTipoContacto'];
$slMedioContacto = $_POST['slMedioContacto'];
$txaNotaContacto = $_POST['txaNotaContacto'];
$rutaAudio = $_POST['rutaAudio'];
$rutaAdjuntaDocContactarPaciente = $_POST['rutaAdjuntaDocContactarPaciente'];

if ($rutaAudio != '') {
   $rutaAudio = "/audios/".$rutaAudio;
}else{
    $rutaAudio = '';
}


$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.contacto_paciente_pp (ID_DERIVACION, TIPO_CONTACTO,MEDIO_CONTACTO, NOTA_CONTACTO, RUTA_AUDIO, FECHA_REGISTRO, HORA_REGISTRO, RUTA_DOCUMENTO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"),
    GetSQLValueString($slTipoContacto, "text"),
    GetSQLValueString($slMedioContacto, "text"),
    GetSQLValueString(utf8_decode($txaNotaContacto), "text"),
    GetSQLValueString(utf8_decode($rutaAudio), "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString(utf8_decode($rutaAdjuntaDocContactarPaciente), "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$query_qrPaciente = "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$idPaciente'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$nomPaciente = $qrPaciente->Fields('NOMBRE');
$codRutPac = $qrPaciente->Fields('COD_RUTPAC');


$comentarioBitacora = 'Se realiza contacto con paciente '.$nomPaciente.' rut: '.$codRutPac.' de la derivacion numero P0'.$idDerivacion.', nota: '.$txaNotaContacto ;
$asunto= 'Contacto paciente';


$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA, RUTA_AUDIO, RUTA_DOCUMENTO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString(utf8_decode($rutaAudio), "text"),
    GetSQLValueString(utf8_decode($rutaAdjuntaDocContactarPaciente), "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

//#############################################################  guarda daTos ICRS##########################################################
if ($idDerivacionPp != '') {
    $rutaAdjuntaDocContactarPacientePp='https://crss.redges.cl/vistas/modulos/pacientesPp/bitacora/adjuntaDoc/'.$rutaAdjuntaDocContactarPaciente;

    $query_qrDerivacion = "SELECT * FROM $MM_icrs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacionPp'";
    $qrDerivacion = $icrs->SelectLimit($query_qrDerivacion) or die($icrs->ErrorMsg());
    $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

    $gestoraPp = $qrDerivacion->Fields('ENFERMERA');

    $query_qrUltimaBitacora = ("SELECT max(ID_BITACORA) as ID_BITACORA FROM $MM_oirs_DATABASE.bitacora");
    $qrUltimaBitacora = $oirs->SelectLimit($query_qrUltimaBitacora) or die($oirs->ErrorMsg());
    $totalRows_qrUltimaBitacora = $qrUltimaBitacora->RecordCount();

    $ultimoIdBitacoraPp = $qrUltimaBitacora->Fields('ID_BITACORA');
    $nDerivacionPp = 'D0'.$idDerivacionPp;

    $comentarioBitacoraPp = 'Se realiza contacto con paciente '.$nomPaciente.' rut: '.$codRutPac.' de la derivacion numero D0'.$idDerivacionPp.', nota: '.$txaNotaContacto ;
    $asuntoPp = 'Contacto paciente (D0'.$idDerivacionPp.')';
    $estadoNoti = 'nuevo';


    $insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA,ID_BITACORA_REMOTO,RUTA_DOCUMENTO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacionPp, "int"),     
        GetSQLValueString('CRSS', "text"),
        GetSQLValueString(utf8_decode($comentarioBitacoraPp), "text"),
        GetSQLValueString($asuntoPp, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($ultimoIdBitacoraPp, "int"),
        GetSQLValueString(utf8_decode($rutaAdjuntaDocContactarPacientePp), "text"));
    $Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());




    $insertSQL2 = sprintf("INSERT INTO $MM_icrs_DATABASE.notificaciones ( USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO,ORIGEN) VALUES ( %s, %s, %s, %s, %s, %s, %s)",
      
        GetSQLValueString($gestoraPp, "text"),
        GetSQLValueString($asuntoPp, "text"),
        GetSQLValueString(utf8_decode($comentarioBitacoraPp), "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($estadoNoti, "text"),
        GetSQLValueString('CRSS', "text"));
    $Result2 = $icrs->Execute($insertSQL2) or die($icrs->ErrorMsg());
}

echo 1;



