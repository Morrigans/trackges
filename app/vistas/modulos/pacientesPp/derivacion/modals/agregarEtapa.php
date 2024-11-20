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

$idDerivacion = $_POST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$codEtapaPatologia = $_POST['slEtapaPatologiaDerivacion'];

$query_select = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA='$codEtapaPatologia'";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount(); 

$descEtapaPatologia = utf8_encode($select->Fields('DESC_ETAPA_PATOLOGIA'));

$nderivacion = $qrDerivacion->Fields('N_DERIVACION');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones_etapas_pp (ID_DERIVACION, N_DERIVACION, CODIGO_ETAPA_PATOLOGIA, SESION, AUDITORIA) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"), 
    GetSQLValueString($nderivacion, "text"), 
    GetSQLValueString($_POST['slEtapaPatologiaDerivacion'], "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($auditoria, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$comentarioBitacora = 'Se agrega Etapa ['.$descEtapaPatologia.'] a la derivación número '.$nderivacion;
$asunto= 'Etapa agregada';
$hora= date('G:i');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


//******INSERTA ETAPA SI CORRESPONDE EN GESTOR DE RED DE ORIGEN******************************************************************************************

$origen = $qrDerivacion->Fields('PRESTADOR_ORIGEN');

if ($origen != '') {// si origen es vacio quiere decir que no es una derivacion enviada por algun gestor de red, sino que fue creada localmente por lo que no debe hacer esto que esta en el if

   $idDerivacionOrigen = $qrDerivacion->Fields('ID_DERIVACION_PP');//obtengo el id de la derivacion del lado del gestor de red quien me la derivo

   if ($origen == '49') { // si la derivacion de origen viene de icrs
      $nDerivacionOrigen = 'D0'.$idDerivacionOrigen;
      require_once '../../../../../Connections/icrs.php';//llamo la conexion de icrs para insertar la nueva canasta

      $insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.derivaciones_etapas (ID_DERIVACION, N_DERIVACION, CODIGO_ETAPA_PATOLOGIA, SESION, AUDITORIA) VALUES (%s, %s, %s, %s, %s)",
          GetSQLValueString($idDerivacionOrigen, "int"), 
          GetSQLValueString($nDerivacionOrigen, "text"), 
          GetSQLValueString($_POST['slEtapaPatologiaDerivacion'], "text"), 
          GetSQLValueString($usuario, "text"),
          GetSQLValueString($auditoria, "date"));
      $Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());

      //comentario que se va a bitacora de icrs************
      $comentarioBitacoraIcrs = 'Se agrega Etapa ['.$descEtapaPatologia.'] a la derivación número '.$nDerivacionOrigen;
      $asunto= 'Etapa agregada'; 
      $hora= date('G:i');

      $insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
            GetSQLValueString($idDerivacionOrigen, "text"), 
            GetSQLValueString('CRSS', "text"),
            GetSQLValueString(utf8_decode($comentarioBitacoraIcrs), "text"),
            GetSQLValueString($asunto, "text"),
            GetSQLValueString($auditoria, "date"),
            GetSQLValueString($hora, "date"));
        $Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());

     }
  }
  //********************************************************************************************************************************************

echo 1;

