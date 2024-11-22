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
$hora= date('G:i');

$idDerivacion = $_POST['idDerivacion'];
$idCitacion = $_POST['idCitacion'];
$slAtiendePaciente = $_POST['slAtiendePaciente'];
$slCorrespondeCanasta = $_POST['slCorrespondeCanasta'];
$slQuirurgico = $_POST['slQuirurgico'];
$slComentarioAtiendePaciente = $_POST['slComentarioAtiendePaciente'];
$rutaAdjuntaDocAtencion = $_POST['rutaAdjuntaDocAtencion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$folio = $qrDerivacion->Fields('FOLIO');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_atenciones (ID_DERIVACION, ID_CITACION, ATENDIDO, CORRESPONDE, COMENTARIO, FECHA_REGISTRO, HORA_REGISTRO, QUIRURGICO, RUTA_DOCUMENTO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"),
    GetSQLValueString($idCitacion, "int"),
    GetSQLValueString($slAtiendePaciente, "text"),
    GetSQLValueString($slCorrespondeCanasta, "text"),
    GetSQLValueString(utf8_decode($slComentarioAtiendePaciente), "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($slQuirurgico, "date"),
    GetSQLValueString(utf8_decode($rutaAdjuntaDocAtencion), "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$estado = 'otraConsultaAtendida';

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_events SET ESTADO_CITA=%s, color=%s WHERE id= '$idCitacion'",
            GetSQLValueString('ATENDIDO', "text"),
            GetSQLValueString('#020202', "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

// $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_derivaciones SET ESTADO=%s WHERE ID_DERIVACION= '$idDerivacion'",
//             GetSQLValueString($estado, "text"));
// $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$comentarioBitacora = 'Otra consulta del Folio número '.$folio.'.<br/>Atiende paciente: '.$slAtiendePaciente.'.<br/>Comentario atención: '.$slComentarioAtiendePaciente;
$asunto= 'Otra consulta';

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

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA, RUTA_DOCUMENTO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($folio, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString(utf8_decode($rutaAdjuntaDocAtencion), "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 

