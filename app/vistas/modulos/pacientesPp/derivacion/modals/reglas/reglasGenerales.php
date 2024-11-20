<?php
//Connection statement
require_once '../../../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

$canasta = $_REQUEST['canasta'];
$idDerivacion = $_REQUEST['idDerivacion'];

//obtiene la canasta de la que depende la que estoy seleccionando
$query_qrDepende = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$canasta'";
$qrDepende  = $oirs->SelectLimit($query_qrDepende ) or die($oirs->ErrorMsg());
$totalRows_qrDepende  = $qrDepende ->RecordCount();

//obtengo la dependencia
$dependencia = $qrDepende->Fields('DEPENDENCIA');

//obtiene el nombre de la canasta de la que depende la que estoy seleccionando
$query_qrNomDepende = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$dependencia'";
$qrNomDepende  = $oirs->SelectLimit($query_qrNomDepende ) or die($oirs->ErrorMsg());
$totalRows_qrNomDepende  = $qrNomDepende ->RecordCount();

$nomDependencia = utf8_encode($qrNomDepende->Fields('DESC_CANASTA_PATOLOGIA'));

//obtengo si se puede repetir
$repite = $qrDepende->Fields('REPITE');

//obtengo si puede retornar
$retorno = $qrDepende->Fields('RETORNO');

//obtengo si puede correr en simultaneo
$simultanea = $qrDepende->Fields('SIMULTANEA');

//evalua que la canasta de la que depende este finalizada.
$query_qrReglaDependencia = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_DERIVACION = '$idDerivacion' AND CODIGO_CANASTA_PATOLOGIA = '$dependencia' AND ESTADO = 'activa'";
$qrReglaDependencia  = $oirs->SelectLimit($query_qrReglaDependencia) or die($oirs->ErrorMsg());
$totalRows_qrReglaDependencia  = $qrReglaDependencia ->RecordCount();

if ($repite == 'no') {
	//evalua si la canasta seleccionada ya existe y si esta activa, solo podra repetirse estando finalizada la primera.
	$query_qrReglaRepite = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_DERIVACION = '$idDerivacion' AND CODIGO_CANASTA_PATOLOGIA = '$canasta'";
	$qrReglaRepite  = $oirs->SelectLimit($query_qrReglaRepite) or die($oirs->ErrorMsg());
	$totalRows_qrReglaRepite  = $qrReglaRepite ->RecordCount();

}else{
	//evalua si la canasta seleccionada ya existe y si esta activa, solo podra repetirse estando finalizada la primera.
	$query_qrReglaRepite = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_DERIVACION = '$idDerivacion' AND CODIGO_CANASTA_PATOLOGIA = '$canasta' AND ESTADO = 'activa'";
	$qrReglaRepite  = $oirs->SelectLimit($query_qrReglaRepite) or die($oirs->ErrorMsg());
	$totalRows_qrReglaRepite  = $qrReglaRepite ->RecordCount();
}

if ($simultanea == 'no') {
//evalua que la si hay canastas activas en la derivacion, para advertir que no puede habilitarse esta canasta ya que no correo simultanea
	$query_qrReglaSimultanea = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_DERIVACION = '$idDerivacion' AND ESTADO = 'activa'";
	$qrReglaSimultanea  = $oirs->SelectLimit($query_qrReglaSimultanea) or die($oirs->ErrorMsg());
	$totalRows_qrReglaSimultanea  = $qrReglaSimultanea ->RecordCount();
}else{
	$totalRows_qrReglaSimultanea == 0;//lo pongo en 0 para saltar la advertencia ya que puede correr simultanea
}


if ($totalRows_qrReglaDependencia > 0) {
    echo "dependencia|".$nomDependencia;
} 
elseif ($totalRows_qrReglaRepite > 0) {
    echo "repite|";
} 
elseif ($totalRows_qrReglaSimultanea > 0) {
    echo "simultanea|";
} 
else {
    //caso exitoso pasa las reglas
}




