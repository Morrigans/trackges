<?php
//Connection statement
require_once '../../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

$idDerivacion = $_REQUEST['idDerivacion'];
$comentarioCambioEstadoCanasta = $_REQUEST['comentarioCambioEstadoCanasta'];
$fechaFinCanasta = $_REQUEST['fechaFinCanasta'];
$observacion = $_REQUEST['comentarioCambioEstadoCanasta'];
$motivoFinCanasta = $_REQUEST['slMotivoFinCanasta'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$nderivacion = $qrDerivacion->Fields('N_DERIVACION');

$idDerivacionCanasta = $_REQUEST['canasta'];
$rutPrestador = $_REQUEST['prestador']; 

$query_qrPrestadores = "SELECT * FROM $MM_oirs_DATABASE.prestador WHERE RUT_PRESTADOR = '$rutPrestador'";
$qrPrestadores = $oirs->SelectLimit($query_qrPrestadores) or die($oirs->ErrorMsg());
$totalRows_qrPrestadores = $qrPrestadores->RecordCount();

$estado = 'finalizada';

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_canastas_pp SET ESTADO=%s, OBSERVACION=%s, FECHA_FIN_CANASTA=%s, AUDITORIA=%s, MOTIVO_FIN_CANASTA=%s WHERE ID_CANASTA_PATOLOGIA = '$idDerivacionCanasta'",
            GetSQLValueString($estado, "text"),
            GetSQLValueString(utf8_decode($observacion), "text"),
            GetSQLValueString($fechaFinCanasta, "date"),
            GetSQLValueString($auditoria, "date"),
            GetSQLValueString($motivoFinCanasta, "int"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$idEtapaPatologia = $_REQUEST['idEtapaPatologia'];
$codEtapaPatologia = $_REQUEST['codEtapaPatologia'];

$query_select = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA='$codEtapaPatologia'";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount(); 

$descEtapaPatologia = utf8_encode($select->Fields('DESC_ETAPA_PATOLOGIA'));

$query_qrDescCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_CANASTA_PATOLOGIA='$idDerivacionCanasta'";
$qrDescCanastaPatologia = $oirs->SelectLimit($query_qrDescCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrDescCanastaPatologia = $qrDescCanastaPatologia->RecordCount(); 

$fechaInicioCanasta = $qrDescCanastaPatologia->Fields('FECHA_CANASTA');
$codCanastapatologia = $qrDescCanastaPatologia->Fields('CODIGO_CANASTA_PATOLOGIA');

$query_qrCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA='$codCanastapatologia'";
$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount(); 

$descCanastaPatologia = utf8_encode($qrCanastaPatologia->Fields('DESC_CANASTA_PATOLOGIA'));

$query_qrMotivo = "SELECT * FROM $MM_oirs_DATABASE.motivos_fin_canastas WHERE ID_MOTIVO='$motivoFinCanasta'";
$qrMotivo = $oirs->SelectLimit($query_qrMotivo) or die($oirs->ErrorMsg());
$totalRows_qrMotivo = $qrMotivo->RecordCount(); 

$nomMotivo = $qrMotivo->Fields(DESC_MOTIVO);

if ($nomMotivo == '') {
    $comentarioBitacora = 'Se finaliza canasta ['.$descCanastaPatologia.'] asignada a prestador ['.utf8_encode($qrPrestadores->Fields('DESC_PRESTADOR')).'] con fecha de inicio ['.date("d-m-Y", strtotime($fechaInicioCanasta)).'] y fecha fin ['.date("d-m-Y", strtotime($fechaFinCanasta)).'] perteneciente a la etapa ['.$descEtapaPatologia.'] de la derivación número '.$nderivacion.', comentario: '.$observacion;
}else{
    $comentarioBitacora = 'Se finaliza canasta ['.$descCanastaPatologia.'] asignada a prestador ['.utf8_encode($qrPrestadores->Fields('DESC_PRESTADOR')).'] con fecha de inicio ['.date("d-m-Y", strtotime($fechaInicioCanasta)).'] y fecha fin ['.date("d-m-Y", strtotime($fechaFinCanasta)).'] perteneciente a la etapa ['.$descEtapaPatologia.'] de la derivación número '.$nderivacion.', comentario: '.$observacion.', Motivo canasta vencida: '.$nomMotivo;
}


$asunto= 'Canasta finalizada';
$hora= date('G:i');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

//obtengo el ultimo id de bitacora del mensaje recien creado al crear nueva canasta seleccionando a crss como prestador
$query_qrUltimaBitacora = ("SELECT max(ID_BITACORA) as ID_BITACORA FROM $MM_oirs_DATABASE.bitacora_pp");
$qrUltimaBitacora = $oirs->SelectLimit($query_qrUltimaBitacora) or die($oirs->ErrorMsg());
$totalRows_qrUltimaBitacora = $qrUltimaBitacora->RecordCount();

$ultimaBitacora = $qrUltimaBitacora->Fields('ID_BITACORA');


//******FINALIZA CANASTA SI CORRESPONDE EN GESTOR DE RED DE ORIGEN******************************************************************************************

$origen = $qrDerivacion->Fields('PRESTADOR_ORIGEN');

if ($origen != '') {// si origen es vacio quiere decir que no es una derivacion enviada por algun gestor de red, sino que fue creada localmente por lo que no debe hacer esto que esta en el if

   $idDerivacionOrigen = $qrDerivacion->Fields('ID_DERIVACION_PP');//obtengo el id de la derivacion del lado del gestor de red quien me la derivo

   if ($origen == '49') { // si la derivacion de origen viene de icrs
      $nDerivacionOrigen = 'D0'.$idDerivacionOrigen;
      require_once '../../../../../Connections/icrs.php';//llamo la conexion de icrs para insertar la nueva canasta


      //busco el id de la canasta existente en icrs para cambiarle el estado en icrs
      $query_qrBuscaCanastaExistente = ("SELECT * FROM $MM_icrs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION = '$idDerivacionOrigen' and CODIGO_ETAPA_PATOLOGIA = '$codEtapaPatologia' and CODIGO_CANASTA_PATOLOGIA = '$codCanastapatologia' AND ESTADO = 'activa'");
      $qrBuscaCanastaExistente = $icrs->SelectLimit($query_qrBuscaCanastaExistente) or die($icrs->ErrorMsg());
      $totalRows_qrBuscaCanastaExistente = $qrBuscaCanastaExistente->RecordCount();

      //obtengo el id de la canasta existente para cambiar el estado en icrs
      $idCanastaPatologiaOrigen = $qrBuscaCanastaExistente->Fields('ID_CANASTA_PATOLOGIA');

      $estado = 'finalizada';
      //cambio el estado a finalizada de la canasta en icrs
      $updateSQL = sprintf("UPDATE $MM_icrs_DATABASE.derivaciones_canastas SET ESTADO=%s, OBSERVACION=%s, FECHA_FIN_CANASTA=%s, AUDITORIA=%s, MOTIVO_FIN_CANASTA=%s WHERE ID_CANASTA_PATOLOGIA = '$idCanastaPatologiaOrigen'",
            GetSQLValueString($estado, "text"),
            GetSQLValueString(utf8_decode($observacion), "text"),
            GetSQLValueString($fechaFinCanasta, "date"),
            GetSQLValueString($auditoria, "date"),
            GetSQLValueString($motivoFinCanasta, "int"));
        $Result1 = $icrs->Execute($updateSQL) or die($icrs->ErrorMsg());

      
     if ($nomMotivo == '') {
         $comentarioBitacora = 'Se finaliza canasta ['.$descCanastaPatologia.'] asignada a prestador ['.utf8_encode($qrPrestadores->Fields('DESC_PRESTADOR')).'] con fecha de inicio ['.date("d-m-Y", strtotime($fechaInicioCanasta)).'] y fecha fin ['.date("d-m-Y", strtotime($fechaFinCanasta)).'] perteneciente a la etapa ['.$descEtapaPatologia.'] de la derivación número D0'.$idDerivacionOrigen.', comentario: '.utf8_encode($observacion);
     }else{
         $comentarioBitacora = 'Se finaliza canasta ['.$descCanastaPatologia.'] asignada a prestador ['.utf8_encode($qrPrestadores->Fields('DESC_PRESTADOR')).'] con fecha de inicio ['.date("d-m-Y", strtotime($fechaInicioCanasta)).'] y fecha fin ['.date("d-m-Y", strtotime($fechaFinCanasta)).'] perteneciente a la etapa ['.$descEtapaPatologia.'] de la derivación número D0'.$idDerivacionOrigen.', comentario: '.utf8_encode($observacion).', Motivo canasta vencida: '.$nomMotivo;
     }


     $asunto= 'Canasta finalizada';
     $hora= date('G:i');

     $insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA, ID_BITACORA_REMOTO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
         GetSQLValueString($idDerivacionOrigen, "text"), 
         GetSQLValueString('CRSS', "text"),
         GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
         GetSQLValueString($asunto, "text"),
         GetSQLValueString($auditoria, "date"),
         GetSQLValueString($hora, "date"),
         GetSQLValueString($ultimaBitacora, "text"));
     $Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());

     $asuntoPp= 'Canasta finalizada (D0'.$idDerivacionOrigen.')';
     $estadoNoti='nuevo';

     $query_qrDerivacionIcrs = "SELECT * FROM $MM_icrs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacionOrigen'";
     $qrDerivacionIcrs = $icrs->SelectLimit($query_qrDerivacionIcrs) or die($icrs->ErrorMsg());
     $totalRows_qrDerivacionIcrs = $qrDerivacionIcrs->RecordCount();

     $gestoraPp = $qrDerivacionIcrs->Fields('ENFERMERA');

     $insertSQL2 = sprintf("INSERT INTO $MM_icrs_DATABASE.notificaciones ( USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO,ORIGEN) VALUES ( %s, %s, %s, %s, %s, %s, %s)",
       
         GetSQLValueString($gestoraPp, "text"),
         GetSQLValueString($asuntoPp, "text"),
         GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
         GetSQLValueString($auditoria, "date"),
         GetSQLValueString($hora, "date"),
         GetSQLValueString($estadoNoti, "text"),
         GetSQLValueString('CRSS', "text"));
     $Result2 = $icrs->Execute($insertSQL2) or die($icrs->ErrorMsg());
   }
}
//********************************************************************************************************************************************

echo 1;

