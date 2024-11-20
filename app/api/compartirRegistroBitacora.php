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
$idBitacora = $_POST['idBitacora'];
$ruta = $_POST['ruta'];
$idDerivacionRedGes = $_POST['idDerivacionRedGes'];
$idDerivacionPrestador = $_POST['idDerivacion'];
$comentarioBitacora = $_POST['comentarioBitacora'];
$asunto = $_POST['asunto'];
$enfermeraQueDerivo = $_POST['enfermeraQueDerivo'];

    // $asunto= 'Caso Aceptado API';
    $hora= date('G:i');

    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, ID_DERIVACION_PRESTADOR, SESION, BITACORA, ASUNTO, AUDITORIA, HORA, RUTA_DOCUMENTO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacionRedGes, "int"), 
        GetSQLValueString($idDerivacionPrestador, "int"),
        GetSQLValueString($usuarioPrestador, "text"),
        GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
        GetSQLValueString(utf8_decode($asunto), "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($ruta, "date"));
    $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

    $asunto = 'Nuevo Msj Prestador (DO'.$idDerivacionRedGes.')';
    $estadoNoti = 'nuevo';
    $receptor = $enfermeraQueDerivo;

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