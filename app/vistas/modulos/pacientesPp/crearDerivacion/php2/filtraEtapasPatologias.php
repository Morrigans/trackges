<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$idPatologia=$_POST['patologia'];
$decreto=$_POST['decreto'];

$query_qrPatologia = "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE ID_PATOLOGIA='$idPatologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$codPatologia = $qrPatologia->Fields('CODIGO_PATOLOGIA');

$query_select = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_PATOLOGIA='$codPatologia' AND DECRETO = '$decreto' ORDER BY DESC_ETAPA_PATOLOGIA ASC";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();   

?>
 <option value="">Seleccione...</option>
 <?php
while(!$select->EOF){  ?>    
    <option value="<?php echo $select->Fields('ID_ETAPA_PATOLOGIA')?>"><?php echo utf8_encode($select->Fields('DESC_ETAPA_PATOLOGIA'))?></option>
<?php
  $select->MoveNext();
  } 
$select->Close();
?>