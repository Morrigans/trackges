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
	$codPatologia = $select->Fields('CODIGO_PATOLOGIA');

	$query_qrPatologia = "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE CODIGO_PATOLOGIA='$codPatologia'";
	$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
	$totalRows_qrPatologia = $qrPatologia->RecordCount(); 

	$idPatologia = $qrPatologia->Fields('ID_PATOLOGIA');

	$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones SET ID_PATOLOGIA=%s WHERE ID_DERIVACION= '$idDerivacion'",
	        	GetSQLValueString($idPatologia, "int"));
	$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$select->MoveNext();
}

echo 'Actualizacion finalizada de tabla';

