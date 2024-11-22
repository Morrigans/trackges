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

$idDerivacion = $_POST['idDerivacion'];
$tipoUsuario = $_POST['tipoUsuario'];
$idPab = $_POST['idPab'];
$estadoPab = $_POST['estadoPab'];
$comentarioPab = $_POST['comentarioPab'];

$query_qrPab = "SELECT * FROM $MM_oirs_DATABASE.api_pabellones WHERE ID_DERIVACION = '$idDerivacion'";
$qrPab = $oirs->SelectLimit($query_qrPab) or die($oirs->ErrorMsg());
$totalRows_qrPab = $qrPab->RecordCount();

 $fechaReserva = date("d-m-Y",strtotime($qrPab->Fields('fecha_reserva')));
 $codigoPrestacion = $qrPab->Fields('codigo_prestacion');


date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');


if ($estadoPab=='validado') {
	    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.api_pabellones SET ESTADO_VALIDACION=%s, COMENTARIO=%s WHERE id_pabellones= '$idPab'",
            GetSQLValueString($estadoPab, "text"),
            GetSQLValueString ($comentarioPab, "text"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());


$comentarioBitacora = 'Se ha validado ingreso pabellon para Ges con fecha: '. $fechaReserva.' y codigo prestación '.$codigoPrestacion.', comentario: '.$comentarioPab ;
$asunto= 'Validado para ges';
    
    echo 1;

}else{
         $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.api_pabellones SET ESTADO_VALIDACION=%s, COMENTARIO=%s WHERE id_pabellones= '$idPab'",
            GetSQLValueString($estadoPab, "text"),
            GetSQLValueString ($comentarioPab, "text"));
            $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$comentarioBitacora = 'Se ha rechazado ingreso a pabellon para Ges con fecha: '. $fechaReserva.' y codigo prestación '.$codigoPrestacion.', comentario: '.$comentarioPab ;
$asunto= 'Rechazado para ges';

    echo 2;

}


$hora= date('G:i');

$idUsuario = $_SESSION['idUsuario'];

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

