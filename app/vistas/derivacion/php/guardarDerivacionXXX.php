<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d'); //CAPTURA FECHA ACTUAL
$estado = 'pendiente';

$fechaLimite = $_POST['fechaFinGarantia'];
if($fechaLimite == 'Sin Limite' or $fechaLimite == ''){
    $fechaLimite = '0000-00-00'; 
}else{
  $fechaLimite = date("Y-m-d", strtotime($fechaLimite));  
}

$codPatologia = $_POST['slPatologiaDerivacion'];

$query_qrPatologia = "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE CODIGO_PATOLOGIA='$codPatologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount(); 

$idPatologia = $qrPatologia->Fields('ID_PATOLOGIA');


    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones (COD_RUTPAC, ID_CONVENIO, CODIGO_TIPO_PATOLOGIA, CODIGO_PATOLOGIA, CODIGO_ETAPA_PATOLOGIA, CODIGO_CANASTA_PATOLOGIA, FECHA_DERIVACION, ENFERMERA, SESION, AUDITORIA, ESTADO, COMENTARIO, ID_PATOLOGIA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['buscaPaciente'], "text"), 
    GetSQLValueString($_POST['slConvenioDerivacion'], "int"), 
    GetSQLValueString($_POST['slTipoPatologiaDerivacion'], "text"), 
    GetSQLValueString($_POST['slPatologiaDerivacion'], "text"), 
    GetSQLValueString($_POST['slEtapaPatologiaDerivacion'], "text"), 
    GetSQLValueString($_POST['slCanastaPatologiaDerivacion'], "text"), 
    GetSQLValueString($_POST['fechaDerivacion'], "date"), 
    GetSQLValueString($_POST['slAsignarEnfermeriaDerivacion'], "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($auditoria, "text"),
    GetSQLValueString($estado, "text"),
    GetSQLValueString(utf8_decode($_POST['comentarioDerivacion']), "text"),
    GetSQLValueString($idPatologia, "text"));
	$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$query_select = ("SELECT max(ID_DERIVACION) as ID_DERIVACION FROM $MM_oirs_DATABASE.derivaciones");
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();

$idDerivacion = $select->Fields('ID_DERIVACION');

$nderivacion = 'D0'.$idDerivacion;

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET N_DERIVACION=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($nderivacion, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

if ($_POST['slTipoPatologiaDerivacion'] == 1) {

	// inserta la primera etapa seleccionada para la derivacion
	    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones_etapas (ID_DERIVACION, N_DERIVACION, CODIGO_ETAPA_PATOLOGIA, SESION, AUDITORIA) VALUES (%s, %s, %s, %s, %s)",
	    GetSQLValueString($idDerivacion, "int"), 
	    GetSQLValueString($nderivacion, "text"), 
	    GetSQLValueString($_POST['slEtapaPatologiaDerivacion'], "text"), 
	    GetSQLValueString($usuario, "text"),
	    GetSQLValueString($auditoria, "text"));
	$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

	$query_select2 = ("SELECT max(ID_ETAPA_PATOLOGIA) as ID_ETAPA_PATOLOGIA FROM $MM_oirs_DATABASE.derivaciones_etapas");
	$select2 = $oirs->SelectLimit($query_select2) or die($oirs->ErrorMsg());
	$totalRows_select2 = $select2->RecordCount();

	$idEtapaPatologia = $select2->Fields('ID_ETAPA_PATOLOGIA');


	$codCanastapatologia = $_POST['slCanastaPatologiaDerivacion'];

	$query_qrCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA='$codCanastapatologia'";
	$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
	$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount(); 

	//obtengo dias limite de la canasta seleccionada para guardarlo en tabla de derivaciones_canastas
	$diasLimite = $qrCanastaPatologia->Fields('TIEMPO_LIMITE');

	// inserta la primera canasta asociada a la etapa seleccionada para la derivacion
	    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones_canastas (CODIGO_ETAPA_PATOLOGIA, ID_ETAPA_PATOLOGIA, ID_DERIVACION, N_DERIVACION, CODIGO_CANASTA_PATOLOGIA, FECHA_CANASTA, DIAS_LIMITE, FECHA_LIMITE, SESION, INICIAL, AUDITORIA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
	    GetSQLValueString($_POST['slEtapaPatologiaDerivacion'], "text"), 
	    GetSQLValueString($idEtapaPatologia, "int"), 
	    GetSQLValueString($idDerivacion, "text"), 
	    GetSQLValueString($nderivacion, "text"), 
	    GetSQLValueString($_POST['slCanastaPatologiaDerivacion'], "text"), 
	    GetSQLValueString($_POST['fechaActivacion'], "date"), 
	    GetSQLValueString($diasLimite, "date"),// tiempo limite de la canasta seleccionada lo obtengo arriba 
	    GetSQLValueString($fechaLimite, "date"), // fecha limite la traigo calculada de archivo frmDerivacion
	    GetSQLValueString($usuario, "text"),
	    GetSQLValueString("si", "text"),
	    GetSQLValueString($auditoria, "text"));
	$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

}

$codRutPac = $_POST['buscaPaciente'];

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$nPaciente = utf8_encode($qrPaciente->Fields('NOMBRE'));  

$profesional = $_POST['slAsignarEnfermeriaDerivacion'];

$query_qrProfesional= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$profesional'";
$qrProfesional = $oirs->SelectLimit($query_qrProfesional) or die($oirs->ErrorMsg());
$totalRows_qrProfesional = $qrProfesional->RecordCount();

$nomProfesional = $qrProfesional->Fields('NOMBRE'); 

$comentarioBitacora = 'Se crea Derivacion número '.$nderivacion.' para paciente '.$nPaciente.' rut '.$codRutPac.' asignada a profesional '.$nomProfesional.', Comentario: '.$_POST['comentarioDerivacion'];
$asunto= 'Creada';
$hora= date('G:i'); //CAPTURA HORA ACTUAL

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

$asunto = 'Derivacion Asignada';
$estadoNoti = 'nuevo';

$insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['slAsignarEnfermeriaDerivacion'], "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($estadoNoti, "text"));
$Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
echo 1;