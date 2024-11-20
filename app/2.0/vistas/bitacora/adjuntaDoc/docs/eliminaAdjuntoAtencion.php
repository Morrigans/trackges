<?php
//Connection statement
require_once '../../../../../Connections/oirs.php';
require_once '../../../../../includes/functions.inc.php';


//$idBitacora = $_REQUEST['idBitacora'];
$ruta =$_REQUEST['rutaAdjuntaDocAtencion'];

list($carpeta,$archivo) = explode("/", $ruta);

$borraRuta='';
 $archivo;

unlink($archivo);


echo 1;