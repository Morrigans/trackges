<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$patologia=$_POST['patologia'];

$query_select = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_PATOLOGIA='$patologia' ORDER BY DESC_ETAPA_PATOLOGIA ASC";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();		

?>
 <option value="">Seleccione...</option>
 <?php
while(!$select->EOF){  ?> 	 
    <option value="<?php echo $select->Fields('CODIGO_ETAPA_PATOLOGIA')?>"><?php echo $select->Fields('DESC_ETAPA_PATOLOGIA')?></option>
<?php
  $select->MoveNext();
  } 
$select->Close();
?>