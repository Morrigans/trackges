<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idAlarma = $_POST['idAlarma'];
$idBitacora = $_POST['idBitacora'];

$insertSQL = sprintf("DELETE from $MM_oirs_DATABASE.alarmas where ID_ALARMA='$idAlarma'");
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$query_qrAlarmas = ("SELECT * FROM $MM_oirs_DATABASE.alarmas where ID_BITACORA = '$idBitacora' AND ESTADO = 'activa'"); 
$qrAlarmas = $oirs->SelectLimit($query_qrAlarmas) or die($oirs->ErrorMsg());
$totalRows_qrAlarmas = $qrAlarmas->RecordCount();

if ($totalRows_qrAlarmas == 0) {
    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.bitacora SET PROGRAMADO=%s WHERE ID_BITACORA= '$idBitacora'",
            GetSQLValueString('', "text"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());
}



echo 1;

$Result1->Close(); 