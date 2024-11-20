<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$inpPaquete=$_REQUEST['inpPaquete']; 
$idCanasta=$_REQUEST['idCanasta']; 

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.paquetes (DESC_PAQUETE, ID_CANASTA) VALUES (%s,%s)",
    GetSQLValueString(utf8_decode($inpPaquete), "text"),
    GetSQLValueString($idCanasta, "int"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 