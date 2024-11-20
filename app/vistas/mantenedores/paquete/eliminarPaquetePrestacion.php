<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idPaquetePrestacion = $_POST['idPaquetePrestacion'];
$insertSQL = sprintf("DELETE from $MM_oirs_DATABASE.paquetes_prestaciones where ID_PAQUETE_PRESTACION='$idPaquetePrestacion'");
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close();