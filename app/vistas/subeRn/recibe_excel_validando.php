<?php
// Solo se permite el ingreso con el inicio de sesion.
  session_start();
  // Si el usuario no se ha logueado se le regresa al inicio.
  if (!isset($_SESSION['loggedin'])) {
  header('Location: ../../index.php');
  exit; }

require_once '../../Connections/oirs.php';
require_once '../../includes/functions.inc.php';

//require('config.php');

$tipo       = $_FILES['datosRn']['type'];
$tamanio    = $_FILES['datosRn']['size'];
$archivotmp = $_FILES['datosRn']['tmp_name'];
$lineas     = file($archivotmp);
$cantidadCasos = count($lineas);

$query_qrCasosRn = ("SELECT COUNT(FOLIO) as ncasos FROM rn");
$qrCasosRn = $oirs->SelectLimit($query_qrCasosRn) or die($oirs->ErrorMsg());
$totalRows_qrCasosRn = $qrCasosRn->RecordCount();

date_default_timezone_set('America/Santiago');
$hora= date('G:i'); //CAPTURA HORA ACTUAL
$fecha = date('Y-m-d');

$usuario = $_SESSION['dni'];

$query_qrTipoUsuario = "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$usuario'"; 
$qrTipoUsuario = $oirs->SelectLimit($query_qrTipoUsuario) or die($oirs->ErrorMsg());
$totalRows_qrTipoUsuario = $qrTipoUsuario->RecordCount();

$idClinica = $qrTipoUsuario->Fields('ID_PRESTADOR');
$idSesion = $qrTipoUsuario->Fields('ID');

// Capturo el ultimo id para extraer su numero de carga
$query_qrUltimoFolio = ("SELECT max(ID_ESTADOS_RN) as ID_ESTADOS_RN FROM $MM_oirs_DATABASE.rn_estados");
$qrUltimoFolio = $oirs->SelectLimit($query_qrUltimoFolio) or die($oirs->ErrorMsg());
$totalRows_qrUltimoFolio = $qrUltimoFolio->RecordCount();

$idUltimo = $qrUltimoFolio->Fields('ID_ESTADOS_RN');

$query_qrMax = "SELECT * FROM $MM_oirs_DATABASE.rn_estados WHERE ID_ESTADOS_RN = '$idUltimo'"; 
$qrMax = $oirs->SelectLimit($query_qrMax) or die($oirs->ErrorMsg());
$totalRows_qrMax = $qrMax->RecordCount();

$nroCarga = $qrMax->Fields('NUMERO_CARGA');

$nroCarga=$nroCarga+1;


$i = 0;

