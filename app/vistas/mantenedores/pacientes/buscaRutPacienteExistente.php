<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$rutPaciente = $_POST['rutPaciente'];

$codRutPac = explode(".", $rutPaciente);
$rut0 = $codRutPac[0]; // porción1
$rut1 = $codRutPac[1]; // porción2
$rut2 = $codRutPac[2]; // porción2
$codRutPac = $rut0.$rut1.$rut2;

$query_pac = "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'";
$pac = $oirs->SelectLimit($query_pac) or die($oirs->ErrorMsg());
$totalRows_pac = $pac->RecordCount();

if ($totalRows_pac > 0) {
    echo 1;
}else{
    echo 0;
}


