<?
require_once '../../../../Connections/oirs.php';
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];


date_default_timezone_set("America/Santiago");

$idDerivacion= $_REQUEST['idDerivacion'];

$query_qrBuscaDoc= "SELECT * FROM $MM_oirs_DATABASE.2_events WHERE ID_DERIVACION = '$idDerivacion' AND ESTADO_CITA = 'CITA'";
$qrBuscaDoc = $oirs->SelectLimit($query_qrBuscaDoc) or die($oirs->ErrorMsg());
$totalRows_qrBuscaDoc = $qrBuscaDoc->RecordCount();

$rutPro= $qrBuscaDoc->Fields('cod_rutpro');
$fechaCita= $qrBuscaDoc->Fields('start');
$rutAgenda= $qrBuscaDoc->Fields('RUT_SESION');
$tipoAt= $qrBuscaDoc->Fields('TIPO_ATENCION');
$idEvents= $qrBuscaDoc->Fields('id');

$query_buscaPro= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$rutPro'";
$buscaPro = $oirs->SelectLimit($query_buscaPro) or die($oirs->ErrorMsg());
$totalRows_buscaPro = $buscaPro->RecordCount();

$query_buscaAgen= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$rutAgenda'";
$buscaAgen = $oirs->SelectLimit($query_buscaAgen) or die($oirs->ErrorMsg());
$totalRows_buscaAgen = $buscaAgen->RecordCount();

$nomPro= $buscaPro->Fields("NOMBRE");
$nomAgen= $buscaAgen->Fields("NOMBRE");


$arr = array('nomPro'=>$nomPro, 'fechaCita'=>$fechaCita, 'nomAgen'=>$nomAgen, 'tipoAt'=>$tipoAt, 'idEvents'=>$idEvents);
echo json_encode($arr); 