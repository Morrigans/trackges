<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
require_once '../../../../Connections/icrs.php';

//Aditional Functions
require_once '../../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria = date('Y-m-d');
$fechaFinCanasta = date('Y-m-d');//debido a que es cierre forzado por cierre de caso
$observacion = 'Forzado por cierre de caso';

$idDerivacion = $_POST['idDerivacion'];
$slMotivoCierreCaso = $_POST['slMotivoCierreCaso'];

$estado = 'cerrada';

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idDerivacionPp = $qrDerivacion->Fields('ID_DERIVACION_PP');


$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_pp SET ESTADO=%s, ID_CIERRE_CASO=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($estado, "text"),
            GetSQLValueString($slMotivoCierreCaso, "int"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$estado = 'finalizada';

$updateSQL1 = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_canastas_pp SET ESTADO=%s, OBSERVACION=%s, FECHA_FIN_CANASTA=%s, AUDITORIA=%s WHERE ID_DERIVACION = '$idDerivacion'",
            GetSQLValueString($estado, "text"),
            GetSQLValueString(utf8_decode($observacion), "text"),
            GetSQLValueString($fechaFinCanasta, "date"),
            GetSQLValueString($auditoria, "date"));
$Result1 = $oirs->Execute($updateSQL1) or die($oirs->ErrorMsg());

$comentarioBitacora = $_POST['comentarioBitacoraCerrarCaso'];
$asunto= 'Cerrada';
$hora= date('G:i');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


$insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA,ID_BITACORA_REMOTO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacionPp, "int"),     
    GetSQLValueString('CRSS', "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($ultimoIdBitacoraPp, "int"));
$Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());



echo 1;

