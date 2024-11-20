<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idCanasta = $_POST['id'];


$insertSQL = sprintf("DELETE from $MM_oirs_DATABASE.canasta_patologia where ID_CANASTA_PATOLOGIA='$idCanasta'");
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close();