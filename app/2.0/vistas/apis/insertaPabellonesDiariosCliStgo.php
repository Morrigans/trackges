<?php
//headers necesarios para identificar el dominio que se autoriza para realizar consultas mediante api
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD, OPTIONS");
header("Access-Control-Allow-Headers: *");

//definimos con header el tipo del documento (JSON)
header("Content-Type:application/json");

$token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if ($token !== 'Bearer $P$BzHUsxIgVwVh4HoD9P6YgTGxo7wQYm1') {
    http_response_code(401); // Unauthorized
    echo json_encode(array("error" => "Tocken no autorizado"));
    exit();
}

//Conexion
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php'; 

//recojemos el Json
$arr  = file_get_contents('php://input');
$jsonString = $arr;

date_default_timezone_set('America/Santiago'); 
$auditoria= date('Y-m-d');
$hora = date('G:i');

//$jsonString ='[{"rut_paciente": "13.301.435-7", "id_reserva": "209776", "fecha_reserva": "2023-01-25", "fecha_inicio_pabellon": "2023-01-25", "nombre_medico": "lucero x fernandos", "codigo_prestacion": "17-03-061-00", "nombre_cirugia": "y de complejidad mayor:inc. reempl-pab", "estado": "confirmado"}, {"rut_paciente": "4.829.267-4", "id_reserva": "212229", "fecha_reserva": "2023-01-25", "fecha_inicio_pabellon": "2023-01-25", "nombre_medico": "olguin monras fernando", "codigo_prestacion": "18-02-053-00", "nombre_cirugia": "apendicectomia y/o dren.absceso apen-pab", "estado": "confirmado"}, {"rut_paciente": "7.372.149-0", "id_reserva": "210321", "fecha_reserva": "2023-01-25", "fecha_inicio_pabellon": "2023-01-25", "nombre_medico": "van grieken garcia jorge", "codigo_prestacion": "17-50-053-00", "nombre_cirugia": "implante de marcapaso bicameral -pab", "estado": "confirmado"}]';


function quitarPuntosRut($rut) {
    // Quitamos los puntos y guiones del RUT
    $rutSinPuntos = str_replace('.', '', $rut);
    
    return $rutSinPuntos;
}

