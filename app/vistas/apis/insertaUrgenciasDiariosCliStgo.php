<?php
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

//recojemos el Json

$arr = $_REQUEST["params"];

$arr = str_replace("'","\"",$arr);
$arr = substr($arr,1);
$arr = substr($arr,0,-1);


    date_default_timezone_set("America/Santiago");
    $auditoria= date("Y-m-d");
    $hora = date("G:i");

echo $jsonString ="[".$arr."]";


//$jsonString ='[{"rut_paciente": "4.829.267-4", "id_urgencia": "678783", "fecha_admision": "2023-01-25", "area_atencion": "adulto", "tipo_alta": "hospitalizado", "nombre_convenio": "fonasa", "ley_urgencia": "no", "fecha_foto": "2023-01-26"}]';


//DECODIFICA EL JSON RECIBIDO Y LO CONVIERTE EN ARREGO Y LO RECORRE CON EL foreach
$array = json_decode($jsonString, true);
foreach ($array as $value) {

    //Con el rut del paciente voy buscar el id derivacion y folio para relacionar el censo con la derivacion del paciente *********************************
    $rutPac = $value['rut_paciente'];

    $query_qrPacientes = "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$rutPac'";
    $qrPacientes = $oirs->SelectLimit($query_qrPacientes) or die($oirs->ErrorMsg());
    $totalRows_qrPacientes = $qrPacientes->RecordCount();

    $idPaciente = $qrPacientes->Fields('ID');
    $nomPaciente = $qrPacientes->Fields('NOMBRE');

    $query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_PACIENTE = '$idPaciente'";
    $qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
    $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

    $idDerivacion = $qrDerivacion->Fields('ID_DERIVACION');
    $folioRn = $qrDerivacion->Fields('FOLIO');
    $gestora = $qrDerivacion->Fields('ENFERMERA');
    //*****************************************************************************************************************************************************

       $insertSQLNDiario = sprintf("INSERT INTO $MM_oirs_DATABASE.api_urgencias (rut_paciente, id_urgencia, fecha_admision, area_atencion, tipo_alta, nombre_convenio, ley_urgencia, fecha_foto, fecha_registro, ID_DERIVACION, FOLIO, ESTADO_VALIDACION) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
            GetSQLValueString($value['rut_paciente'], "text"),
            GetSQLValueString($value['id_urgencia'], "text"),
            GetSQLValueString($value['fecha_admision'], "date"),
            GetSQLValueString($value['area_atencion'], "date"),
            GetSQLValueString($value['tipo_alta'], "text"),
            GetSQLValueString($value['nombre_convenio'], "text"),
            GetSQLValueString($value['ley_urgencia'], "text"),
            GetSQLValueString($value['fecha_foto'], "date"),
            GetSQLValueString($auditoria, "date"),
            GetSQLValueString($idDerivacion, "int"),
            GetSQLValueString($folioRn, "text"),
            GetSQLValueString('sin validar', "text"));
        $Result1NDiario = $oirs->Execute($insertSQLNDiario) or die($oirs->ErrorMsg());

        $insertSQLNHistorico = sprintf("INSERT INTO $MM_oirs_DATABASE.api_urgencias_historico (rut_paciente, id_urgencia, fecha_admision, area_atencion, tipo_alta, nombre_convenio, ley_urgencia, fecha_foto, fecha_registro, ID_DERIVACION, FOLIO) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
            GetSQLValueString($value['rut_paciente'], "text"),
            GetSQLValueString($value['id_urgencia'], "text"),
            GetSQLValueString($value['fecha_admision'], "date"),
            GetSQLValueString($value['area_atencion'], "date"),
            GetSQLValueString($value['tipo_alta'], "text"),
            GetSQLValueString($value['nombre_convenio'], "text"),
            GetSQLValueString($value['ley_urgencia'], "text"),
            GetSQLValueString($value['fecha_foto'], "date"),
            GetSQLValueString($auditoria, "date"),
            GetSQLValueString($idDerivacion, "int"),
            GetSQLValueString($folioRn, "text"));
        $Result1NHistorico = $oirs->Execute($insertSQLNHistorico) or die($oirs->ErrorMsg());


        //registro en bitacora la nueva hospitalizacion
        $comentarioBitacora = 'El paciente '.utf8_encode($nomPaciente).' registra un ingreso a urgencias con id de admision '.$value['id_urgencia'].' en area de atencion '.$value['area_atencion'];
        $asunto= 'Ingreso urgencia';

        $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
            GetSQLValueString($idDerivacion, "text"), 
            GetSQLValueString('99.999.999-9', "text"),
            GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
            GetSQLValueString($asunto, "text"),
            GetSQLValueString($auditoria, "date"),
            GetSQLValueString($hora, "date"));
        $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


        $query_qrLogin = "SELECT * FROM $MM_oirs_DATABASE.login WHERE ID = '$gestora'";
        $qrLogin = $oirs->SelectLimit($query_qrLogin) or die($oirs->ErrorMsg());
        $totalRows_qrLogin = $qrLogin->RecordCount();

        $receptor = $qrLogin->Fields('USUARIO');

        $estadoNoti = 'nuevo';

        $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($idDerivacion, "int"),
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
            $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "int"),
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