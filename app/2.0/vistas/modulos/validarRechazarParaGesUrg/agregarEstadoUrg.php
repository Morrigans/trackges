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
$idUrg = $_POST['idUrg'];
$estadoUrg = $_POST['estadoUrg'];
$comentarioUrg = $_POST['comentarioUrg'];

$query_qrUrg = "SELECT * FROM $MM_oirs_DATABASE.api_urgencias WHERE ID_DERIVACION = '$idDerivacion'";
$qrUrg = $oirs->SelectLimit($query_qrUrg) or die($oirs->ErrorMsg());
$totalRows_qrUrg = $qrUrg->RecordCount();

 $fechaReserva = date("d-m-Y",strtotime($qrUrg->Fields('fecha_admision')));
 $area_atencion = $qrUrg->Fields('area_atencion');


date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');


if ($estadoUrg=='validado') {
	    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.api_urgencias SET ESTADO_VALIDACION=%s, COMENTARIO=%s WHERE id_urgencias= '$idUrg'",
            GetSQLValueString($estadoUrg, "text"),
            GetSQLValueString ($comentarioUrg, "text"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());


$comentarioBitacora = 'Se ha validado ingreso a urgencia para Ges con fecha: '. $fechaReserva.' y área de atención '.$area_atencion.', comentario: '.$comentarioUrg ;
$asunto= 'Validado para ges';
    
    echo 1;

}else{
         $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.api_urgencias SET ESTADO_VALIDACION=%s, COMENTARIO=%s WHERE id_urgencias= '$idUrg'",
            GetSQLValueString($estadoUrg, "text"),
            GetSQLValueString ($comentarioUrg, "text"));
            $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$comentarioBitacora = 'Se ha rechazado ingreso a urgencia para Ges con fecha: '. $fechaReserva.' y área de atención '.$area_atencion.', comentario: '.$comentarioUrg ;
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

