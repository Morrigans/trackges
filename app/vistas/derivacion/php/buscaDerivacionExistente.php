<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$idPaciente = $_REQUEST['idPaciente'];
$patologia = $_REQUEST['patologia'];

//busco si tiene derivaciones activas ese rut para advertir en caso que tenga y no ingresen repetidas
$query_qrBuscaDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_PACIENTE = '$idPaciente' AND ID_PATOLOGIA = '$patologia'";
$qrBuscaDerivacion = $oirs->SelectLimit($query_qrBuscaDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrBuscaDerivacion = $qrBuscaDerivacion->RecordCount();

$nDerivacion = $qrBuscaDerivacion->Fields('N_DERIVACION');
echo $nDerivacion;

