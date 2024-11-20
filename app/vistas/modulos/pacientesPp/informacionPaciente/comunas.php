<?php
//Connection statement
require_once '../../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../../includes/functions.inc.php';

$provincia = $_POST['provincia'];

$query_func1 = "SELECT * FROM $MM_oirs_DATABASE.comunas WHERE provincia_id = '$provincia'";
$func1 = $oirs->SelectLimit($query_func1) or die($oirs->ErrorMsg());
$totalRows_func1 = $func1->RecordCount(); ?>

<option value="">Seleccione...</option>
<?php while (!$func1->EOF) {
  
   echo '<option value="'.$func1->Fields('comuna_id').'">'.utf8_encode($func1->Fields('comuna_nombre')).'</option>';
 $func1->MoveNext(); } 



?>