foreach ($lineas as $linea) {
    $cantidad_registros = count($lineas);
    $cantidad_regist_agregados =  ($cantidad_registros - 1);

    if ($i != 0) {

        $datos = explode(";", $linea);
       
        $folio               = !empty($datos[0])  ? ($datos[0]) : '';

        //formatea el rut que se sube de la planilla
        $rutPac                = !empty($datos[1])  ? ($datos[1]) : '';

		$nombre                = !empty($datos[2])  ? ($datos[2]) : '';
        $pSalud               = !empty($datos[3])  ? ($datos[3]) : '';
        $iSanitaria               = !empty($datos[4])  ? ($datos[4]) : '';
        $estado               = !empty($datos[5])  ? ($datos[5]) : '';
        $mTotal               = !empty($datos[6])  ? ($datos[6]) : '';
        $mensaje               = !empty($datos[7])  ? ($datos[7]) : '';

          
if( !empty($folio) ){
    // busca si los folios de la planilla coinciden con los de la tabla para insertar los nuevos o actualizar los existentes
    $query_qrDuplicados = "SELECT FOLIO FROM $MM_oirs_DATABASE.rn WHERE FOLIO = '$folio'"; 
    $qrDuplicados = $oirs->SelectLimit($query_qrDuplicados) or die($oirs->ErrorMsg());
    $totalRows_qrDuplicados = $qrDuplicados->RecordCount();
}   

//#################################### INSERTA DATOS AL NO EXISTIR FOLIOS DE LA PLANILLA EN LA TABLA###########################################################
if ( $totalRows_qrDuplicados == 0 ) { 

     //buscar si existe el rut en la tabla pacientes
    $query_qrBuscaPcte = ("SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$rutPac'");
    $qrBuscaPcte = $oirs->SelectLimit($query_qrBuscaPcte) or die($oirs->ErrorMsg());
    $totalRows_qrBuscaPcte = $qrBuscaPcte->RecordCount();

    // if que evalua si existe o no el paciente en la tabla pacientes **********************************************
    if ($totalRows_qrBuscaPcte > 0) {
        $idPaciente = $qrBuscaPcte->Fields('ID');
    }else{
    //si el paciente no existe en el mantenedor debo agregarlo
        $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.pacientes (COD_RUTPAC, NOMBRE) VALUES (%s, %s)",
            GetSQLValueString($rutPac, "text"), 
            GetSQLValueString($nombre, "text"));
        $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

    // Capturo id paciente creado para relacionarlo con la tabla de derivaciones
        $query_qrIdPaciente = ("SELECT max(ID) as ID FROM $MM_oirs_DATABASE.pacientes");
        $qrIdPaciente = $oirs->SelectLimit($query_qrIdPaciente) or die($oirs->ErrorMsg());
        $totalRows_qrIdPaciente = $qrIdPaciente->RecordCount();

        $idPaciente = $qrIdPaciente->Fields('ID');
    }//fin if existe paciente ***************************************************************************************


    // Crea estados locales basados en estado rn para insertar en tabla de derivaciones y estado_derivaciones*******************************************************************
        if (utf8_encode($estado) == 'Prestador asignado') {
            $estadoDerivacion = 'pendiente';
        }
        if (utf8_encode($estado) == 'Derivación Aceptada' or utf8_encode($estado) == 'Solicita autorización') {
            $estadoDerivacion = 'aceptada';
        }
        if (utf8_encode($estado) == 'Alta Paciente' or utf8_encode($estado) == 'Autorizado para pago' or utf8_encode($estado) == 'Validado para Pago') {
            $estadoDerivacion = 'cerrada';
        }
    //**************************************************************************************************************************************************************************

    // //buscar canasta para asociar a la derivacion
    // $query_qrPatologia = ("SELECT * FROM $MM_oirs_DATABASE.patologia WHERE DESC_PATOLOGIA = '$pSalud'");
    // $qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
    // $totalRows_qrPatologia = $qrPatologia->RecordCount();

    // $idPatologia = $qrPatologia->Fields('ID_PATOLOGIA');


// Crea la derivacion con datos iniciales *****************************************************************************
    // $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones (FOLIO, ID_CONVENIO, CODIGO_TIPO_PATOLOGIA, ESTADO, ESTADO_RN, MONTO_ACUMULADO_RN, AUDITORIA, ID_CLINICA, ID_PACIENTE, SESION) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    //     GetSQLValueString($folio, "text"), 
    //     GetSQLValueString('2', "int"), 
    //     GetSQLValueString('1', "text"), 
    //     //GetSQLValueString($idPatologia, "int"), 
    //     GetSQLValueString($estadoDerivacion, "text"), 
    //     GetSQLValueString($estado, "text"), 
    //     GetSQLValueString($mTotal, "int"), 
    //     GetSQLValueString($fecha, "date"), 
    //     GetSQLValueString($idClinica, "int"),
    //     GetSQLValueString($idPaciente, "int"),
    //     GetSQLValueString($idSesion, "int"));
    // $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

    $decreto = 'LEP2225';

    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones (FOLIO, ID_CONVENIO, FECHA_DERIVACION, CODIGO_TIPO_PATOLOGIA, ESTADO, ESTADO_RN, MONTO_ACUMULADO_RN, AUDITORIA, ID_CLINICA, ID_PACIENTE, SESION, DECRETO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($folio, "text"), 
        GetSQLValueString('2', "int"), 
        GetSQLValueString($fecha, "date"), 
        GetSQLValueString('1', "text"), 
        //GetSQLValueString($idPatologia, "int"), 
        GetSQLValueString($estadoDerivacion, "text"), 
        GetSQLValueString($estado, "text"), 
        GetSQLValueString($mTotal, "int"), 
        GetSQLValueString($fecha, "date"), 
        GetSQLValueString($idClinica, "int"),
        GetSQLValueString($idPaciente, "int"),
        GetSQLValueString($idSesion, "int"),
        GetSQLValueString($decreto, "text"));
    $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
// ********************************************************************************************************************

// Capturo id derivacion creado para para agregarlo a la tabla RN y relacionarlo con derivaciones
    $query_qrDerivacionUlt = ("SELECT max(ID_DERIVACION) as ID_DERIVACION FROM $MM_oirs_DATABASE.derivaciones");
    $qrDerivacionUlt = $oirs->SelectLimit($query_qrDerivacionUlt) or die($oirs->ErrorMsg());
    $totalRows_qrDerivacionUlt = $qrDerivacionUlt->RecordCount();

    $idDerivacion = $qrDerivacionUlt->Fields('ID_DERIVACION');

// Agrego N_DERIVACION
    $nderivacion = 'R0'.$idDerivacion;

    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET N_DERIVACION=%s WHERE ID_DERIVACION= '$idDerivacion'",
                GetSQLValueString($nderivacion, "text"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());


// Guarda estados locales basados en estado rn, segun los if de arriba *****************************************************************************
    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.estados_derivacion (ID_DERIVACION, ESTADO, FECHA_REGISTRO, HORA_REGISTRO) VALUES (%s, %s, %s, %s)",
        GetSQLValueString($idDerivacion, "int"), 
        GetSQLValueString($estadoDerivacion, "text"), 
        GetSQLValueString($fecha, "date"), 
        GetSQLValueString($hora, "date"));
    $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
// ********************************************************************************************************************

// Guarda monto de nuevas derivaciones rn en tabla montos y marcados como inicial *****************************************************************************
    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.montos (ID_DERIVACION, MONTO, TIPO_MONTO) VALUES (%s, %s, %s)",
        GetSQLValueString($idDerivacion, "int"), 
        GetSQLValueString($mTotal, "text"), 
        GetSQLValueString('inicial', "text"));
    $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
