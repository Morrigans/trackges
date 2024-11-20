<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$id = $_POST['id'];
$insertSQL = sprintf("DELETE from $MM_oirs_DATABASE.etapa_patologia where ID_ETAPA_PATOLOGIA='$id'");
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close();