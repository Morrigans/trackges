<?php
//Connection statement
require_once '../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

$idDerivacion = $_REQUEST['idDerivacion'];


$idCanastapatologia = $_POST['slCanastaPatologiaDerivacion'];

$query_qrCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE ID_CANASTA_PATOLOGIA='$idCanastapatologia'";
$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount(); 

//obtengo dias limite de la canasta seleccionada para guardarlo en tabla de derivaciones_canastas
$diasLimite = $qrCanastaPatologia->Fields('TIEMPO_LIMITE');

$codCanastaPatologia = $qrCanastaPatologia->Fields('CODIGO_CANASTA_PATOLOGIA');

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idDerivacionesEtapa = $_REQUEST['idEtapaPatologia'];
$codEtapaPatologia = $_REQUEST['codEtapaPatologia'];
$fechaCanasta = $_REQUEST['fechaCanasta'];
// $fechaCanasta = date("Y-m-d", strtotime($fechaCanasta));

$query_select = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA='$codEtapaPatologia'";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount(); 

$idEtapaDerivacion = $select->Fields('ID_ETAPA_PATOLOGIA');

$descEtapaPatologia = utf8_encode($select->Fields('DESC_ETAPA_PATOLOGIA'));


$query_qrCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA='$codCanastaPatologia'";
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

//busco el id de login para guardar el id de quien es la sesion actual para guardarlo con la derivacion
$query_qrLoginId= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$usuario'";
$qrLoginId = $oirs->SelectLimit($query_qrLoginId) or die($oirs->ErrorMsg());
$totalRows_qrLoginId = $qrLoginId->RecordCount();

$idSesion = $qrLoginId->Fields('ID'); 


// inserta la primera canasta asociada a la etapa seleccionada para la derivacion
    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones_canastas (ID_DERIVACIONES_ETAPA, CODIGO_ETAPA_PATOLOGIA, ID_ETAPA_PATOLOGIA, ID_DERIVACION, N_DERIVACION, ID_CANASTA, RUT_PRESTADOR, CODIGO_CANASTA_PATOLOGIA, FECHA_CANASTA, DIAS_LIMITE, FECHA_LIMITE, SESION, INICIAL, AUDITORIA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacionesEtapa, "int"), 
    GetSQLValueString($codEtapaPatologia, "text"), 
    GetSQLValueString($idEtapaDerivacion, "int"), 
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($nderivacion, "text"), 
    GetSQLValueString($_POST['slCanastaPatologiaDerivacion'], "int"),
    GetSQLValueString($rutPrestador, "text"), 
    GetSQLValueString($codCanastaPatologia, "text"), 
    GetSQLValueString($fechaCanasta, "date"), 
    GetSQLValueString($diasLimite, "date"),// tiempo limite de la canasta seleccionada lo obtengo arriba 
    GetSQLValueString($fechaLimite, "date"), // fecha limite la traigo calculada de archivo frmDerivacion
    GetSQLValueString($idSesion, "text"),
    GetSQLValueString("si", "text"),
    GetSQLValueString($auditoria, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());





$comentarioBitacora = 'Se agrega Canasta ['.$descCanastaPatologia.'] a Etapa '.$descEtapaPatologia.' de la derivación número '.$nderivacion;
$asunto= 'Canasta agregada';
$hora= date('G:i');

$query_qrUltimaCanasta = "SELECT MAX(ID_CANASTA_PATOLOGIA) AS ID_CANASTA_PATOLOGIA FROM $MM_oirs_DATABASE.derivaciones_canastas";
$qrUltimaCanasta = $oirs->SelectLimit($query_qrUltimaCanasta) or die($oirs->ErrorMsg());
$totalRows_qrUltimaCanasta = $qrUltimaCanasta->RecordCount(); 

$ultimaCanasta = $qrUltimaCanasta->Fields('ID_CANASTA_PATOLOGIA');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, ID_CANASTA_PATOLOGIA, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"),
    GetSQLValueString($ultimaCanasta, "text"),
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


echo 1;

