<?php
//headers necesarios para identificar el dominio que se autoriza para realizar consultas mediante api
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD, OPTIONS");
header("Access-Control-Allow-Headers: *");

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

    //busco si cambio estadoPadre o iSanitaria
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

                $comentarioBitacora = 'Cambio de estado '.$estadoRnExistente.' a estado '.$estadoPadre.' para Folio Right Now '.$folio;
                $asunto= 'Cambio estado';

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
            $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_derivaciones (FOLIO, FECHA_DERIVACION, LATERALIDAD, COD_RUTPAC, PROCESO, CATEGORIA, INTERVENCION_SANITARIA, FECHA_REGISTRO, HORA_REGISTRO, SESION, ESTADO_RN) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
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
                GetSQLValueString($estadoPadre, "text"));
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

            $comentarioBitacora = 'Se crea Derivacion número '.$nderivacion.' para Folio Right Now '.$folio;
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
    }// fin totalRows_qrBuscaCambioEstadoIsanitaria > 0
      
   

       
}

//*****************************************************************************************************************************************

?>

