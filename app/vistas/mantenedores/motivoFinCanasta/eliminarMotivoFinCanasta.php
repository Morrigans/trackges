<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idMotivo = $_POST['id'];
$insertSQL = sprintf("DELETE from $MM_oirs_DATABASE.motivos_fin_canastas where ID_MOTIVO='$idMotivo'");
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close();