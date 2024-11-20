<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idBitacora = $_POST['idBitacora'];
$prestadorCompartido = $_POST['prestadorCompartido'];

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.bitacora_administrativa SET COMPARTIDO=%s, PRESTADOR_COMPARTIDO=%s WHERE ID_BITACORA= '$idBitacora'",
            GetSQLValueString('si', "text"),
            GetSQLValueString($prestadorCompartido, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 