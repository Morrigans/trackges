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
$hora= date('G:i'); //CAPTURA HORA ACTUAL
$estado = 'pendiente';

$fechaLimite = $_POST['fechaFinGarantia'];
if($fechaLimite == 'Sin Limite' or $fechaLimite == ''){
    $fechaLimite = '0000-00-00'; 
}else{
  $fechaLimite = date("Y-m-d", strtotime($fechaLimite));  
}

$idPatologia = $_POST['slPatologiaDerivacion'];
$idPaciente = $_POST['idPaciente'];

//busco el id de login para guardar el id de quien es la sesion actual para guardarlo con la derivacion
$query_qrLoginId= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$usuario'";
$qrLoginId = $oirs->SelectLimit($query_qrLoginId) or die($oirs->ErrorMsg());
$totalRows_qrLoginId = $qrLoginId->RecordCount();

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$idPaciente'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$codRutPac = $qrPaciente->Fields('COD_RUTPAC');
$nPaciente = utf8_encode($qrPaciente->Fields('NOMBRE')); 
$idSesion = $qrLoginId->Fields('ID'); 
 
$idClinica = '19';

$decreto = 'LEP2225';

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones (FOLIO, ID_CONVENIO, CODIGO_TIPO_PATOLOGIA, FECHA_DERIVACION, ENFERMERA, ADMINISTRATIVA, SESION, AUDITORIA, ESTADO, ESTADO_RN, MONTO_ACUMULADO_RN, COMENTARIO, ID_PATOLOGIA, ID_PACIENTE, ID_CLINICA, DECRETO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
	GetSQLValueString($_POST['folio'], "text"),
    GetSQLValueString($_POST['slConvenioDerivacion'], "int"), 
    GetSQLValueString($_POST['slTipoPatologiaDerivacion'], "int"), 
    GetSQLValueString($_POST['fechaDerivacion'], "date"), 
    GetSQLValueString($_POST['slAsignarEnfermeriaDerivacion'], "int"), 
    GetSQLValueString($_POST['slAsignarAdministrativaDerivacion'], "int"), 
    GetSQLValueString($idSesion, "int"),
    GetSQLValueString($auditoria, "text"),
    GetSQLValueString($estado, "text"),
    GetSQLValueString(utf8_decode($_POST['estadoRn']), "text"),
    GetSQLValueString(utf8_decode($_POST['montoInicial']), "text"),
    GetSQLValueString($_POST['slAsignarAdministrativaDerivacion'], "int"),
    GetSQLValueString($idPatologia, "text"),
    GetSQLValueString($idPaciente, "int"),
    GetSQLValueString($idClinica, "int"),
	GetSQLValueString($decreto, "text"));
	$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$query_select = ("SELECT max(ID_DERIVACION) as ID_DERIVACION FROM $MM_oirs_DATABASE.derivaciones");
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();

$idDerivacion = $select->Fields('ID_DERIVACION');

//inserto estado en tabla de estados**********************************************************************************************************************************
	$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.estados_derivacion (ID_DERIVACION, ESTADO, FECHA_REGISTRO, HORA_REGISTRO, SESION) VALUES (%s, %s, %s, %s, %s)",
	    GetSQLValueString($idDerivacion, "int"), 
	    GetSQLValueString($estado, "text"),
	    GetSQLValueString($auditoria, "date"),
	    GetSQLValueString($hora, "date"),
	    GetSQLValueString($idSesion, "int"));
		$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
//********************************************************************************************************************************************************************

