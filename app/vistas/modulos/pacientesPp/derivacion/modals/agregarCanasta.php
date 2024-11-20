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

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idEtapaPatologia = $_REQUEST['idEtapaPatologia'];
$codEtapaPatologia = $_REQUEST['codEtapaPatologia'];
$fechaCanasta = $_REQUEST['fechaCanasta'];
// $fechaCanasta = date("Y-m-d", strtotime($fechaCanasta));

$query_select = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA='$codEtapaPatologia'";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount(); 

$descEtapaPatologia = utf8_encode($select->Fields('DESC_ETAPA_PATOLOGIA'));

$codCanastapatologia = $_POST['slCanastaPatologiaDerivacion'];

$query_qrCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA='$codCanastapatologia'";
$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount(); 

$descCanastaPatologia = utf8_encode($qrCanastaPatologia->Fields('DESC_CANASTA_PATOLOGIA'));

$diasLimite = $qrCanastaPatologia->Fields('TIEMPO_LIMITE');

if ($diasLimite==null or $diasLimite==0) {
   $fechaLimite = '0000-00-00';
}else{
   //obtengo fecha limite de la canasta para guardarla en derivaciones_canastas
   $fechaLimite = date("Y-m-d",strtotime($fechaCanasta."+ $diasLimite days"));
}

$nderivacion = $qrDerivacion->Fields('N_DERIVACION');

$rutPrestador = $_POST['slPrestadorCanasta']; 


