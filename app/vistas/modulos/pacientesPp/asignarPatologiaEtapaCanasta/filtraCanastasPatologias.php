<?php
require_once '../../../../Connections/oirs.php';
require_once '../../../../includes/functions.inc.php';

$idEtapaPatologia=$_POST['etapaPatologia'];
$decreto=$_POST['decreto'];


$query_qrEtapaPatologia = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE ID_ETAPA_PATOLOGIA='$idEtapaPatologia'";
$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

$codEtapaPatologia = $qrEtapaPatologia->Fields('CODIGO_ETAPA_PATOLOGIA');

$query_select = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_ETAPA_PATOLOGIA='$codEtapaPatologia' AND DECRETO = '$decreto' ORDER BY DESC_CANASTA_PATOLOGIA ASC";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();		

?>
 <option value="">Seleccione...</option> 
 <?php
while(!$select->EOF){  ?> 	 
    <option value="<?php echo $select->Fields('ID_CANASTA_PATOLOGIA')?>"><?php echo $select->Fields('DESC_CANASTA_PATOLOGIA')?></option>
<?php
  $select->MoveNext();
  } 
$select->Close();
?>