<?php
//Connection statement
require_once '../Connections/oirs.php';

//Aditional Functions
require_once '../includes/functions.inc.php';

$query_select = "SELECT * FROM $MM_oirs_DATABASE.folios_anulados";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount(); 



while (!$select->EOF) {

	$folio = $select->Fields('FOLIO');
	$fechaAnulacion = $select->Fields('FECHA_REGISTRO');

	$query_qrRnEstados = "SELECT * FROM $MM_oirs_DATABASE.rn_estados WHERE FOLIO='$folio' AND FECHA_REGISTRO >'$fechaAnulacion' order by ID_ESTADOS_RN ASC";
	$qrRnEstados = $oirs->SelectLimit($query_qrRnEstados) or die($oirs->ErrorMsg());
	$totalRows_qrRnEstados = $qrRnEstados->RecordCount(); 

	$fechaReactivacion = $qrRnEstados->Fields('FECHA_REGISTRO');

	$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE FOLIO = '$folio'";
	$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
	$totalRows_qrDerivacion = $qrDerivacion->RecordCount(); 

	$idDerivacion = $qrDerivacion->Fields('ID_DERIVACION');


	if ($totalRows_qrRnEstados > 0) {
	   
	   $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET ESTADO_ANULACION=%s WHERE FOLIO= '$folio'",
	               GetSQLValueString('activo', "text"));
	   $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

	   date_default_timezone_set('America/Santiago');
	   $auditoria= date('Y-m-d');

	   $comentarioBitacora = 'El folio '.$folio.' fue reactivado por rightnow el dia '.date("d-m-Y", strtotime($qrRnEstados->Fields('FECHA_REGISTRO'))).', el cual fue anulado el '.date("d-m-Y", strtotime($select->Fields('FECHA_REGISTRO'))). '</br>';
	   $asunto= 'Folio reactivado';
	   $hora= date('G:i');

	   $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
	       GetSQLValueString($idDerivacion, "text"), 
	       GetSQLValueString('99.999.999-9', "text"),
	       GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
	       GetSQLValueString($asunto, "text"),
	       GetSQLValueString($auditoria, "date"),
	       GetSQLValueString($hora, "date"));
	   $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

	   $deleteSQL = sprintf("DELETE from $MM_oirs_DATABASE.folios_anulados where FOLIO='$folio'");
	   $Result1 = $oirs->Execute($deleteSQL) or die($oirs->ErrorMsg());
	}
	
$select->MoveNext();
}


