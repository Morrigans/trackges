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
$hora= date('G:i');

$idDerivacion = $_POST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
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


$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.contacto_paciente (ID_DERIVACION, TIPO_CONTACTO,MEDIO_CONTACTO, NOTA_CONTACTO, RUTA_AUDIO, FECHA_REGISTRO, HORA_REGISTRO, RUTA_DOCUMENTO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"),
    GetSQLValueString($slTipoContacto, "text"),
    GetSQLValueString($slMedioContacto, "text"),
    GetSQLValueString($txaNotaContacto, "text"),
    GetSQLValueString($rutaAudio, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($rutaAdjuntaDocContactarPaciente, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$query_qrPaciente = "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$idPaciente'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$nomPaciente = $qrPaciente->Fields('NOMBRE');
$codRutPac = $qrPaciente->Fields('COD_RUTPAC');


$comentarioBitacora = 'Se realiza contacto con paciente '.$nomPaciente.' rut: '.$codRutPac.' de la derivacion numero D0'.$idDerivacion.', nota: '.$txaNotaContacto ;
$asunto= 'Contacto paciente';


$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA, RUTA_AUDIO, RUTA_DOCUMENTO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($rutaAudio, "text"),
    GetSQLValueString($rutaAdjuntaDocContactarPaciente, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;



