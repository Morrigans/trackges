<?php
//Connection statement
require_once '../../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../../includes/functions.inc.php';

$idAlarma = $_POST['idAlarma'];
$fechaRecordatorio = $_POST['fechaRecordatorio'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');
$hora= date('G:i');

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.alarmas_pp SET ESTADO=%s WHERE ID_ALARMA= '$idAlarma'",
            GetSQLValueString('desactivada', "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());



echo 1;

$Result1->Close(); 