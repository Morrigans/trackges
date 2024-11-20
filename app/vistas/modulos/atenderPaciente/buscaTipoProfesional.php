<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$tipoEspecialidad=$_POST['tipoEspecialidad'];

$query_atencion = "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO='$tipoEspecialidad' ORDER BY NOMBRE ASC";
$atencion = $oirs->SelectLimit($query_atencion) or die($oirs->ErrorMsg());
$totalRows_atencion = $atencion->RecordCount();		
?>
<option value="0">Seleccione</option>
<?php
while(!$atencion->EOF){  ?> 	 
    <option value="<?php echo $atencion->Fields('ID')?>"><?php echo utf8_encode($atencion->Fields('NOMBRE'))?></option>
<?php
  $atencion->MoveNext();
  } 
$atencion->Close();
?>