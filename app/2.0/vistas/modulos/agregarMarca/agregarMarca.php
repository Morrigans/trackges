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
$slAgregarMarcaDerivacion = $_POST['slAgregarMarcaDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

if ($slAgregarMarcaDerivacion!='0') {

    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET MARCA=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($slAgregarMarcaDerivacion, "text"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

    echo 1;
}else{

    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET MARCA=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString('', "text"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

    echo 0;

}

$comentarioBitacora = 'Se marca derivacion R0'.$idDerivacion.' para cierre';
$asunto= 'para cierre';
$hora= date('G:i');

$idUsuario = $_SESSION['idUsuario'];

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

