<?php
//headers necesarios para identificar el dominio que se autoriza para realizar consultas mediante api
header("Access-Control-Allow-Origin: https://domicilio.redges.cl");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD, OPTIONS");
header("Access-Control-Allow-Headers: *");

//definimos con header el tipo del documento (JSON)
header("Content-Type:application/json");
//Conexion
require_once '../Connections/oirs.php';
require_once '../includes/functions.inc.php';

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

//recojemos variables que vienen de PRESTADOR
$idDerivacion = $_POST['idDerivacionRedGes'];
$slMotivoCierreCaso = $_POST['slMotivoCierreCaso'];
$comentarioBitacora = $_POST['comentarioBitacoraCerrarCaso'];
$usuarioPrestador = $_POST['usuarioPrestador'];
$idDerivacionPrestador = $_POST['idDerivacionPrestador']; 

    $asunto= 'Caso Cerrado';
    $hora= date('G:i');

    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, ID_DERIVACION_PRESTADOR, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacion, "int"), 
         GetSQLValueString($idDerivacionPrestador, "int"),
        GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
        GetSQLValueString($asunto, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"));
    $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

    $asunto = 'Caso Cerrado API';
    $estadoNoti = 'nuevo';

    $query_qrGestorQueDerivo = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
    $qrGestorQueDerivo = $oirs->SelectLimit($query_qrGestorQueDerivo) or die($oirs->ErrorMsg());
    $totalRows_qrGestorQueDerivo = $qrGestorQueDerivo->RecordCount();

    $rutGestorQueDerivo = $qrGestorQueDerivo->Fields('ENFERMERA');
    $receptor = $rutGestorQueDerivo;

   $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO) VALUES (%s, %s, %s, %s, %s, %s)",
        GetSQLValueString($receptor, "text"),
        GetSQLValueString($asunto, "text"),
        GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($estadoNoti, "text")); 
    $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());

echo 1;


        
?>