<?php
//Connection statement
require_once '../Connections/oirs.php';

//Aditional Functions
require_once '../includes/functions.inc.php';

$query_select = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount(); 



while (!$select->EOF) {

	$codCanastapatologia = $select->Fields('CODIGO_CANASTA_PATOLOGIA');
	$idCanastapatologia = $select->Fields('ID_CANASTA_PATOLOGIA');
	$fechaCanasta = $select->Fields('FECHA_CANASTA');

	$query_qrCanastaPatologia = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA='$codCanastapatologia'";
	$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
	$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount(); 

	

	$diasLimite = $qrCanastaPatologia->Fields('TIEMPO_LIMITE');

	if ($diasLimite==null) {
	   $fechaLimite = '0000-00-00';
	}else{
	   //obtengo fecha limite de la canasta para guardarla en derivaciones_canastas
	   $fechaLimite = date("Y-m-d",strtotime($fechaCanasta."+ $diasLimite days"));
	}

	$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_canastas SET FECHA_LIMITE=%s, DIAS_LIMITE=%s WHERE ID_CANASTA_PATOLOGIA= '$idCanastapatologia'",
	            GetSQLValueString($fechaLimite, "date"),
	        	GetSQLValueString($diasLimite, "int"));
	$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$select->MoveNext();
}

echo 'Actualizacion finalizada de tabla canastas_derivaciones y sus campos FECHA_LIMITE Y TIEMPO_LIMITE';

