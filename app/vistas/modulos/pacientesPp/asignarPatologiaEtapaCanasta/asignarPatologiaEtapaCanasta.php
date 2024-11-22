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
$idSesion = $_SESSION['idUsuario'];


date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

$query_qrTipoUsuario = "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$usuario'";
$qrTipoUsuario = $oirs->SelectLimit($query_qrTipoUsuario) or die($oirs->ErrorMsg());
$totalRows_qrTipoUsuario = $qrTipoUsuario->RecordCount();

$idClinica = $qrTipoUsuario->Fields('ID_PRESTADOR');

$idDerivacion = $_POST['idDerivacion'];
$idPatologia = $_POST['idPatologia'];
$idEtapaPatologia = $_POST['idEtapa'];
$idCanastapatologia = $_POST['idCanasta'];

$fechaLimite = $_POST['fechaFinGarantia'];
if($fechaLimite == 'Sin Limite' or $fechaLimite == ''){
    $fechaLimite = '0000-00-00'; 
}else{
  $fechaLimite = date("Y-m-d", strtotime($fechaLimite));  
}

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_pp SET ID_PATOLOGIA=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($idPatologia, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$query_qrEtapaPatologia = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia_pp WHERE ID_ETAPA_PATOLOGIA='$idEtapaPatologia'";
$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

$codEtapaPatologia = $qrEtapaPatologia->Fields('CODIGO_ETAPA_PATOLOGIA');
$nderivacion = 'D0'.$idDerivacion;

// inserta la primera etapa seleccionada para la derivacion
    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones_etapas_pp (ID_DERIVACION, N_DERIVACION, CODIGO_ETAPA_PATOLOGIA, SESION, AUDITORIA) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"), 
    GetSQLValueString($nderivacion, "text"), 
    GetSQLValueString($codEtapaPatologia, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($auditoria, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$query_select2 = ("SELECT max(ID_ETAPA_PATOLOGIA) as ID_ETAPA_PATOLOGIA FROM $MM_oirs_DATABASE.derivaciones_etapas_pp");
$select2 = $oirs->SelectLimit($query_select2) or die($oirs->ErrorMsg());
$totalRows_select2 = $select2->RecordCount();

$idDerivacionesEtapa = $select2->Fields('ID_ETAPA_PATOLOGIA');


$query_qrCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE ID_CANASTA_PATOLOGIA='$idCanastapatologia'";
$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount(); 

$descCanastaPatologia = $qrCanastaPatologia->Fields('DESC_CANASTA_PATOLOGIA');


//obtengo dias limite de la canasta seleccionada para guardarlo en tabla de derivaciones_canastas
$diasLimite = $qrCanastaPatologia->Fields('TIEMPO_LIMITE');

$codCanastaPatologia = $qrCanastaPatologia->Fields('CODIGO_CANASTA_PATOLOGIA');

$query_qrEtapaCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE ID_ETAPA_PATOLOGIA='$idEtapaPatologia'";
$qrEtapaCanastaPatologia = $oirs->SelectLimit($query_qrEtapaCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrEtapaCanastaPatologia = $qrEtapaCanastaPatologia->RecordCount(); 

$descEtapaPatologia = $qrEtapaCanastaPatologia->Fields('DESC_ETAPA_PATOLOGIA');


// inserta la primera canasta asociada a la etapa seleccionada para la derivacion
    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones_canastas_pp (ID_DERIVACIONES_ETAPA, CODIGO_ETAPA_PATOLOGIA, ID_ETAPA_PATOLOGIA, ID_DERIVACION, N_DERIVACION, ID_CANASTA, CODIGO_CANASTA_PATOLOGIA, FECHA_CANASTA, DIAS_LIMITE, FECHA_LIMITE, SESION, INICIAL, AUDITORIA, RUT_PRESTADOR) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacionesEtapa, "int"), 
    GetSQLValueString($codEtapaPatologia, "text"), 
    GetSQLValueString($idEtapaPatologia, "int"), 
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($nderivacion, "text"), 
    GetSQLValueString($idCanastapatologia, "int"), 
    GetSQLValueString($codCanastaPatologia, "text"), 
    GetSQLValueString($_POST['fechaActivacion'], "date"), 
    GetSQLValueString($diasLimite, "date"),// tiempo limite de la canasta seleccionada lo obtengo arriba 
    GetSQLValueString($fechaLimite, "date"), // fecha limite la traigo calculada de archivo frmDerivacion
    GetSQLValueString($idSesion, "text"),
    GetSQLValueString("si", "text"),
    GetSQLValueString($auditoria, "text"),
    GetSQLValueString($idClinica, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


$query_qrPatologia = "SELECT * FROM $MM_oirs_DATABASE.patologia_pp WHERE ID_PATOLOGIA='$idPatologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$descPatologia = $qrPatologia->Fields('DESC_PATOLOGIA');

$comentarioBitacora = 'Se agrega Canasta ['.$descCanastaPatologia.'] a Etapa '.$descEtapaPatologia.' a patología '.$descPatologia.' de la derivación número '.$nderivacion;
$asunto= 'Canasta agregada';
$hora= date('G:i');

$query_qrUltimaCanasta = "SELECT MAX(ID_CANASTA_PATOLOGIA) AS ID_CANASTA_PATOLOGIA FROM $MM_oirs_DATABASE.derivaciones_canastas_pp";
$qrUltimaCanasta = $oirs->SelectLimit($query_qrUltimaCanasta) or die($oirs->ErrorMsg());
$totalRows_qrUltimaCanasta = $qrUltimaCanasta->RecordCount(); 

$ultimaCanasta = $qrUltimaCanasta->Fields('ID_CANASTA_PATOLOGIA');

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION, ID_CANASTA_PATOLOGIA, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"),
    GetSQLValueString($ultimaCanasta, "text"),
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());


echo 1;

