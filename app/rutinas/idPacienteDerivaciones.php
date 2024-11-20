<?php
//Connection statement
require_once '../Connections/oirs.php';

//Aditional Functions
require_once '../includes/functions.inc.php';

$query_select = "SELECT * FROM $MM_oirs_DATABASE.derivaciones";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount(); 

while (!$select->EOF) {

	$idDerivacion = $select->Fields('ID_DERIVACION');
	$CodRutPac = $select->Fields('COD_RUTPAC');

	$query_qrPaciente = "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC='$CodRutPac'";
	$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
	$totalRows_qrPaciente = $qrPaciente->RecordCount(); 

	$idPaciente = $qrPaciente->Fields('ID');

	$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET ID_PACIENTE=%s WHERE ID_DERIVACION= '$idDerivacion'",
	        	GetSQLValueString($idPaciente, "int"));
	$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$select->MoveNext();
}

echo 'Actualizacion finalizada de tabla';

