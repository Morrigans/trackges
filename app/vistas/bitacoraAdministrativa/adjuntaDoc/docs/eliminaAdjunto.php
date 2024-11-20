<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
require_once '../../../../includes/functions.inc.php';


$idBitacora = $_REQUEST['idBitacora'];
$ruta =$_REQUEST['ruta'];

list($carpeta,$archivo) = explode("/", $ruta);

$borraRuta='';
 $archivo;

unlink($archivo);

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');
$hora= date('G:i');

$quitaRuta = sprintf("UPDATE $MM_oirs_DATABASE.bitacora_administrativa SET RUTA_DOCUMENTO=%s WHERE ID_BITACORA='$idBitacora'",
					GetSQLValueString($borraRuta, "text"));
$quita = $oirs->Execute($quitaRuta) or die($oirs->ErrorMsg());
$quita->Close(); 
echo 1;