// ********************************************************************************************************************

// inserta los casos nuevos que vienen en la planilla
    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.rn (FOLIO, ID_DERIVACION, COD_RUTPAC, NOM_PACIENTE, PROBLEMA_SALUD, INTERVENCION_SANITARIA, ESTADO, MONTO_TOTAL, MENSAJE, FECHA_REGISTRO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($folio, "text"), 
        GetSQLValueString($idDerivacion, "int"), 
        GetSQLValueString($rutPac, "text"), 
        GetSQLValueString($nombre, "text"), 
        GetSQLValueString($pSalud, "text"), 
        GetSQLValueString($iSanitaria, "text"), 
        GetSQLValueString($estado, "text"), 
        GetSQLValueString($mTotal, "int"), 
        GetSQLValueString($mensaje, "text"), 
        GetSQLValueString($fecha, "date"));
    $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

// Capturo id RN creado para relacionarlo con la tabla de estados
    $query_qrIdRnUlt = ("SELECT max(ID_RN) as ID_RN FROM $MM_oirs_DATABASE.rn");
    $qrIdRnUlt = $oirs->SelectLimit($query_qrIdRnUlt) or die($oirs->ErrorMsg());
    $totalRows_qrIdRnUlt = $qrIdRnUlt->RecordCount();

    $idRn = $qrIdRnUlt->Fields('ID_RN');

// inserta el estado que viene en la planilla para tener historial de estados rn cada vez que se sube la planilla
        $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.rn_estados (ID_RN, FOLIO, ESTADO, MONTO_ACUMULADO_RN, FECHA_REGISTRO, NUMERO_CARGA, HORA_CARGA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($idRn, "int"), 
            GetSQLValueString($folio, "text"), 
            GetSQLValueString($estado, "text"),
            GetSQLValueString($mTotal, "int"),  
            GetSQLValueString($fecha, "date"),
            GetSQLValueString($nroCarga, "int"),
            GetSQLValueString($hora, "date"));
        $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

