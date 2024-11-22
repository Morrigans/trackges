<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$tipoPatologia=$_POST['tipoPatologia'];

$query_select = "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE ID_TIPO_PATOLOGIA='$tipoPatologia' ORDER BY DESC_PATOLOGIA ASC";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();		

?>
 <option value="">Seleccione...</option>
 <?php
while(!$select->EOF){  ?> 	 
    <option value="<?php echo $select->Fields('CODIGO_PATOLOGIA')?>"><?php echo $select->Fields('DESC_PATOLOGIA')?></option>
<?php
  $select->MoveNext();
  } 
$select->Close();
?>