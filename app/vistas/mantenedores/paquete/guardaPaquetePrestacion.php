<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$idPaquete=$_REQUEST['idPaquete']; 
$slPrestacion=$_REQUEST['slPrestaciones']; 

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.paquetes_prestaciones (ID_PAQUETE, ID_PRESTACION) VALUES (%s,%s)",
    GetSQLValueString($idPaquete, "int"),
    GetSQLValueString($slPrestacion, "int"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());



$Result1->Close(); 

if($Result1){
    echo 1;
}else{
    echo 0;
}