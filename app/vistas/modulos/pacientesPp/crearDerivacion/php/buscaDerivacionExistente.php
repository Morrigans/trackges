<?php
require_once '../../../../../Connections/oirs.php';
require_once '../../../../../includes/functions.inc.php';

$codRutPac = $_REQUEST['codRutPac'];
$patologia = $_REQUEST['patologia'];

//busco si tiene derivaciones activas ese rut para advertir en caso que tenga y no ingresen repetidas
$query_qrBuscaDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE COD_RUTPAC = '$codRutPac' AND CODIGO_PATOLOGIA = '$patologia'";
$qrBuscaDerivacion = $oirs->SelectLimit($query_qrBuscaDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrBuscaDerivacion = $qrBuscaDerivacion->RecordCount();

$nDerivacion = $qrBuscaDerivacion->Fields('N_DERIVACION');
echo $nDerivacion;

