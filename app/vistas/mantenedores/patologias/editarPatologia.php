<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$idPatologiaEd = $_REQUEST['idPatologiaEd'];
$descripcionPatologiaEd = $_REQUEST['descripcionPatologiaEd'];
$vigenciaPatologiaEd = $_REQUEST['vigenciaPatologiaEd'];


$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.patologia SET DESC_PATOLOGIA='$descripcionPatologiaEd', DIAS_VIGENCIA='$vigenciaPatologiaEd' WHERE ID_PATOLOGIA='$idPatologiaEd'");
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close();