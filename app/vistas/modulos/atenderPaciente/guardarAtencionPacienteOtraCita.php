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
$idCitacion = $_POST['idCitacion'];
$slAtiendePaciente = $_POST['slAtiendePaciente'];
$slCorrespondeCanasta = $_POST['slCorrespondeCanasta'];
$slQuirurgico = $_POST['slQuirurgico'];
$slComentarioAtiendePaciente = $_POST['slComentarioAtiendePaciente'];
$rutaAdjuntaDocAtencion = $_POST['rutaAdjuntaDocAtencion'];

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.atenciones (ID_DERIVACION, ID_CITACION, ATENDIDO, CORRESPONDE, COMENTARIO, FECHA_REGISTRO, HORA_REGISTRO, QUIRURGICO, RUTA_DOCUMENTO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"),
    GetSQLValueString($idCitacion, "int"),
    GetSQLValueString($slAtiendePaciente, "text"),
    GetSQLValueString($slCorrespondeCanasta, "text"),
    GetSQLValueString($slComentarioAtiendePaciente, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($slQuirurgico, "date"),
    GetSQLValueString($rutaAdjuntaDocAtencion, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$estado = 'otraConsultaAtendida';

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.events SET ESTADO_CITA=%s, color=%s WHERE id= '$idCitacion'",
            GetSQLValueString('ATENDIDO', "text"),
            GetSQLValueString('#020202', "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET ESTADO=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($estado, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());


$comentarioBitacora = 'Otra consulta de la derivacion numero D0'.$idDerivacion.'.<br/>Atiende paciente: '.$slAtiendePaciente.'.<br/>Comentario atención: '.$slComentarioAtiendePaciente;
$asunto= 'Otra consulta';

$idUsuario = $_SESSION['idUsuario'];

//inserto estado en tabla de estados**********************************************************************************************************************************
    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.estados_derivacion (ID_DERIVACION, ESTADO, FECHA_REGISTRO, HORA_REGISTRO, SESION) VALUES (%s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacion, "int"), 
        GetSQLValueString($estado, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($idUsuario, "int"));
        $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
//********************************************************************************************************************************************************************

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA, RUTA_DOCUMENTO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($rutaAdjuntaDocAtencion, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 

