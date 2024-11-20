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


$idDerivacion = $_POST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();



$idPrestador=19;
$idDerivacionPp=$qrDerivacion->Fields('ID_DERIVACION_PP');
$codRutPac=$qrDerivacion->Fields('COD_RUTPAC');
$estado = 'aceptada';


$query_qrNomPaciente = "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'";
$qrNomPaciente = $oirs->SelectLimit($query_qrNomPaciente) or die($oirs->ErrorMsg());
$totalRows_qrNomPaciente = $qrNomPaciente->RecordCount();

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_pp SET ESTADO=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($estado, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$comentarioBitacora = $_POST['comentarioBitacoraAceptarCaso'];
$asunto= 'Aceptada';
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

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION,ID_PRESTADOR,ID_DERIVACION_PRESTADOR, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"), 
    GetSQLValueString($idPrestador, "int"), 
    GetSQLValueString($idDerivacionPp, "int"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


if ($idDerivacionPp != '') {
    $query_qrDerivacionPp = "SELECT * FROM $MM_icrs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacionPp'";
    $qrDerivacionPp = $icrs->SelectLimit($query_qrDerivacionPp) or die($icrs->ErrorMsg());
    $totalRows_qrDerivacionPp = $qrDerivacionPp->RecordCount();

    $gestora = $qrDerivacionPp->Fields('ENFERMERA');

    $query_qrBuscaUltimoRegistroBitPp = "SELECT * FROM $MM_oirs_DATABASE.bitacora_pp ORDER BY ID_BITACORA DESC LIMIT 1";
    $qrBuscaUltimoRegistroBitPp = $oirs->Execute($query_qrBuscaUltimoRegistroBitPp) or die($oirs->ErrorMsg());
    $totalRows_qrBuscaUltimoRegistroBitPp = $qrBuscaUltimoRegistroBitPp->RecordCount();

    $ultimoIdBitacoraPp=$qrBuscaUltimoRegistroBitPp->Fields('ID_BITACORA');

    $comentarioBitacoraPp = 'La derivación número ' . $qrDerivacion->Fields('ID_DERIVACION_PP') . ' del paciente ' . utf8_encode($qrNomPaciente->Fields('NOMBRE')) . ' rut ' . $codRutPac . ' ha sido aceptada.';

    $insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA,ID_BITACORA_REMOTO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacionPp, "int"),     
        GetSQLValueString('CRSS', "text"),
        GetSQLValueString(utf8_decode($comentarioBitacoraPp), "text"),
        GetSQLValueString($asunto, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($ultimoIdBitacoraPp, "int"));
    $Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());

    $estadoNoti='nuevo';
    $asuntoPp= 'Aceptada (D0'.$idDerivacionPp.')';
    $insertSQL2 = sprintf("INSERT INTO $MM_icrs_DATABASE.notificaciones ( USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO,ORIGEN) VALUES ( %s, %s, %s, %s, %s, %s, %s)",
      
        GetSQLValueString($gestora, "text"),
        GetSQLValueString($asuntoPp, "text"),
        GetSQLValueString(utf8_decode($comentarioBitacoraPp), "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($estadoNoti, "text"),
        GetSQLValueString('CRSS', "text"));
    $Result2 = $icrs->Execute($insertSQL2) or die($icrs->ErrorMsg());
}




echo 1;

