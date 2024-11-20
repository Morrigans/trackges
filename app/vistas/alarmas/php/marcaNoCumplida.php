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


$idAlarma = $_REQUEST['idAlarma'];
$usuario = $_REQUEST['usuario'];
$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrBuscaAlarmas = "SELECT * FROM $MM_oirs_DATABASE.alarmas WHERE ID_ALARMA = '$idAlarma' ";
$qrBuscaAlarmas = $oirs->SelectLimit($query_qrBuscaAlarmas) or die($oirs->ErrorMsg());
$totalRows_qrBuscaAlarmas = $qrBuscaAlarmas->RecordCount();

$rutEmisor= $qrBuscaAlarmas->Fields('USUARIO_EMISOR');
$rutReceptor= $qrBuscaAlarmas->Fields('USUARIO_RECEPTOR');
$mensajeAlarma= $qrBuscaAlarmas->Fields('MENSAJE');

$query_qrFunc = "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$rutReceptor' ";
$qrFunc = $oirs->SelectLimit($query_qrFunc) or die($oirs->ErrorMsg());
$totalRows_qrFunc = $qrFunc->RecordCount();

$nomFunc= $qrFunc->Fields("NOMBRE");

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.alarmas SET CUMPLIDA=%s WHERE ID_ALARMA= '$idAlarma'",
            GetSQLValueString('', "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$fechaCita = date("d-m-Y",strtotime($fechaCita));

$mensaje = 'La solicitud: '.$mensajeAlarma.',  asignada a: '.$nomFunc.', Rut '.$rutReceptor.', ha sido marcada como NO resuelta quedando en estado pendiente.';


date_default_timezone_set('America/Santiago');
$fecha= date('Y-m-d');
$hora= date('G:i');
$asunto = 'Solicitud NO resuelta';
$estado = 'nuevo';

if($rutEmisor!=$rutReceptor){
    $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (USUARIO, ID_DERIVACION, ASUNTO, MENSAJE, FECHA, HORA, ESTADO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($rutEmisor, "text"),
        GetSQLValueString($idDerivacion, "int"),
        GetSQLValueString($asunto, "text"),
        GetSQLValueString($mensaje, "text"),
        GetSQLValueString($fecha, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($estado, "text"));
    $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
}
echo 1;

