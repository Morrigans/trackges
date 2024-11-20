<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';



$idBitacora = $_REQUEST['idBitacora'];

$query_qrBitacora = "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE ID_BITACORA = '$idBitacora'";
$qrBitacora = $oirs->SelectLimit($query_qrBitacora) or die($oirs->ErrorMsg());
$totalRows_qrBitacora = $qrBitacora->RecordCount();

$linkAudio = $qrBitacora->Fields('RUTA_AUDIO');
?>

<audio id="audioBitacora" src="<?php echo '2.0/vistas/bitacora/modals'.$linkAudio ?>"
       autoplay>
</audio>


