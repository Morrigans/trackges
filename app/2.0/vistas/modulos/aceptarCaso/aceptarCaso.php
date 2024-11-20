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

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$estado = 'aceptada';

if ($qrDerivacion->Fields('ESTADO') == 'pendiente' and $qrDerivacion->Fields('REASIGNADA') == 'si' and $qrDerivacion->Fields('RUT_PRESTADOR') != '') {//si viene reasignada con prestador asignado
    $estado = 'prestador';
    $asunto= 'Prestador asignado';
}

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET ESTADO=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($estado, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$comentarioBitacora = $_POST['comentarioBitacoraAceptarCaso'];
$asunto= 'Aceptada';
$hora= date('G:i');

$idUsuario = $_SESSION['idUsuario'];

//inserto estado en tabla de estados**********************************************************************************************************************************
    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.estados_derivacion (ID_DERIVACION, FOLIO, ESTADO, FECHA_REGISTRO, HORA_REGISTRO, SESION) VALUES (%s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacion, "int"), 
        GetSQLValueString($folio, "text"), 
        GetSQLValueString($estado, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($idUsuario, "int"));
        $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
//********************************************************************************************************************************************************************

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


echo 1;

