<?php
//headers necesarios para identificar el dominio que se autoriza para realizar consultas mediante api
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD, OPTIONS");
header("Access-Control-Allow-Headers: *");

//definimos con header el tipo del documento (JSON)
header("Content-Type:application/json");

function formatearRUT($rut) {
    // Separar el número del RUT del dígito verificador
    list($numero, $dv) = explode('-', $rut);

    // Invertir el número del RUT para facilitar la adición de puntos cada tres dígitos
    $numeroInvertido = strrev($numero);
    $numeroFormateadoInvertido = '';

    // Añadir puntos cada tres dígitos
    for ($i = 0; $i < strlen($numeroInvertido); $i++) {
        if ($i > 0 && $i % 3 == 0) {
            $numeroFormateadoInvertido .= '.';
        }
        $numeroFormateadoInvertido .= $numeroInvertido[$i];
    }

    // Volver a invertir el número formateado para obtener el RUT correcto
    $numeroFormateado = strrev($numeroFormateadoInvertido);

    // Retornar el RUT formateado con el dígito verificador
    return $numeroFormateado . '-' . $dv;
}

//Conexion
require_once "../../../Connections/oirs.php";
require_once "../../../includes/functions.inc.php"; 

 date_default_timezone_set("America/Santiago");
    $auditoria= date("Y-m-d");
    $fecha= date("Y-m-d");
    $hora = date("G:i");

//recojemos el Json
$arr  = file_get_contents('php://input');
echo $jsonString = $arr;

