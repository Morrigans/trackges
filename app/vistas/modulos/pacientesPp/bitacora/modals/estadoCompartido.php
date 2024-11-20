<?php
//Connection statement
require_once '../../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../../includes/functions.inc.php';

$idBitacora = $_POST['idBitacora'];

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.bitacora_pp SET COMPARTIDO=%s WHERE ID_BITACORA= '$idBitacora'",
            GetSQLValueString('si', "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 