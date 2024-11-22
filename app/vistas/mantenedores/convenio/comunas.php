<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$provincia = $_POST['provincia'];

$query_func = "SELECT * FROM $MM_oirs_DATABASE.comunas WHERE provincia_id = '$provincia'";
$func = $oirs->SelectLimit($query_func) or die($oirs->ErrorMsg());
$totalRows_func = $func->RecordCount(); ?>

<option value="">Seleccione...</option>
<?php while (!$func->EOF) {
  
   echo '<option value="'.$func->Fields('comuna_id').'">'.$func->Fields('comuna_nombre').'</option>';
 $func->MoveNext(); } 



?>