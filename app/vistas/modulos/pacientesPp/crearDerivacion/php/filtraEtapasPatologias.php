<?php
require_once '../../../../../Connections/oirs.php';
require_once '../../../../../includes/functions.inc.php';

$patologia=$_POST['patologia'];
$decreto='LEP2225';

$query_select = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_PATOLOGIA='$patologia' AND DECRETO = '$decreto' ORDER BY DESC_ETAPA_PATOLOGIA ASC";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();		

?>
 <option value="">Seleccione...</option>
 <?php
while(!$select->EOF){  ?> 	 
    <option value="<?php echo $select->Fields('CODIGO_ETAPA_PATOLOGIA')?>"><?php echo utf8_encode($select->Fields('DESC_ETAPA_PATOLOGIA'))?></option>
<?php
  $select->MoveNext();
  } 
$select->Close();
?>