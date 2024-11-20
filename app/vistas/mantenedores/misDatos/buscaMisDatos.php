<?php 
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

date_default_timezone_set("America/Santiago");
header('Content-Type: text/html; charset=utf-8');

$rutPro = $_REQUEST['usuario'];	

$query_qrPro= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO='$rutPro'";
$qrPro = $oirs->SelectLimit($query_qrPro) or die($oirs->ErrorMsg());
$totalRows_qrPro = $qrPro->RecordCount();

  $idPro= $qrPro->Fields('ID');
  $nombre= $qrPro->Fields('NOMBRE');
  $correo= $qrPro->Fields('MAIL');
  $fono= $qrPro->Fields('FONO');
  $direccion= $qrPro->Fields('DIRECCION');
  $comunas= $qrPro->Fields('COMUNA');

  $arr = array('idPro'=>$idPro, 'nombre'=>$nombre, 'correo'=>$correo, 'fono'=>$fono); 

  echo json_encode($arr); 