// Crea mensajes de bitacora, se personalizan segun estados para carga inicial*******************************************************************
        $ultimaCanasta = '0';
        $sesion = '99.999.999-9'; // RUT de administrador
        if (utf8_encode($estado) == 'Prestador asignado') {
            $estadoDerivacion = 'pendiente';
            $comentarioBitacora = 'Se crea Derivacion número '.$nderivacion.' para Folio Right Now '.$folio.' de paciente '.utf8_encode($nombre).' rut '.$rutPac;
            $asunto= 'Creada';
            $comentarioBitacoraMonto = 'Se asigna a Derivacion número '.$nderivacion.' Folio Right Now '.$folio.' un monto inicial de $'.number_format($mTotal);
            $asuntoMonto= 'Monto inicial';

            // Inserto comentario en bitacora ********************************************************************************
            $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, ID_CANASTA_PATOLOGIA, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "text"), 
                GetSQLValueString($ultimaCanasta, "text"),
                GetSQLValueString($sesion, "text"),
                GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                GetSQLValueString($asunto, "text"),
                GetSQLValueString($fecha, "date"),
                GetSQLValueString($hora, "date"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
            //********************************************************************************************************************

            //registro monto inicial en bitacora************************************************************************************************************************
            $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, ID_CANASTA_PATOLOGIA, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "text"), 
                GetSQLValueString($ultimaCanasta, "text"),
                GetSQLValueString($sesion, "text"),
                GetSQLValueString(utf8_decode($comentarioBitacoraMonto), "text"),
                GetSQLValueString($asuntoMonto, "text"),
                GetSQLValueString($fecha, "date"),
                GetSQLValueString($hora, "date"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
            //***********************************************************************************************************************************************************
        }
        if (utf8_encode($estado) == 'Derivación Aceptada' or utf8_encode($estado) == 'Solicita autorización') {
            $estadoDerivacion = 'aceptada';
            $comentarioBitacora = 'Derivacion número '.$nderivacion.' de paciente '.utf8_encode($nombre).' rut '.$rutPac.' migrada desde Right Now con folio '.$folio.' en estado *'.utf8_encode($estado).'* con un monto de '.number_format($mTotal);
            $asunto= 'Migrada';

            // Inserto comentario en bitacora ********************************************************************************
            $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, ID_CANASTA_PATOLOGIA, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "text"), 
                GetSQLValueString($ultimaCanasta, "text"),
                GetSQLValueString($sesion, "text"),
                GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                GetSQLValueString($asunto, "text"),
                GetSQLValueString($fecha, "date"),
                GetSQLValueString($hora, "date"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
            //********************************************************************************************************************
        }
        if (utf8_encode($estado) == 'Alta Paciente' or utf8_encode($estado) == 'Autorizado para pago' or utf8_encode($estado) == 'Validado para Pago') {
            $estadoDerivacion = 'cerrada';
            $comentarioBitacora = 'Derivacion número '.$nderivacion.' de paciente '.utf8_encode($nombre).' rut '.$rutPac.' migrada desde Right Now con folio '.$folio.' en estado *'.utf8_encode($estado).'* con un monto de '.number_format($mTotal);
            $asunto= 'Migrada';

            // Inserto comentario en bitacora ********************************************************************************
            $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, ID_CANASTA_PATOLOGIA, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "text"), 
                GetSQLValueString($ultimaCanasta, "text"),
                GetSQLValueString($sesion, "text"),
                GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                GetSQLValueString($asunto, "text"),
                GetSQLValueString($fecha, "date"),
                GetSQLValueString($hora, "date"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
            //********************************************************************************************************************
        }
    //**************************************************************************************************************************************************************************



} 
/*#################################CASO CONTRARIO ACTUALIZO EL O LOS REGISTROS YA EXISTENTES##################################################################*/
else{

        // Modifica campos de la derivacion para actualizar los campos en caso de haber un cambio desde rn
        $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET ESTADO_RN=%s, MONTO_ACUMULADO_RN=%s, AUDITORIA=%s WHERE FOLIO= '$folio'",
            GetSQLValueString($estado, "text"),
            GetSQLValueString($mTotal, "int"),
            GetSQLValueString($fecha, "date"));
        $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

        // consulta para obtener el id_derivacion y el estado del folio en sistema
        $query_qrEstadosRn = "SELECT * FROM $MM_oirs_DATABASE.rn WHERE FOLIO = '$folio'"; 
        $qrEstadosRn = $oirs->SelectLimit($query_qrEstadosRn) or die($oirs->ErrorMsg());
        $totalRows_qrEstadosRn = $qrEstadosRn->RecordCount();

        $estadoRn = $qrEstadosRn->Fields('ESTADO');
        $montoRn = $qrEstadosRn->Fields('MONTO_TOTAL');
        $idDerivacion = $qrEstadosRn->Fields('ID_DERIVACION');
        $nderivacion = 'R0'.$idDerivacion;
        
        // if estado de la planilla es diferente al ultimo estado del folio se inserta registro en bitacora sobre el cambio de estado**************************************************
        if ($estadoRn != $estado) {
            $comentarioBitacora = 'La Derivacion número '.$nderivacion.' Folio Right Now '.$folio.' cambia de estado *'.utf8_encode($estadoRn). '* a estado *'.utf8_encode($estado).'*';
            $asunto= 'Cambio estado RN';

            $ultimaCanasta = '0';
            
            // Inserto comentario en bitacora ********************************************************************************
            $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, ID_CANASTA_PATOLOGIA, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "text"), 
                GetSQLValueString($ultimaCanasta, "text"),
                GetSQLValueString('1', "text"),
                GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                GetSQLValueString($asunto, "text"),
                GetSQLValueString($fecha, "date"),
                GetSQLValueString($hora, "date"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
            //********************************************************************************************************************
        }

        // if estado de la planilla es diferente al ultimo estado del folio se inserta registro en bitacora sobre el cambio de estado**************************************************
        if ($montoRn != $mTotal) {
            $comentarioBitacora = 'La Derivacion número '.$nderivacion.' Folio Right Now '.$folio.' cambia de monto *'.utf8_encode($montoRn). '* a monto *'.utf8_encode($mTotal).'*';
            $asunto= 'Cambio monto RN';

            $ultimaCanasta = '0';
            
            // Inserto comentario en bitacora ********************************************************************************
            $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, ID_CANASTA_PATOLOGIA, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idDerivacion, "text"), 
                GetSQLValueString($ultimaCanasta, "text"),
                GetSQLValueString('1', "text"),
                GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
                GetSQLValueString($asunto, "text"),
                GetSQLValueString($fecha, "date"),
                GetSQLValueString($hora, "date"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
            //********************************************************************************************************************
        }

        // Modifica datos de tabla rn    
        $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.rn SET COD_RUTPAC=%s,NOM_PACIENTE=%s,PROBLEMA_SALUD=%s,INTERVENCION_SANITARIA=%s,ESTADO=%s,MONTO_TOTAL=%s,MENSAJE=%s,FECHA_REGISTRO=%s WHERE FOLIO= '$folio'",
                    GetSQLValueString($rutPac, "text"),
                    GetSQLValueString($nombre, "text"),
                    GetSQLValueString($pSalud, "text"),
                    GetSQLValueString($iSanitaria, "text"),
                    GetSQLValueString($estado, "text"),
                    GetSQLValueString($mTotal, "int"),
                    GetSQLValueString($mensaje, "text"),
                    GetSQLValueString($fecha, "date"));
        $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());


        // Capturo id RN creado para relacionarlo con la tabla de estados
            $query_qrIdRn = ("SELECT * FROM $MM_oirs_DATABASE.rn WHERE FOLIO = '$folio'");
            $qrIdRn = $oirs->SelectLimit($query_qrIdRn) or die($oirs->ErrorMsg());
            $totalRows_qrIdRn = $qrIdRn->RecordCount();

            $idRn = $qrIdRn->Fields('ID_RN');

        // inserta el estado que viene en la planilla para tener historial de estados rn cada vez que se sube la planilla
            $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.rn_estados (ID_RN, FOLIO, ESTADO, MONTO_ACUMULADO_RN, FECHA_REGISTRO, NUMERO_CARGA, HORA_CARGA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($idRn, "int"), 
                GetSQLValueString($folio, "text"), 
                GetSQLValueString($estado, "text"),
                GetSQLValueString($mTotal, "int"),  
                GetSQLValueString($fecha, "date"),
                GetSQLValueString($nroCarga, "int"),
                GetSQLValueString($hora, "date"));
            $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

    } 
  }

 $i++;
}

