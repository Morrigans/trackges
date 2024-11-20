<?php
//headers necesarios para identificar el dominio que se autoriza para realizar consultas mediante api
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD, OPTIONS");
header("Access-Control-Allow-Headers: *");

//definimos con header el tipo del documento (JSON)
header("Content-Type:application/json");

//Conexion
require_once "../../Connections/oirs.php";
require_once "../../includes/functions.inc.php"; 

date_default_timezone_set("America/Santiago");
    $auditoria= date("Y-m-d");
    $fecha= date("Y-m-d");
    $hora = date("G:i");

//recojemos el Json
$arr  = file_get_contents('php://input');
echo $jsonString = $arr;

//DECODIFICA EL JSON RECIBIDO Y LO CONVIERTE EN ARREGlO Y LO RECORRE CON EL foreach
$array = json_decode($jsonString, true);
foreach ($array as $value) {

    $folio = $value['folio_rn'];
    $montoDev = $value['monto_devengado'];
    $diasDesdeQx = $value['dias_desde_qx'];

    $query_qrDerivaciones = "SELECT * FROM $MM_oirs_DATABASE.derivaciones where FOLIO = '$folio'"; 
    $qrDerivaciones = $oirs->SelectLimit($query_qrDerivaciones) or die($oirs->ErrorMsg());
    $totalRows_qrDerivaciones = $qrDerivaciones->RecordCount();

    $estadoRn = utf8_encode($qrDerivaciones->Fields('ESTADO_RN'));

    if ($estadoRn == 'Prestador Asignado' or $estadoRn == 'Derivación Aceptada' or $estadoRn == 'Solicita autorización') {

        $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET MONTO_DEVENGADO=%s, DIAS_DESDE_CIRUGIA=%s WHERE FOLIO= '$folio'",
                    GetSQLValueString($montoDev, "text"),
                    GetSQLValueString($diasDesdeQx, "text"));
        $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());
    

        // inserto en historico***********************************************************************************
        $insertSQLHistorico = sprintf("INSERT INTO $MM_oirs_DATABASE.api_monto_dev (FOLIO, MONTO_DEV, FECHA_PRIMERA_QX, DIAS_DESDE_QX, FECHA_REGISTRO, AUDITORIA) VALUES (%s,%s,%s,%s,%s,%s)",
            GetSQLValueString($value['folio_rn'], "text"),
            GetSQLValueString($value['monto_devengado'], "text"),
            GetSQLValueString($value['fecha_primera_qx'], "date"),
            GetSQLValueString($value['dias_desde_qx'], "int"),
            GetSQLValueString($value['fecha_registro'], "date"),
            GetSQLValueString($auditoria, "date"));
        $Result1Historico = $oirs->Execute($insertSQLHistorico) or die($oirs->ErrorMsg()); 
        //**********************************************************************************************************
    }
}
