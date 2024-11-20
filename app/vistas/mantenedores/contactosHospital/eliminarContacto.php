<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idHospitalContacto = $_POST['idHospitalContacto'];
$insertSQL = sprintf("DELETE from $MM_oirs_DATABASE.hospitales_contactos where ID_HOSPITAL_CONTACTO='$idHospitalContacto'");
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close();