$array = json_decode($jsonString, true);
foreach ($array as $value) {

    //Con el rut del paciente voy buscar el id derivacion y folio para relacionar el censo con la derivacion del paciente *********************************
    $rutPac = $value['Rut Paciente'];

    $rutPacLimpio = quitarPuntosRut($rutPac);

    $idProtocolo = $value['ID_PROTOCOLO']; 

    $query_qrPacientes = "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$rutPacLimpio'";
    $qrPacientes = $oirs->SelectLimit($query_qrPacientes) or die($oirs->ErrorMsg());
    $totalRows_qrPacientes = $qrPacientes->RecordCount();

    $nomPaciente = $qrPacientes->Fields('NOMBRE');

    $query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE COD_RUTPAC = '$rutPac'";
    $qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
    $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

    $idDerivacion = $qrDerivacion->Fields('ID_DERIVACION');
    $folioRn = $qrDerivacion->Fields('FOLIO');
    $gestora = $qrDerivacion->Fields('ENFERMERA');
    //*****************************************************************************************************************************************************

    // inserto en historico***********************************************************************************
    $insertSQLHistorico = sprintf("INSERT INTO $MM_oirs_DATABASE.2_api_pabellones_historico (rut_paciente, id_reserva, fecha_reserva, fecha_inicio_pabellon, nombre_medico, codigo_prestacion, nombre_cirugia, estado, fecha_registro, ID_DERIVACION, FOLIO, ID_PROTOCOLO) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
        GetSQLValueString($rutPacLimpio, "text"),
        GetSQLValueString($value['id_reserva'], "text"),
        GetSQLValueString($value['fecha_reserva'], "date"),
        GetSQLValueString($value['fecha_inicio_pabellon'], "date"),
        GetSQLValueString(utf8_decode($value['nombre_medico']), "text"),
        GetSQLValueString($value['codigo_prestacion'], "text"),
        GetSQLValueString(utf8_decode($value['nombre_cirugia']), "text"),
        GetSQLValueString(utf8_decode($value['estado']), "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($idDerivacion, "int"),
        GetSQLValueString($folioRn, "text"),
        GetSQLValueString($idProtocolo, "text"));
    $Result1Historico = $oirs->Execute($insertSQLHistorico) or die($oirs->ErrorMsg()); 
    //**********************************************************************************************************

    $id_reserva = $value['id_reserva'];

    $query_qrPabellon = "SELECT * FROM $MM_oirs_DATABASE.2_api_pabellones WHERE id_reserva = '$id_reserva'";
    $qrPabellon = $oirs->SelectLimit($query_qrPabellon) or die($oirs->ErrorMsg());
    $totalRows_qrPabellon = $qrPabellon->RecordCount();

    if ($totalRows_qrPabellon > 0) {
        $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_api_pabellones SET fecha_reserva=%s, fecha_inicio_pabellon=%s, nombre_medico=%s, codigo_prestacion=%s, nombre_cirugia=%s, estado=%s, fecha_registro=%s WHERE id_reserva= '$id_reserva'",
            GetSQLValueString($value['fecha_reserva'], "date"),
            GetSQLValueString($value['fecha_inicio_pabellon'], "date"),
            GetSQLValueString(utf8_decode($value['nombre_medico']), "text"),
            GetSQLValueString($value['codigo_prestacion'], "text"),
            GetSQLValueString($value['nombre_cirugia'], "text"),
            GetSQLValueString(utf8_decode($value['estado']), "text"),
            GetSQLValueString($auditoria, "date"));
        $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

    }else{
        // inserto en censo diario***********************************************************************************
    $insertSQLDiario = sprintf("INSERT INTO $MM_oirs_DATABASE.2_api_pabellones (rut_paciente, id_reserva, fecha_reserva, fecha_inicio_pabellon, nombre_medico, codigo_prestacion, nombre_cirugia, estado, fecha_registro, ID_DERIVACION, FOLIO, ESTADO_VALIDACION,ID_PROTOCOLO) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
        GetSQLValueString($rutPacLimpio, "text"),
        GetSQLValueString($value['id_reserva'], "text"),
        GetSQLValueString($value['fecha_reserva'], "date"),
        GetSQLValueString($value['fecha_inicio_pabellon'], "date"),
        GetSQLValueString(utf8_decode($value['nombre_medico']), "text"),
        GetSQLValueString($value['codigo_prestacion'], "text"),
        GetSQLValueString(utf8_decode($value['nombre_cirugia']), "text"),
        GetSQLValueString(utf8_decode($value['estado']), "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($idDerivacion, "int"),
        GetSQLValueString($folioRn, "text"),
        GetSQLValueString($idProtocolo, "text"),
        GetSQLValueString('sin validar', "text"));
    $Result1Diario = $oirs->Execute($insertSQLDiario) or die($oirs->ErrorMsg()); 
    //**********************************************************************************************************

    //registro en bitacora la nueva hospitalizacion
    $comentarioBitacora = 'El paciente '.utf8_encode($nomPaciente).' registra un ingreso a pabellon con id de reserva '.$value['id_reserva'].' y codigo de prestacion '.$value['codigo_prestacion'];
    $asunto= 'Ingreso pabellon';

    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacion, "text"), 
        GetSQLValueString($folioRn, "text"), 
        GetSQLValueString('99.999.999-9', "text"),
        GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
        GetSQLValueString($asunto, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"));
    $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


    $receptor = $gestora;

    $estadoNoti = 'nuevo';

    $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, FOLIO, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacion, "int"),
        GetSQLValueString($folioRn, "text"),
        GetSQLValueString($receptor, "text"),
        GetSQLValueString(utf8_decode($asunto), "text"),
        GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($estadoNoti, "text"),
        GetSQLValueString('99.999.999-9', "text"));
    $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());


    //busco los perfiles de supervisor para asignarles la notificacion de nueva hospitalizacion
    $query_qrBuscaSupervisor = "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO='2'";
    $qrBuscaSupervisor = $oirs->SelectLimit($query_qrBuscaSupervisor) or die($oirs->ErrorMsg());
    $totalRows_qrBuscaSupervisor = $qrBuscaSupervisor->RecordCount(); 

    while (!$qrBuscaSupervisor->EOF) {
        $supervisor = $qrBuscaSupervisor->Fields('USUARIO');
        $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, FOLIO, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($idDerivacion, "int"),
            GetSQLValueString($folioRn, "text"),
            GetSQLValueString($supervisor, "text"),
            GetSQLValueString(utf8_decode($asunto), "text"),
            GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
            GetSQLValueString($auditoria, "date"),
            GetSQLValueString($hora, "date"),
            GetSQLValueString($estadoNoti, "text"),
            GetSQLValueString('99.999.999-9', "text"));
        $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
    $qrBuscaSupervisor->MoveNext(); }
    //***********************************************************************************************
    }

}

echo $jsonString;