//DECODIFICA EL JSON RECIBIDO Y LO CONVIERTE EN ARREGO Y LO RECORRE CON EL foreach
$array = json_decode($jsonString, true);
foreach ($array as $value) {


    //Con el rut del paciente voy buscar el id derivacion y folio para relacionar el censo con la derivacion del paciente *********************************
    $rutPac = $value['rut_paciente'];

    $rutPacFormateado = formatearRUT($rutPac);
    


    $query_qrPacientes = "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$rutPacFormateado'";
    $qrPacientes = $oirs->SelectLimit($query_qrPacientes) or die($oirs->ErrorMsg());
    $totalRows_qrPacientes = $qrPacientes->RecordCount();

    $idPaciente = $qrPacientes->Fields('ID');
    $nomPaciente = $qrPacientes->Fields('NOMBRE');

    $query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE COD_RUTPAC = '$rutPacFormateado' AND ESTADO_RN <> 'anulado'";
    $qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
    $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

    $idDerivacion = $qrDerivacion->Fields('ID_DERIVACION');
    $folioRn = $qrDerivacion->Fields('FOLIO');
    $gestora = $qrDerivacion->Fields('ENFERMERA');
    //*****************************************************************************************************************************************************


    // inserto en historico***********************************************************************************
    $insertSQLHistorico = sprintf("INSERT INTO $MM_oirs_DATABASE.2_api_censo_historico (rut_paciente, id_admision, fecha_censo, fecha_ingreso, dias_ingresado, codigo_prestacion, diagnostico, nombre_convenio, ley_urgencia, fecha_foto, fecha_registro, ID_DERIVACION, FOLIO) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
        GetSQLValueString($value['rut_paciente'], "text"),
        GetSQLValueString($value['id_admision'], "text"),
        GetSQLValueString($value['fecha_censo'], "date"),
        GetSQLValueString($value['fecha_ingreso'], "date"),
        GetSQLValueString($value['dias_ingresado'], "int"),
        GetSQLValueString($value['codigo_prestacion'], "text"),
        GetSQLValueString(utf8_decode($value['diagnostico']), "text"),
        GetSQLValueString(utf8_decode($value['nombre_convenio']), "text"),
        GetSQLValueString($value['ley_urgencia'], "text"),
        GetSQLValueString($value['fecha_foto'], "date"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($idDerivacion, "int"),
        GetSQLValueString($folioRn, "text"));
    $Result1Historico = $oirs->Execute($insertSQLHistorico) or die($oirs->ErrorMsg()); 
    //**********************************************************************************************************

    $idAdmision = $value['id_admision'];

    $query_qrCenso = "SELECT * FROM $MM_oirs_DATABASE.2_api_censo WHERE id_admision = '$idAdmision'";
    $qrCenso = $oirs->SelectLimit($query_qrCenso) or die($oirs->ErrorMsg());
    $totalRows_qrCenso = $qrCenso->RecordCount();

    if ($totalRows_qrCenso > 0) {
        $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_api_censo SET dias_ingresado=%s, ley_urgencia=%s, fecha_foto=%s, fecha_registro=%s, fecha_censo=%s WHERE id_admision= '$idAdmision'",
            GetSQLValueString($value['dias_ingresado'], "int"),
            GetSQLValueString($value['ley_urgencia'], "text"),
            GetSQLValueString($value['fecha_foto'], "date"),
            GetSQLValueString($auditoria, "date"),
            GetSQLValueString($value['fecha_censo'], "date"));
        $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

    }else{
        // inserto en censo diario***********************************************************************************
    $insertSQLDiario = sprintf("INSERT INTO $MM_oirs_DATABASE.2_api_censo (rut_paciente, id_admision, fecha_censo, fecha_ingreso, dias_ingresado, codigo_prestacion, diagnostico, nombre_convenio, ley_urgencia, fecha_foto, fecha_registro, ID_DERIVACION, FOLIO, ESTADO) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
        GetSQLValueString($value['rut_paciente'], "text"),
        GetSQLValueString($value['id_admision'], "text"),
        GetSQLValueString($value['fecha_censo'], "date"),
        GetSQLValueString($value['fecha_ingreso'], "date"),
        GetSQLValueString($value['dias_ingresado'], "int"),
        GetSQLValueString($value['codigo_prestacion'], "text"),
        GetSQLValueString(utf8_decode($value['diagnostico']), "text"),
        GetSQLValueString(utf8_decode($value['nombre_convenio']), "text"),
        GetSQLValueString($value['ley_urgencia'], "text"),
        GetSQLValueString($value['fecha_foto'], "date"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($idDerivacion, "int"),
        GetSQLValueString($folioRn, "text"),
        GetSQLValueString('sin validar', "text"));
    $Result1Diario = $oirs->Execute($insertSQLDiario) or die($oirs->ErrorMsg()); 
    //**********************************************************************************************************


    //registro en bitacora la nueva hospitalizacion
    $comentarioBitacora = 'El paciente '.utf8_encode($nomPaciente).' registra un ingreso hospitalario con id de admision '.$value['id_admision'].' y codigo de prestacion '.$value['codigo_prestacion'];
    $asunto= 'Ingreso hospitalario';

    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacion, "int"), 
        GetSQLValueString($folioRn, "text"), 
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

    $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
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
        $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
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


    // alarma a supervisora y gestora
    // notificacion supervisora y gestora
    }

}


//******************************BUSCA EGRESOS HOSPITALARIOS********************************************************************

$cargaAnterior = date("Y-m-d",strtotime($auditoria."- 1 days")); 