$nderivacion = 'R0'.$idDerivacion;

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET N_DERIVACION=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($nderivacion, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

if ($_POST['slTipoPatologiaDerivacion'] == 1) {

	$idEtapaPatologia = $_POST['slEtapaPatologiaDerivacion'];

	$query_qrEtapaPatologia = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE ID_ETAPA_PATOLOGIA='$idEtapaPatologia'";
	$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
	$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

	$codEtapaPatologia = $qrEtapaPatologia->Fields('CODIGO_ETAPA_PATOLOGIA');

	// inserta la primera etapa seleccionada para la derivacion
	    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones_etapas (ID_DERIVACION, N_DERIVACION, CODIGO_ETAPA_PATOLOGIA, SESION, AUDITORIA) VALUES (%s, %s, %s, %s, %s)",
	    GetSQLValueString($idDerivacion, "int"), 
	    GetSQLValueString($nderivacion, "text"), 
	    GetSQLValueString($codEtapaPatologia, "text"), 
	    GetSQLValueString($usuario, "text"),
	    GetSQLValueString($auditoria, "text"));
	$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

	$query_select2 = ("SELECT max(ID_ETAPA_PATOLOGIA) as ID_ETAPA_PATOLOGIA FROM $MM_oirs_DATABASE.derivaciones_etapas");
	$select2 = $oirs->SelectLimit($query_select2) or die($oirs->ErrorMsg());
	$totalRows_select2 = $select2->RecordCount();

	$idDerivacionesEtapa = $select2->Fields('ID_ETAPA_PATOLOGIA');

	$idCanastapatologia = $_POST['slCanastaPatologiaDerivacion'];

	$query_qrCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE ID_CANASTA_PATOLOGIA='$idCanastapatologia'";
	$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
	$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount(); 

	//obtengo dias limite de la canasta seleccionada para guardarlo en tabla de derivaciones_canastas
	$diasLimite = $qrCanastaPatologia->Fields('TIEMPO_LIMITE');

	$codCanastaPatologia = $qrCanastaPatologia->Fields('CODIGO_CANASTA_PATOLOGIA');

	$idEtapaCanastapatologia = $_POST['slEtapaPatologiaDerivacion'];

	$query_qrEtapaCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE ID_ETAPA_PATOLOGIA='$idEtapaCanastapatologia'";
	$qrEtapaCanastaPatologia = $oirs->SelectLimit($query_qrEtapaCanastaPatologia) or die($oirs->ErrorMsg());
	$totalRows_qrEtapaCanastaPatologia = $qrEtapaCanastaPatologia->RecordCount(); 

	$codEtapaCanastaPatologia = $qrEtapaCanastaPatologia->Fields('CODIGO_ETAPA_PATOLOGIA');

	// inserta la primera canasta asociada a la etapa seleccionada para la derivacion
	    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.derivaciones_canastas (ID_DERIVACIONES_ETAPA, CODIGO_ETAPA_PATOLOGIA, ID_ETAPA_PATOLOGIA, ID_DERIVACION, N_DERIVACION, ID_CANASTA, CODIGO_CANASTA_PATOLOGIA, FECHA_CANASTA, DIAS_LIMITE, FECHA_LIMITE, SESION, INICIAL, AUDITORIA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
	    GetSQLValueString($idDerivacionesEtapa, "int"), 
	    GetSQLValueString($codEtapaCanastaPatologia, "text"), 
	    GetSQLValueString($_POST['slEtapaPatologiaDerivacion'], "int"), 
	    GetSQLValueString($idDerivacion, "text"), 
	    GetSQLValueString($nderivacion, "text"), 
	    GetSQLValueString($_POST['slCanastaPatologiaDerivacion'], "int"), 
	    GetSQLValueString($codCanastaPatologia, "text"), 
	    GetSQLValueString($_POST['fechaActivacion'], "date"), 
	    GetSQLValueString($diasLimite, "date"),// tiempo limite de la canasta seleccionada lo obtengo arriba 
	    GetSQLValueString($fechaLimite, "date"), // fecha limite la traigo calculada de archivo frmDerivacion
	    GetSQLValueString($idSesion, "text"),
	    GetSQLValueString("si", "text"),
	    GetSQLValueString($auditoria, "text"));
	$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

}


//inserto monto en tabla montos **************************************************************************************
$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.montos (ID_DERIVACION, MONTO, TIPO_MONTO) VALUES (%s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"),
    GetSQLValueString($_POST['montoInicial'], "int"),
    GetSQLValueString('inicial', "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
//*********************************************************************************************************************



$profesional = $_POST['slAsignarEnfermeriaDerivacion'];

$query_qrProfesional= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$profesional'";
$qrProfesional = $oirs->SelectLimit($query_qrProfesional) or die($oirs->ErrorMsg());
$totalRows_qrProfesional = $qrProfesional->RecordCount();

$nomProfesional = $qrProfesional->Fields('NOMBRE'); 

$comentarioBitacora = 'Se crea Derivacion número '.$nderivacion.' para paciente '.$nPaciente.' rut '.$codRutPac.' asignada a profesional '.$nomProfesional.', Comentario: '.$_POST['comentarioDerivacion'];
$asunto= 'Creada';


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


//registro monto inicial en bitacora************************************************************************************************************************
$comentarioBitacora = 'Se asigna a Derivacion número '.$nderivacion.' un monto inicial de $'.number_format($_POST['montoInicial']);
$asunto= 'Monto inicial';

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, ID_CANASTA_PATOLOGIA, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($ultimaCanasta, "text"),
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
//***********************************************************************************************************************************************************


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