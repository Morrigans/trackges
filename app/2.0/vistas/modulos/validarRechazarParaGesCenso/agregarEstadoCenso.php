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

$idDerivacion = $_POST['idDerivacion'];
$tipoUsuario = $_POST['tipoUsuario'];
$idCenso = $_POST['idCenso'];
$estadoCenso = $_POST['estadoCenso'];
$comentarioCenso = $_POST['comentarioCenso'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$folio = $qrDerivacion->Fields('FOLIO');

$query_qrCenso = "SELECT * FROM $MM_oirs_DATABASE.2_api_censo WHERE ID_DERIVACION = '$idDerivacion'";
$qrCenso = $oirs->SelectLimit($query_qrCenso) or die($oirs->ErrorMsg());
$totalRows_qrCenso = $qrCenso->RecordCount();

 $fechaIngreso = date("d-m-Y",strtotime($qrCenso->Fields('fecha_ingreso')));
 $codigoPrestacion = $qrCenso->Fields('codigo_prestacion');


date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');


if ($estadoCenso=='validado') {
	    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_api_censo SET ESTADO=%s, COMENTARIO=%s WHERE id_censo= '$idCenso'",
            GetSQLValueString($estadoCenso, "text"),
            GetSQLValueString ($comentarioCenso, "text"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());


$comentarioBitacora = 'Se ha validado ingreso hospitalario para Ges con fecha: '. $fechaIngreso.' y codigo prestación '.$codigoPrestacion.', comentario: '.$comentarioCenso ;
$asunto= 'Validado para ges';
    
    echo 1;

}else{


         $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_api_censo SET ESTADO=%s, COMENTARIO=%s WHERE id_censo= '$idCenso'",
            GetSQLValueString($estadoCenso, "text"),
            GetSQLValueString ($comentarioCenso, "text"));
            $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$comentarioBitacora = 'Se ha rechazado ingreso hospitalario para Ges con fecha: '. $fechaIngreso.' y codigo prestación '.$codigoPrestacion.', comentario: '.$comentarioCenso ;
$asunto= 'Rechazado para ges';

    echo 2;

}


$hora= date('G:i');

$idUsuario = $_SESSION['idUsuario'];

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"), 
    GetSQLValueString($folio, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

