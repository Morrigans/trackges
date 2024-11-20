<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$rutPaciente = $_POST['rutPaciente'];

$query_pac = "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$rutPaciente'";
$pac = $oirs->SelectLimit($query_pac) or die($oirs->ErrorMsg());
$totalRows_pac = $pac->RecordCount();

if ($totalRows_pac > 0) {
    echo 1;
}else{
    echo 0;
}


