<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$rut = $_REQUEST['nomPac'];

$codRutPac = explode(".", $rut);
$rut0 = $codRutPac[0]; // porción1
$rut1 = $codRutPac[1]; // porción2
$rut2 = $codRutPac[2]; // porción2
$codRutPac = $rut0.$rut1.$rut2;

$query_select2 = ("SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'");
$select2 = $oirs->SelectLimit($query_select2) or die($oirs->ErrorMsg());
$totalRows_select2 = $select2->RecordCount();

$idComuna = $select2->Fields('COMUNA');
$idPaciente = $select2->Fields('ID');

$query_VerComuna = "SELECT * FROM $MM_oirs_DATABASE.comunas WHERE comuna_id = '$idComuna'";
$VerComuna = $oirs->SelectLimit($query_VerComuna) or die($oirs->ErrorMsg());
$totalRows_VerComuna = $VerComuna->RecordCount();

$comuna = utf8_encode($VerComuna->Fields('comuna_nombre'));

$respuesta=$select2->Fields('COD_RUTPAC').'!'.utf8_encode($select2->Fields('NOMBRE')).'!'.utf8_encode($select2->Fields('DIRECCION')).'!'.$comuna.'!'.$idComuna.'!'.$idPaciente;

echo $respuesta;

$select2->Close();
