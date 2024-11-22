<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$region = $_POST['region'];

$query_func = "SELECT * FROM $MM_oirs_DATABASE.provincias WHERE region_id = '$region'";
$func = $oirs->SelectLimit($query_func) or die($oirs->ErrorMsg());
$totalRows_func = $func->RecordCount(); ?>

<option value="">Seleccione...</option>
<?php

while (!$func->EOF) {
 
   echo '<option value="'.$func->Fields('provincia_id').'">'.utf8_encode($func->Fields('provincia_nombre')).'</option>';
 $func->MoveNext(); } 



?>