//******************************BUSCA ANULADOS********************************************************************

$query_qrUltCarga = ("SELECT MAX(NUMERO_CARGA) as NUMERO_CARGA FROM rn_estados");
$qrUltCarga = $oirs->SelectLimit($query_qrUltCarga) or die($oirs->ErrorMsg());
$totalRows_qrUltCarga = $qrUltCarga->RecordCount();

$ultNumeroCarga = $qrUltCarga->Fields('NUMERO_CARGA');

$cargaAnterior = $ultNumeroCarga - 1;

$query_qrCargaAnterior = ("SELECT FOLIO FROM rn_estados WHERE NUMERO_CARGA = '$cargaAnterior'");
$qrCargaAnterior = $oirs->SelectLimit($query_qrCargaAnterior) or die($oirs->ErrorMsg());
$totalRows_qrCargaAnterior = $qrCargaAnterior->RecordCount();

 while (!$qrCargaAnterior->EOF) { 

  $folioAnterior = $qrCargaAnterior->Fields('FOLIO');

    $query_qrBuscaFolioAnulado = ("SELECT * FROM rn_estados WHERE FOLIO = '$folioAnterior' and NUMERO_CARGA = '$ultNumeroCarga'");
    $qrBuscaFolioAnulado = $oirs->SelectLimit($query_qrBuscaFolioAnulado) or die($oirs->ErrorMsg());
    $totalRows_qrBuscaFolioAnulado = $qrBuscaFolioAnulado->RecordCount();

     // echo $totalRows_qrBuscaFolioAnulado.'---' ;
    if ($totalRows_qrBuscaFolioAnulado == 0) {

        $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.folios_anulados (FOLIO, NUMERO_CARGA, FECHA_REGISTRO) VALUES (%s, %s, %s)",
            GetSQLValueString($folioAnterior, "int"),
            GetSQLValueString($ultNumeroCarga, "int"),
            GetSQLValueString($fecha, "date"));
        $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

        $query_qrBuscaIdDerivacion = ("SELECT * FROM derivaciones WHERE FOLIO = '$folioAnterior'");
        $qrBuscaIdDerivacion = $oirs->SelectLimit($query_qrBuscaIdDerivacion) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaIdDerivacion = $qrBuscaIdDerivacion->RecordCount();

        $idDerivacion = $qrBuscaIdDerivacion->Fields('ID_DERIVACION');

        $comentarioBitacora = 'Folio '.$folioAnterior.' anulado desde Right Now';
        $asunto= 'Folio anulado';

        $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
            GetSQLValueString($idDerivacion, "int"), 
            GetSQLValueString('99.999.999-9', "text"),
            GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
            GetSQLValueString($asunto, "text"),
            GetSQLValueString($fecha, "date"),
            GetSQLValueString($hora, "date"));
        $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

        $updateSQLAnulados = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET ESTADO_ANULACION=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString('anulado', "text"));
        $ResultAnulados = $oirs->Execute($updateSQLAnulados) or die($oirs->ErrorMsg());

        //***************************************************

        $gestora = $qrBuscaIdDerivacion->Fields('ENFERMERA');


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
            GetSQLValueString($fecha, "date"),
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
                GetSQLValueString($fecha, "date"),
                GetSQLValueString($hora, "date"),
                GetSQLValueString($estadoNoti, "text"),
                GetSQLValueString('99.999.999-9', "text"));
            $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
        $qrBuscaSupervisor->MoveNext(); }
        //**********************************************************************************************
        
    }


    $qrCargaAnterior->MoveNext(); 
}

//*****************************************************************************************************************************************

$cant_qrCasosRn =  $qrCasosRn->Fields('ncasos'); 

$cantidadCasosCsv = $cantidadCasos - 1; //para descontar la fila de titulos del excel
$casosNuevos = $cantidadCasosCsv - $cant_qrCasosRn;
$casosActualizados = $cantidadCasosCsv - $casosNuevos;

echo $respuesta = $cantidadCasosCsv.'!'.$casosNuevos.'!'.$casosActualizados.'!'.$cantidadCasos;
?>

