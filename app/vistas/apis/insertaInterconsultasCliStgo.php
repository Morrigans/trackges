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
    echo $idIngreso = $value['id_ingreso'];
    echo $estado = $value['estado'];
    echo $especialidad = $value['especialidad'];
    echo $fechaSolicitud = $value['fecha_solicitud'];
    echo $fechaFinalizada = $value['fecha_finalizada'];
    echo $profesional = $value['profesional'];
    echo $demora = $value['demora'];

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

    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.api_interconsultas (ID_DERIVACION, FOLIO, COD_RUTPAC, ID_INGRESO, ESTADO, ESPECIALIDAD, FECHA_SOLICITUD, FECHA_FINALIZADA, PROFESIONAL, DEMORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacion, "text"), 
        GetSQLValueString($folio, "int"), 
        GetSQLValueString($rutPac, "date"), 
        GetSQLValueString($idIngreso, "text"), 
        GetSQLValueString($estado, "text"), 
        GetSQLValueString($especialidad, "text"), 
        GetSQLValueString($fechaSolicitud, "date"), 
        GetSQLValueString($fechaFinalizada, "date"),
        GetSQLValueString($profesional, "text"),
        GetSQLValueString($demora, "text"));
    $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

    
    echo json_encode(["message" => "Interconsultas correctas"]);


   
}

