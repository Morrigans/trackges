<?php
// Verificar si se proporcionó un token de acceso y si es válido
$token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if ($token !== 'Bearer $P$BzHUsxIgVwVh4HoD9P6YgTGxo7wQYm1') {
    http_response_code(401); // Unauthorized
    echo json_encode(array("error" => "Tocken no autorizado"));
    exit();
}

//headers necesarios para identificar el dominio que se autoriza para realizar consultas mediante api
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD, OPTIONS");
header("Access-Control-Allow-Headers: *");

//definimos con header el tipo del documento (JSON)
header("Content-Type:application/json");

//Conexion
require_once '../../Connections/oirs.php';
require_once '../../includes/functions.inc.php'; 

//date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');
$hora = date('G:i');

//recojemos el Json
$arr  = file_get_contents('php://input');
$jsonString = $arr;

$array = json_decode($jsonString, true);
foreach ($array as $value) {

    //Con el rut del paciente voy buscar el id derivacion y folio para relacionar el censo con la derivacion del paciente *********************************
    echo $rutPac = $value['rut_paciente'];
    echo $codPrestacion = $value['codigo_prestacion'];
    echo $cirugia = $value['cirugia'];
    echo $estado = $value['estado'];
    echo $idReserva = $value['id_reserva'];
    echo $fechaReserva = $value['fecha_reserva'];

    $query_qrPacientes = "SELECT ID, NOMBRE FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$rutPac'";
    $qrPacientes = $oirs->SelectLimit($query_qrPacientes) or die($oirs->ErrorMsg());
    $totalRows_qrPacientes = $qrPacientes->RecordCount();

    $idPaciente = $qrPacientes->Fields('ID');
    $nomPaciente = $qrPacientes->Fields('NOMBRE');

    echo $query_qrDerivacion = "SELECT ID_DERIVACION,FOLIO,ENFERMERA FROM $MM_oirs_DATABASE.derivaciones WHERE ID_PACIENTE = '$idPaciente'";
    $qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
    $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

    $idDerivacion = $qrDerivacion->Fields('ID_DERIVACION');
    $folio = $qrDerivacion->Fields('FOLIO');
    $gestora = $qrDerivacion->Fields('ENFERMERA');
    //*****************************************************************************************************************************************************

    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.api_prog_pabellones (ID_DERIVACION, FOLIO, COD_RUTPAC, COD_PRESTACION, CIRUGIA, ESTADO, ID_RESERVA, FECHA_RESERVA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacion, "text"), 
        GetSQLValueString($folio, "int"), 
        GetSQLValueString($rutPac, "date"), 
        GetSQLValueString(utf8_decode($codPrestacion), "text"), 
        GetSQLValueString(utf8_decode($cirugia), "text"), 
        GetSQLValueString(utf8_decode($estado), "text"), 
        GetSQLValueString(utf8_decode($idReserva), "int"), 
        GetSQLValueString($fechaReserva, "date"));
    $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

    
    echo json_encode(["message" => "Programación de pabellones correcta"]);


   
}

