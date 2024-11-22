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

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.atenciones_pp (ID_DERIVACION, ID_CITACION, ATENDIDO, CORRESPONDE, COMENTARIO, FECHA_REGISTRO, HORA_REGISTRO, QUIRURGICO, RUTA_DOCUMENTO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
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

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.events_pp SET ESTADO_CITA=%s, color=%s WHERE id= '$idCitacion'",
            GetSQLValueString('ATENDIDO', "text"),
            GetSQLValueString('#020202', "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_pp SET ESTADO=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($estado, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());


$comentarioBitacora = 'Otra consulta de la derivacion numero P0'.$idDerivacion.'.<br/>Atiende paciente: '.$slAtiendePaciente.'.<br/>Comentario atención: '.$slComentarioAtiendePaciente;
$asunto= 'Otra consulta';
 
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

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA, RUTA_DOCUMENTO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($rutaAdjuntaDocAtencion, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

//#############################################################  guarda daTos ICRS##########################################################
if ($idDerivacionPp != '') {
    $asuntoPp= 'Otra consulta  (D0'.$idDerivacionPp.')';
    $estadoNoti='nuevo';

    $query_qrDerivacion = "SELECT * FROM $MM_icrs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacionPp'";
    $qrDerivacion = $icrs->SelectLimit($query_qrDerivacion) or die($icrs->ErrorMsg());
    $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

    $gestoraPp = $qrDerivacion->Fields('ENFERMERA');

    $query_qrUltimaBitacora = ("SELECT max(ID_BITACORA) as ID_BITACORA FROM $MM_oirs_DATABASE.bitacora_pp");
    $qrUltimaBitacora = $oirs->SelectLimit($query_qrUltimaBitacora) or die($oirs->ErrorMsg());
    $totalRows_qrUltimaBitacora = $qrUltimaBitacora->RecordCount();

    $ultimoIdBitacoraPp = $qrUltimaBitacora->Fields('ID_BITACORA');



    $comentarioBitacoraPp = 'Otra consulta de la derivacion numero D0'.$idDerivacionPp.'.<br/>Atiende paciente: '.$slAtiendePaciente.'. <br/>Corresponde derivación: '.$slCorrespondeCanasta.'. <br/>Comentario atención: '.$slComentarioAtiendePaciente;

    $insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA,ID_BITACORA_REMOTO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacionPp, "int"),     
        GetSQLValueString('CRSS', "text"),
        GetSQLValueString($comentarioBitacoraPp, "text"),
        GetSQLValueString($asunto, "text"),
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

$Result1->Close(); 

