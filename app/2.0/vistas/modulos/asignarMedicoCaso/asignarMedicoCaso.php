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

$idDerivacion = $_POST['idDerivacion'];
$slAsignarMedicoDerivacion = $_POST['slAsignarMedicoDerivacion'];
$canasta = $_POST['canasta'];

$estado = 'prestador';  

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_derivaciones SET MEDICO=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($slAsignarMedicoDerivacion, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$query_qrMedico= "SELECT * FROM $MM_oirs_DATABASE.login where USUARIO = '$slAsignarMedicoDerivacion'";
$qrMedico = $oirs->SelectLimit($query_qrMedico) or die($oirs->ErrorMsg());
$totalRows_qrMedico = $qrMedico->RecordCount();

$query_qrDerivacion= "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones where ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$folio = $qrDerivacion->Fields('FOLIO');

$nomMedico = $qrMedico->Fields('NOMBRE');

$comentarioBitacora = 'Se asigna a '.$nomMedico.' rut '.$slAsignarMedicoDerivacion.' como médico a Folio Rigth Now '.$folio;
$asunto= 'Médico asignado';
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
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


echo 1;

