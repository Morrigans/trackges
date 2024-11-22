<?php
//headers necesarios para identificar el dominio que se autoriza para realizar consultas mediante api
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD, OPTIONS");
header("Access-Control-Allow-Headers: *");

// Verificar si se proporcionó un token de acceso y si es válido
$token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if ($token !== 'Bearer $P$BzHUsxIgVwVh4HoD9P6YgTGxo7wQYm1') {
    http_response_code(401); // Unauthorized
    echo json_encode(array("error" => "Tocken no autorizado"));
    exit();
}

//definimos con header el tipo del documento (JSON)
header("Content-Type:application/json");

require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$idClinica = '19';
$idSesion = '99.999.999-9';

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

    $folio = $value['Folio_Padre'];
    $estadoPadre = utf8_decode($value['Estado_Padre']);
    $lateralidad = utf8_decode($value['Lateralidad']);
    $rutPac = utf8_decode($value['Rut_Paciente']);
    $nombrePaciente = utf8_decode($value['Nombre_Paciente']);
    $proceso = utf8_decode($value['Proceso']);
    $categoria = utf8_decode($value['Categoria']);
    $iSanitaria = utf8_decode($value['Intervención_Sanitaria_Actual']);
    $fechaDeivacion = $value['Fecha_Asignación'];
    $problemaSalud = utf8_decode($value['Especialidad']);

    //busco si problema de salud existe
    $query_qrBuscaProbleSalud = ("SELECT * FROM $MM_oirs_DATABASE.2_problemas_salud WHERE PROBLEMA_SALUD = '$problemaSalud'");
    $qrBuscaProbleSalud = $oirs->SelectLimit($query_qrBuscaProbleSalud) or die($oirs->ErrorMsg());
    $totalRows_qrBuscaProbleSalud = $qrBuscaProbleSalud->RecordCount();

    if ($totalRows_qrBuscaProbleSalud == 0) {//si no existe
        //insertar el nuevo problema de salud
        $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_problemas_salud (PROBLEMA_SALUD) VALUES (%s)",
            GetSQLValueString($problemaSalud, "text"));
        $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
    }

    // estado padre cuando sea anulado vendra con texto como "Anulado:motivo de anulacion"
    if (strpos($estadoPadre, 'Anulado') === 0) {
        $estadoPadre = 'Anulado';
    }

    //busco si cambio estadoPadre o iSanitaria y ademas si la derivacion existe
    $query_qrBuscaCambioEstadoIsanitaria = ("SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE FOLIO = '$folio'");
    $qrBuscaCambioEstadoIsanitaria = $oirs->SelectLimit($query_qrBuscaCambioEstadoIsanitaria) or die($oirs->ErrorMsg());
    $totalRows_qrBuscaCambioEstadoIsanitaria = $qrBuscaCambioEstadoIsanitaria->RecordCount();

    //aprobecho la consulta para obtener el ID_DERIVACION para insertar en las tablas de cambio de estado y cambio de isanitaria
    $idDerivacion = $qrBuscaCambioEstadoIsanitaria->Fields('ID_DERIVACION');


    if ($totalRows_qrBuscaCambioEstadoIsanitaria > 0) {// verifico que el folio exista en derivaciones padre, si es asi actualizo datos, sino inserto nueva derivacion padre
            //saco el estado actual en BD del estado Padre y la intervencio Sanitaria
            $estadoRnExistente = $qrBuscaCambioEstadoIsanitaria->Fields('ESTADO_RN');
            $iSanitariaExistente = $qrBuscaCambioEstadoIsanitaria->Fields('INTERVENCION_SANITARIA');

            if ($estadoRnExistente != $estadoPadre) { //si el estado actual en bd es distinto al estado que viene en la api debe hacer un update al estado de la derivacion padre
                $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_derivaciones SET ESTADO_RN=%s WHERE FOLIO= '$folio'",
                            GetSQLValueString($estadoPadre, "text"));
                $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

                //insertar el cambio de estado
                $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_estados_derivacion_rn (ID_DERIVACION, FOLIO, ESTADO, FECHA_REGISTRO, HORA_REGISTRO) VALUES (%s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"), 
                    GetSQLValueString($folio, "text"), 
                    GetSQLValueString($estadoPadre, "text"), 
                    GetSQLValueString($fecha, "date"), 
                    GetSQLValueString($hora, "date"));
                $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

                $comentarioBitacora = 'Cambio de estado '.utf8_encode($estadoRnExistente).' a estado '.$value['Estado_Padre'].' para Folio Right Now '.$folio;

                //si el estado padre es anulado, cambio el asunto para que se refleje en bitacora
                if ($estadoPadre == 'Anulado') {
                    $asunto= 'Folio Anulado';
                }else{
                    $asunto= 'Cambio estado';
                }
                

                // Inserto comentario cambio de estado en bitacora ********************************************************************************
                $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"), 
                    GetSQLValueString($folio, "text"), 
                    GetSQLValueString($idSesion, "text"),
                    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                    GetSQLValueString($asunto, "text"),
                    GetSQLValueString($fecha, "date"),
                    GetSQLValueString($hora, "date"));
                $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
                //********************************************************************************************************************
            }
            if ($iSanitariaExistente != $iSanitaria) { //si iSanitaria actual en bd es distinto a iSanitaria que viene en la api debe hacer un update al iSanitaria de la derivacion padre
                $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_derivaciones SET INTERVENCION_SANITARIA=%s WHERE FOLIO= '$folio'",
                            GetSQLValueString($iSanitaria, "text"));
                $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

                //insertar el cambio de isanitaria
                $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_estados_isanitarias_derivacion_rn (ID_DERIVACION, FOLIO, ESTADO, FECHA_REGISTRO, HORA_REGISTRO) VALUES (%s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"), 
                    GetSQLValueString($folio, "text"), 
                    GetSQLValueString($iSanitaria, "text"), 
                    GetSQLValueString($fecha, "date"), 
                    GetSQLValueString($hora, "date"));
                $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

                $comentarioBitacora = 'Cambio de intervención sanitaria '.$iSanitariaExistente.' a intervención sanitaria '.$iSanitaria.' para Folio Right Now '.$folio;
                $asunto= 'Cambio Int.Sanitaria';

                // Inserto comentario cambio de estado en bitacora ********************************************************************************
                $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"), 
                    GetSQLValueString($folio, "text"), 
                    GetSQLValueString($idSesion, "text"),
                    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                    GetSQLValueString($asunto, "text"),
                    GetSQLValueString($fecha, "date"),
                    GetSQLValueString($hora, "date"));
                $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
                //********************************************************************************************************************
            }

    }else{ // sino existe folio en derivaciones padre, inserto nueva derivacion padre
            // Crea la derivacion con datos iniciales *****************************************************************************
            $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_derivaciones (FOLIO, FECHA_DERIVACION, LATERALIDAD, COD_RUTPAC, PROCESO, CATEGORIA, INTERVENCION_SANITARIA, FECHA_REGISTRO, HORA_REGISTRO, SESION, ESTADO_RN, PROBLEMA_SALUD) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($folio, "text"), 
                GetSQLValueString($fechaDeivacion, "date"), 
                GetSQLValueString($lateralidad, "text"), 
                GetSQLValueString($rutPac, "text"), 
                GetSQLValueString($proceso, "text"), 
                GetSQLValueString($categoria, "text"), 
                GetSQLValueString($iSanitaria, "text"), 
                GetSQLValueString($fecha, "date"),
                GetSQLValueString($hora, "date"),
                GetSQLValueString($idSesion, "text"),
                GetSQLValueString($estadoPadre, "text"),
                GetSQLValueString($problemaSalud, "text"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

            // Capturo id derivacion creado para para agregarlo a la tabla RN y relacionarlo con derivaciones
            $query_qrDerivacionUlt = ("SELECT max(ID_DERIVACION) as ID_DERIVACION FROM $MM_oirs_DATABASE.2_derivaciones");
            $qrDerivacionUlt = $oirs->SelectLimit($query_qrDerivacionUlt) or die($oirs->ErrorMsg());
            $totalRows_qrDerivacionUlt = $qrDerivacionUlt->RecordCount();

            $idDerivacion = $qrDerivacionUlt->Fields('ID_DERIVACION');

            // Agrego N_DERIVACION
            $nderivacion = 'R0'.$idDerivacion;

            $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_derivaciones SET N_DERIVACION=%s WHERE ID_DERIVACION= '$idDerivacion'",
                        GetSQLValueString($nderivacion, "text"));
            $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

            $comentarioBitacora = 'Se crea Folio Right Now '.$folio;
            $asunto= 'Creada';

            // Inserto comentario nueva derivacion ********************************************************************************
            $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "int"), 
                GetSQLValueString($folio, "text"), 
                GetSQLValueString($idSesion, "text"),
                GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                GetSQLValueString($asunto, "text"),
                GetSQLValueString($fecha, "date"),
                GetSQLValueString($hora, "date"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
            //********************************************************************************************************************

            //insertar el estado con el que vine la nueva derivacion
            $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_estados_derivacion_rn (ID_DERIVACION, FOLIO, ESTADO, FECHA_REGISTRO, HORA_REGISTRO) VALUES (%s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "int"), 
                GetSQLValueString($folio, "text"), 
                GetSQLValueString($estadoPadre, "text"), 
                GetSQLValueString($fecha, "date"), 
                GetSQLValueString($hora, "date"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


             //buscar si existe el rut en la tabla pacientes
            $query_qrBuscaPcte = ("SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$rutPac'");
            $qrBuscaPcte = $oirs->SelectLimit($query_qrBuscaPcte) or die($oirs->ErrorMsg());
            $totalRows_qrBuscaPcte = $qrBuscaPcte->RecordCount();

            // if que evalua si existe o no el paciente en la tabla pacientes **********************************************
            if ($totalRows_qrBuscaPcte > 0) {
                //si existe no hace nada
                // $idPaciente = $qrBuscaPcte->Fields('ID');
            }else{
            //si el paciente no existe en el mantenedor debo agregarlo
                $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.pacientes (COD_RUTPAC, NOMBRE) VALUES (%s, %s)",
                    GetSQLValueString($rutPac, "text"), 
                    GetSQLValueString($nombrePaciente, "text"));
                $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
            }
    }// fin totalRows_qrBuscaCambioEstadoIsanitaria > 0 para ver si la derivacion existe
      
   
   //extraigo los datos del folio hijo
   $folioHijo = utf8_decode($value['Folio_Hijo']);
   $etapa = utf8_decode($value['Etapa']);
   $tipoCompra = utf8_decode($value['Tipo_compra']);
   $descripcion = utf8_decode($value['Descripcion']);
   $montoPrestacion = utf8_decode($value['Monto_prestacion']);
   $montoAt = utf8_decode($value['Monto_AT']);
   $total = utf8_decode($value['Total']);
   $estadoHijo = utf8_decode($value['Estado_Hijo']);

   
   if ($folioHijo == '') {//evaluo si viene hijo con la derivacion
       //no debo insertar hijo
   }else{

        //busco si el folio hijo ya existe
        $query_qrBuscaFolioHijo = ("SELECT * FROM $MM_oirs_DATABASE.2_derivaciones_hijos WHERE FOLIO_HIJO = '$folioHijo'");
        $qrBuscaFolioHijo = $oirs->SelectLimit($query_qrBuscaFolioHijo) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaFolioHijo = $qrBuscaFolioHijo->RecordCount();

        $idDerivacionHijo = $qrBuscaFolioHijo->Fields('ID_DERIVACION_HIJO');

        if ($totalRows_qrBuscaFolioHijo > 0) { //si folio hijo ya existe
            //capturo las variables que suriran cambios desde RN
            $estadoHijoExistente = $qrBuscaFolioHijo->Fields('ESTADO_HIJO');
            $montoPrestacionExistente = $qrBuscaFolioHijo->Fields('MONTO_PRESTACION');
            $montoAtExistente = $qrBuscaFolioHijo->Fields('MONTO_AT');
            $totalExistente = $qrBuscaFolioHijo->Fields('TOTAL');

            if ($estadoHijoExistente != $estadoHijo) {

                $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_derivaciones_hijos SET ESTADO_HIJO=%s WHERE FOLIO_HIJO= '$folioHijo'",
                            GetSQLValueString($estadoHijo, "text"));
                $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

                //insertar el cambio de estado hijo
                $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_estados_derivacion_hijos_rn (ID_DERIVACION, ID_DERIVACION_HIJO, FOLIO_HIJO, FOLIO, ESTADO, FECHA_REGISTRO, HORA_REGISTRO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "int"), 
                GetSQLValueString($idDerivacionHijo, "int"), 
                GetSQLValueString($folioHijo, "text"), 
                GetSQLValueString($folio, "text"), 
                GetSQLValueString($estadoHijo, "text"), 
                GetSQLValueString($fecha, "date"), 
                GetSQLValueString($hora, "date"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

                $comentarioBitacora = 'Cambio de estado en Folio Hijo '.$folioHijo.' de '.$estadoHijoExistente.' a '.$estadoHijo.' para Folio Right Now '.$folio;
                $asunto= 'Cambio est. folio hijo';

                // Inserto comentario cambio de estado en bitacora ********************************************************************************
                $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, ID_DERIVACION_HIJO, FOLIO_HIJO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"), 
                    GetSQLValueString($folio, "text"), 
                    GetSQLValueString($idDerivacionHijo, "int"), 
                    GetSQLValueString($folioHijo, "text"), 
                    GetSQLValueString($idSesion, "text"),
                    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                    GetSQLValueString($asunto, "text"),
                    GetSQLValueString($fecha, "date"),
                    GetSQLValueString($hora, "date"));
                $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
                //********************************************************************************************************************
            }

            if ($montoPrestacionExistente != $montoPrestacion) {
                    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_derivaciones_hijos SET MONTO_PRESTACION=%s WHERE FOLIO_HIJO= '$folioHijo'",
                                GetSQLValueString($montoPrestacion, "text"));
                    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

                    //insertar el cambio de monto prestacion
                    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_estados_monto_prestacion_rn (ID_DERIVACION, ID_DERIVACION_HIJO, FOLIO_HIJO, FOLIO, MONTO, FECHA_REGISTRO, HORA_REGISTRO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"), 
                    GetSQLValueString($idDerivacionHijo, "int"), 
                    GetSQLValueString($folioHijo, "text"), 
                    GetSQLValueString($folio, "text"), 
                    GetSQLValueString($montoPrestacion, "text"), 
                    GetSQLValueString($fecha, "date"), 
                    GetSQLValueString($hora, "date"));
                $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

                    $comentarioBitacora = 'Cambio de monto prestación en Folio Hijo '.$folioHijo.' de '.$montoPrestacionExistente.' a '.$montoPrestacion.' para Folio Right Now '.$folio;
                    $asunto= 'Monto prest. folio hijo';

                    // Inserto comentario cambio de monto en bitacora ********************************************************************************
                    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, ID_DERIVACION_HIJO, FOLIO_HIJO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                        GetSQLValueString($idDerivacion, "int"), 
                        GetSQLValueString($folio, "text"), 
                        GetSQLValueString($idDerivacionHijo, "int"), 
                        GetSQLValueString($folioHijo, "text"), 
                        GetSQLValueString($idSesion, "text"),
                        GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                        GetSQLValueString($asunto, "text"),
                        GetSQLValueString($fecha, "date"),
                        GetSQLValueString($hora, "date"));
                    $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
                    //********************************************************************************************************************
            }
            if ($montoAtExistente != $montoAt) {
                    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_derivaciones_hijos SET MONTO_AT=%s WHERE FOLIO_HIJO= '$folioHijo'",
                                GetSQLValueString($montoAt, "text"));
                    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

                    //insertar el cambio de monto at
                    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_estados_monto_at_rn (ID_DERIVACION, ID_DERIVACION_HIJO, FOLIO_HIJO, FOLIO, MONTO, FECHA_REGISTRO, HORA_REGISTRO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"), 
                    GetSQLValueString($idDerivacionHijo, "int"), 
                    GetSQLValueString($folioHijo, "text"), 
                    GetSQLValueString($folio, "text"), 
                    GetSQLValueString($montoAt, "text"), 
                    GetSQLValueString($fecha, "date"), 
                    GetSQLValueString($hora, "date"));
                $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

                    $comentarioBitacora = 'Cambio de monto AT en Folio Hijo '.$folioHijo.' de '.$montoAtExistente.' a '.$montoAt.' para Folio Right Now '.$folio;
                    $asunto= 'Monto AT folio hijo';

                    // Inserto comentario cambio de monto en bitacora ********************************************************************************
                    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, ID_DERIVACION_HIJO, FOLIO_HIJO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                        GetSQLValueString($idDerivacion, "int"), 
                        GetSQLValueString($folio, "text"), 
                        GetSQLValueString($idDerivacionHijo, "int"), 
                        GetSQLValueString($folioHijo, "text"), 
                        GetSQLValueString($idSesion, "text"),
                        GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                        GetSQLValueString($asunto, "text"),
                        GetSQLValueString($fecha, "date"),
                        GetSQLValueString($hora, "date"));
                    $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
                    //********************************************************************************************************************
            }
            if ($totalExistente != $total) {
                 $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_derivaciones_hijos SET TOTAL=%s WHERE FOLIO_HIJO= '$folioHijo'",
                                GetSQLValueString($total, "text"));
                    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

                    //insertar el cambio de monto at
                    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_estados_total_rn (ID_DERIVACION, ID_DERIVACION_HIJO, FOLIO_HIJO, FOLIO, MONTO, FECHA_REGISTRO, HORA_REGISTRO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"), 
                    GetSQLValueString($idDerivacionHijo, "int"), 
                    GetSQLValueString($folioHijo, "text"), 
                    GetSQLValueString($folio, "text"), 
                    GetSQLValueString($total, "text"), 
                    GetSQLValueString($fecha, "date"), 
                    GetSQLValueString($hora, "date"));
                $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

                    $comentarioBitacora = 'Cambio de monto total en Folio Hijo '.$folioHijo.' de '.$totalExistente.' a '.$total.' para Folio Right Now '.$folio;
                    $asunto= 'Monto total folio hijo';

                    // Inserto comentario cambio de monto en bitacora ********************************************************************************
                    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, ID_DERIVACION_HIJO, FOLIO_HIJO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                        GetSQLValueString($idDerivacion, "int"), 
                        GetSQLValueString($folio, "text"), 
                        GetSQLValueString($idDerivacionHijo, "int"), 
                        GetSQLValueString($folioHijo, "text"), 
                        GetSQLValueString($idSesion, "text"),
                        GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                        GetSQLValueString($asunto, "text"),
                        GetSQLValueString($fecha, "date"),
                        GetSQLValueString($hora, "date"));
                    $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
                    //********************************************************************************************************************
            }

        }else{
            //insertar folio hijo
            $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_derivaciones_hijos (ID_DERIVACION, FOLIO, FOLIO_HIJO, ETAPA, TIPO_COMPRA, DESCRIPCION, MONTO_PRESTACION, MONTO_AT, TOTAL, ESTADO_HIJO, FECHA_REGISTRO, HORA_REGISTRO, SESION) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "int"), 
                GetSQLValueString($folio, "text"), 
                GetSQLValueString($folioHijo, "date"), 
                GetSQLValueString($etapa, "text"), 
                GetSQLValueString($tipoCompra, "text"), 
                GetSQLValueString($descripcion, "text"), 
                GetSQLValueString($montoPrestacion, "text"), 
                GetSQLValueString($montoAt, "text"), 
                GetSQLValueString($total, "text"),            
                GetSQLValueString($estadoHijo, "text"),
                GetSQLValueString($fecha, "date"),
                GetSQLValueString($hora, "date"),
                GetSQLValueString($idSesion, "text"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

            // Capturo id derivacion creado para para agregarlo a la tabla RN y relacionarlo con derivaciones
            $query_qrDerivacionHijoUlt = ("SELECT max(ID_DERIVACION_HIJO) as ID_DERIVACION_HIJO FROM $MM_oirs_DATABASE.2_derivaciones_hijos");
            $qrDerivacionHijoUlt = $oirs->SelectLimit($query_qrDerivacionHijoUlt) or die($oirs->ErrorMsg());
            $totalRows_qrDerivacionHijoUlt = $qrDerivacionHijoUlt->RecordCount();

            $idDerivacionHijo = $qrDerivacionHijoUlt->Fields('ID_DERIVACION_HIJO');

            $comentarioBitacora = 'Se crea Folio Hijo número '.$folioHijo.' con un monto de $'.$total.' para Folio Right Now '.$folio;
            $asunto= 'Creado';

            // Inserto comentario nueva derivacion hijo ********************************************************************************
            $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, ID_DERIVACION_HIJO, FOLIO, FOLIO_HIJO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "int"), 
                GetSQLValueString($idDerivacionHijo, "int"), 
                GetSQLValueString($folioHijo, "text"), 
                GetSQLValueString($folio, "text"),  
                GetSQLValueString($idSesion, "text"),
                GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                GetSQLValueString($asunto, "text"),
                GetSQLValueString($fecha, "date"),
                GetSQLValueString($hora, "date"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
            //********************************************************************************************************************

            //insertar el cambio de estado hijo
                $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_estados_derivacion_hijos_rn (ID_DERIVACION, ID_DERIVACION_HIJO, FOLIO_HIJO, FOLIO, ESTADO, FECHA_REGISTRO, HORA_REGISTRO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "int"), 
                GetSQLValueString($idDerivacionHijo, "int"), 
                GetSQLValueString($folioHijo, "text"), 
                GetSQLValueString($folio, "text"), 
                GetSQLValueString($estadoHijo, "text"), 
                GetSQLValueString($fecha, "date"), 
                GetSQLValueString($hora, "date"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
        }//fin $totalRows_qrBuscaFolioHijo > 0
        
   }//$folioHijo == ''

       
}// fin del ciclo for each

//*****************************************************************************************************************************************

// Envía el arreglo de idAtencion en la respuesta JSON
echo json_encode(["message" => "Recibido correctamente"]);
?>