$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones_canastas_pp (CODIGO_ETAPA_PATOLOGIA, ID_ETAPA_PATOLOGIA, ID_DERIVACION, N_DERIVACION, CODIGO_CANASTA_PATOLOGIA, RUT_PRESTADOR, FECHA_CANASTA, DIAS_LIMITE, FECHA_LIMITE, SESION, AUDITORIA) VALUES (%s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($codEtapaPatologia, "text"), 
    GetSQLValueString($idEtapaPatologia, "int"), 
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($nderivacion, "text"), 
    GetSQLValueString($_POST['slCanastaPatologiaDerivacion'], "text"),
    GetSQLValueString($rutPrestador, "text"),
    GetSQLValueString($fechaCanasta, "date"), 
    GetSQLValueString($diasLimite, "date"), 
    GetSQLValueString($fechaLimite, "date"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($auditoria, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$comentarioBitacora = 'Se agrega Canasta ['.$descCanastaPatologia.'] a Etapa '.$descEtapaPatologia.' de la derivación número '.$nderivacion;
$asunto= 'Canasta agregada';
$hora= date('G:i');

$query_qrUltimaCanasta = "SELECT MAX(ID_CANASTA_PATOLOGIA) AS ID_CANASTA_PATOLOGIA FROM $MM_oirs_DATABASE.derivaciones_canastas_pp";
$qrUltimaCanasta = $oirs->SelectLimit($query_qrUltimaCanasta) or die($oirs->ErrorMsg());
$totalRows_qrUltimaCanasta = $qrUltimaCanasta->RecordCount(); 

$ultimaCanasta = $qrUltimaCanasta->Fields('ID_CANASTA_PATOLOGIA');

// $query_select = ("SELECT max(ID_DERIVACION) as ID_DERIVACION FROM $MM_oirs_DATABASE.derivaciones");
// $select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
// $totalRows_select = $select->RecordCount();

// $idDerivacion = $select->Fields('ID_DERIVACION');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION, ID_CANASTA_PATOLOGIA, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"),
    GetSQLValueString($ultimaCanasta, "text"),
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


//******INSERTA CANASTA SI CORRESPONDE EN GESTOR DE RED DE ORIGEN******************************************************************************************

$origen = $qrDerivacion->Fields('PRESTADOR_ORIGEN');

if ($origen != '') {// si origen es vacio quiere decir que no es una derivacion enviada por algun gestor de red, sino que fue creada localmente por lo que no debe hacer esto que esta en el if

   $idDerivacionOrigen = $qrDerivacion->Fields('ID_DERIVACION_PP');//obtengo el id de la derivacion del lado del gestor de red quien me la derivo

   if ($origen == '49') { // si la derivacion de origen viene de icrs
      $nDerivacionOrigen = 'D0'.$idDerivacionOrigen;
      require_once '../../../../../Connections/icrs.php';//llamo la coexion de icrs para insertar la nueva canasta

      //busco el id de la etapa existente en icrs para asociarle la nueva canasta que esta agregando crss
      $query_qrBuscaEtapaExistente = ("SELECT * FROM $MM_icrs_DATABASE.derivaciones_etapas WHERE ID_DERIVACION = '$idDerivacionOrigen' and CODIGO_ETAPA_PATOLOGIA = '$codEtapaPatologia'");
      $qrBuscaEtapaExistente = $icrs->SelectLimit($query_qrBuscaEtapaExistente) or die($icrs->ErrorMsg());
      $totalRows_qrBuscaEtapaExistente = $qrBuscaEtapaExistente->RecordCount();

      //obtengo el id de la etapa existente para insertar en la tabla derivaciones_canastas de icrs y asi la canasta aparezca en el menu etapas/canastas de icrs
      $idEtapaPatologiaOrigen = $qrBuscaEtapaExistente->Fields('ID_ETAPA_PATOLOGIA');

      //inserto la canasta del lado del gesto de red para que vea que canasta estoy agregando a la derivacion que me enviaron
      $insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.derivaciones_canastas (CODIGO_ETAPA_PATOLOGIA, ID_ETAPA_PATOLOGIA, ID_DERIVACION, N_DERIVACION, CODIGO_CANASTA_PATOLOGIA, RUT_PRESTADOR, FECHA_CANASTA, DIAS_LIMITE, FECHA_LIMITE, SESION, AUDITORIA) VALUES (%s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
          GetSQLValueString($codEtapaPatologia, "text"), 
          GetSQLValueString($idEtapaPatologiaOrigen, "int"), 
          GetSQLValueString($idDerivacionOrigen, "int"), 
          GetSQLValueString($nDerivacionOrigen, "text"), 
          GetSQLValueString($_POST['slCanastaPatologiaDerivacion'], "text"),
          GetSQLValueString($rutPrestador, "text"),
          GetSQLValueString($fechaCanasta, "date"), 
          GetSQLValueString($diasLimite, "date"), 
          GetSQLValueString($fechaLimite, "date"), 
          GetSQLValueString($usuario, "text"),
          GetSQLValueString($auditoria, "date"));
      $Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());

      //obtengo el id de la canasta que acabo de insertar en icrs
      $query_qrUltimaCanastaIcrs = "SELECT MAX(ID_CANASTA_PATOLOGIA) AS ID_CANASTA_PATOLOGIA FROM $MM_icrs_DATABASE.derivaciones_canastas";
      $qrUltimaCanastaIcrs = $icrs->SelectLimit($query_qrUltimaCanastaIcrs) or die($icrs->ErrorMsg());
      $totalRows_qrUltimaCanastaIcrs = $qrUltimaCanastaIcrs->RecordCount(); 

      $ultimaCanastaIcrs = $qrUltimaCanastaIcrs->Fields('ID_CANASTA_PATOLOGIA');

      //comentario que se va a bitacora de icrs************
      $comentarioBitacoraIcrs = 'Se agrega Canasta ['.$descCanastaPatologia.'] a Etapa '.$descEtapaPatologia.' de la derivación número '.$nDerivacionOrigen;
      $asunto= 'Canasta agregada';
      $hora= date('G:i');

      $insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.bitacora (ID_DERIVACION, ID_CANASTA_PATOLOGIA, SESION, BITACORA, ASUNTO, AUDITORIA, HORA, ID_BITACORA_REMOTO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
          GetSQLValueString($idDerivacionOrigen, "text"),
          GetSQLValueString($ultimaCanastaIcrs, "text"),
          GetSQLValueString('CRSS', "text"),
          GetSQLValueString(utf8_decode($comentarioBitacoraIcrs), "text"),
          GetSQLValueString($asunto, "text"),
          GetSQLValueString($auditoria, "date"),
          GetSQLValueString($hora, "date"),
          GetSQLValueString($ultimaBitacora, "text")); 
      $Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());

      $asuntoPp= 'Canasta agregada (D0'.$idDerivacionOrigen.')';
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

