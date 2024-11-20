<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$canasta = $_REQUEST['canasta'];

$query_select2 = ("SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE ID_CANASTA_PATOLOGIA = '$canasta'");
$select2 = $oirs->SelectLimit($query_select2) or die($oirs->ErrorMsg());
$totalRows_select2 = $select2->RecordCount();

echo $select2->Fields('TIEMPO_LIMITE');

$select2->Close();
