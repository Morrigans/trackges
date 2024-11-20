<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$etapaPatologia=$_POST['etapaPatologia'];

$query_select = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_ETAPA_PATOLOGIA='$etapaPatologia' ORDER BY DESC_CANASTA_PATOLOGIA ASC";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();		

?>
 <option value="">Seleccione...</option>
 <?php
while(!$select->EOF){  ?> 	 
    <option value="<?php echo $select->Fields('CODIGO_CANASTA_PATOLOGIA')?>"><?php echo utf8_encode($select->Fields('DESC_CANASTA_PATOLOGIA'))?></option>
<?php
  $select->MoveNext();
  } 
$select->Close();
?>