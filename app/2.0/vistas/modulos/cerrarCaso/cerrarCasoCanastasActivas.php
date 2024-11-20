<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

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

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET ESTADO=%s, ID_CIERRE_CASO=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($estado, "text"),
            GetSQLValueString($slMotivoCierreCaso, "int"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$estado = 'finalizada';

$updateSQL1 = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_canastas SET ESTADO=%s, OBSERVACION=%s, FECHA_FIN_CANASTA=%s, AUDITORIA=%s WHERE ID_DERIVACION = '$idDerivacion'",
            GetSQLValueString($estado, "text"),
            GetSQLValueString(utf8_decode($observacion), "text"),
            GetSQLValueString($fechaFinCanasta, "date"),
            GetSQLValueString($auditoria, "date"));
$Result1 = $oirs->Execute($updateSQL1) or die($oirs->ErrorMsg());

$comentarioBitacora = $_POST['comentarioBitacoraCerrarCaso'];
$asunto= 'Cerrada';
$hora= date('G:i');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


echo 1;

