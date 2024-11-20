<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idBitacora = $_POST['idBitacora'];
$fechaRecordatorio = $_POST['fechaRecordatorio'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');
$hora= date('G:i');

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.bitacora_administrativa SET PROGRAMADO=%s, FECHA_PROGRAMACION=%s WHERE ID_BITACORA= '$idBitacora'",
            GetSQLValueString('', "text"),
            GetSQLValueString('', "date"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());



echo 1;

$Result1->Close(); 