<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$idPatologia = $_REQUEST['id'];

$query_buscaPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE ID_PATOLOGIA='$idPatologia'";
$buscaPatologia = $oirs->SelectLimit($query_buscaPatologia) or die($oirs->ErrorMsg());
$totalRows_buscaPatologia = $buscaPatologia->RecordCount();

  $descPatologia= $buscaPatologia->Fields('DESC_PATOLOGIA');
  $codPatologia= $buscaPatologia->Fields('CODIGO_PATOLOGIA');
  $idTipoPatologia= $buscaPatologia->Fields('ID_TIPO_PATOLOGIA');
  $diasVigencia= $buscaPatologia->Fields('DIAS_VIGENCIA');

  $arr = array('descPatologia'=>$descPatologia, 'codPatologia'=>$codPatologia, 'idTipoPatologia'=>$idTipoPatologia, 'diasVigencia'=>$diasVigencia); 

  echo json_encode($arr); 