$query_qrCargaAnterior = ("SELECT FOLIO, rut_paciente, id_admision, codigo_prestacion FROM 2_api_censo_historico WHERE fecha_registro = '$cargaAnterior'");
$qrCargaAnterior = $oirs->SelectLimit($query_qrCargaAnterior) or die($oirs->ErrorMsg());
$totalRows_qrCargaAnterior = $qrCargaAnterior->RecordCount();

 while (!$qrCargaAnterior->EOF) { 

  $folioAnterior = $qrCargaAnterior->Fields('FOLIO');
  $rutPaciente = $qrCargaAnterior->Fields('rut_paciente');
  $idAdmisionCenso = $qrCargaAnterior->Fields('id_admision');
  $codPrestacion = $qrCargaAnterior->Fields('codigo_prestacion');

  $query_qrPacientes2 = "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$rutPaciente'";
  $qrPacientes2 = $oirs->SelectLimit($query_qrPacientes2) or die($oirs->ErrorMsg());
  $totalRows_qrPacientes2 = $qrPacientes2->RecordCount();

  $nomPaciente2 = $qrPacientes2->Fields('NOMBRE');

    $query_qrBuscaFolioAnulado = ("SELECT * FROM 2_api_censo_historico WHERE FOLIO = '$folioAnterior' and fecha_registro = '$auditoria'");
    $qrBuscaFolioAnulado = $oirs->SelectLimit($query_qrBuscaFolioAnulado) or die($oirs->ErrorMsg());
    $totalRows_qrBuscaFolioAnulado = $qrBuscaFolioAnulado->RecordCount();

     // echo $totalRows_qrBuscaFolioAnulado.'---' ;
    if ($totalRows_qrBuscaFolioAnulado == 0) {

        $query_qrBuscaIdDerivacion = ("SELECT * FROM 2_derivaciones WHERE FOLIO = '$folioAnterior'");
        $qrBuscaIdDerivacion = $oirs->SelectLimit($query_qrBuscaIdDerivacion) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaIdDerivacion = $qrBuscaIdDerivacion->RecordCount();

        $idDerivacion = $qrBuscaIdDerivacion->Fields('ID_DERIVACION');

        $comentarioBitacora = 'El paciente '.utf8_encode($nomPaciente2).' registra un egreso hospitalario del id de admision '.$idAdmisionCenso.' y codigo de prestacion '.$codPrestacion;
        $asunto= 'Egreso hospitalario';

        $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION,FOLIO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($idDerivacion, "int"), 
            GetSQLValueString($folioRn, "text"), 
            GetSQLValueString('99.999.999-9', "text"),
            GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
            GetSQLValueString($asunto, "text"),
            GetSQLValueString($fecha, "date"),
            GetSQLValueString($hora, "date"));
        $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

        //***************************************************

        $gestora = $qrBuscaIdDerivacion->Fields('ENFERMERA');


        $query_qrLoginEgreso = "SELECT * FROM $MM_oirs_DATABASE.login WHERE ID = '$gestora'";
        $qrLoginEgreso = $oirs->SelectLimit($query_qrLoginEgreso) or die($oirs->ErrorMsg());
        $totalRows_qrLoginEgreso = $qrLoginEgreso->RecordCount();

        $receptor = $qrLoginEgreso->Fields('USUARIO');

        $estadoNoti = 'nuevo';

        $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($idDerivacion, "int"),
            GetSQLValueString($receptor, "text"),
            GetSQLValueString(utf8_decode($asunto), "text"),
            GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
            GetSQLValueString($fecha, "date"),
            GetSQLValueString($hora, "date"),
            GetSQLValueString($estadoNoti, "text"),
            GetSQLValueString('99.999.999-9', "text"));
        $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());


        //busco los perfiles de supervisor para asignarles la notificacion de nueva hospitalizacion
        $query_qrBuscaSupervisorEgreso = "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO='2'";
        $qrBuscaSupervisorEgreso = $oirs->SelectLimit($query_qrBuscaSupervisorEgreso) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaSupervisorEgreso = $qrBuscaSupervisorEgreso->RecordCount(); 

        while (!$qrBuscaSupervisorEgreso->EOF) {
            $supervisor = $qrBuscaSupervisorEgreso->Fields('USUARIO');
            $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "int"),
                GetSQLValueString($supervisor, "text"),
                GetSQLValueString(utf8_decode($asunto), "text"),
                GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                GetSQLValueString($fecha, "date"),
                GetSQLValueString($hora, "date"),
                GetSQLValueString($estadoNoti, "text"),
                GetSQLValueString('99.999.999-9', "text"));
            $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
        $qrBuscaSupervisorEgreso->MoveNext(); }
        //**********************************************************************************************
        
    }

    $qrCargaAnterior->MoveNext(); 
}





