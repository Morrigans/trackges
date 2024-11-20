<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idPrestacion = $_POST['idPrestacion'];
$insertSQL = sprintf("DELETE from $MM_oirs_DATABASE.prestaciones where ID_PRESTACION='$idPrestacion'");
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$Result1->Close();

if($Result1){

	echo 1;
}else{
	echo 0;
}