<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$especialidad=$_POST['especialidad'];


$query_select = "SELECT * FROM $MM_oirs_DATABASE.subespecialidades_medicas WHERE ID_ESPECIALIDAD='$especialidad'";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();		

?>
 <option value="">Seleccione...</option> 
 <?php
while(!$select->EOF){  ?> 	 
    <option value="<?php echo $select->Fields('ID_SUBESPECIALIDAD')?>"><?php echo utf8_encode($select->Fields('SUBESPECIALIDAD'))?></option>
<?php
  $select->MoveNext();
  } 
$select->Close();
?>