<?php
require_once '../../../../Connections/oirs.php';
require_once '../../../../includes/functions.inc.php';

$idBitacora = $_REQUEST['idBitacora'];
$idDerivacionCanasta = $_REQUEST['idDerivacionCanasta'];

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

date_default_timezone_set('America/Santiago');
$fechaActual= date('Y-m-d');
$horaActual= date('G:i'); 

$estado= 'adjunto1';
$rutProfesional = $_SESSION['dni'];

$query_select = ("SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE ID_BITACORA='$idBitacora'");
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();

$ruta=$select->Fields('RUTA_DOCUMENTO');

list($carpeta, $archivo) = explode("/", $ruta);

//unlink($archivo);
unlink('../../adjuntaDoc/docs/'.$archivo);

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.bitacora SET RUTA_DOCUMENTO=%s WHERE ID_BITACORA = '$idBitacora'",
            GetSQLValueString("", "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());
            
echo 1;