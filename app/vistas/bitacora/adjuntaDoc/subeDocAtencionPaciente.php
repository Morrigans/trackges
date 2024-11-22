<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$idDerivacion = $_REQUEST['idDerivacion'];

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: index.php');
exit; }
$estado= 'adjunto1';
$rutProfesional = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$fechaActual= date('Y-m-d');
$horaActual= date('G:i'); 

// if (($_FILES["file"]["type"] == "image/pjpeg")
//     || ($_FILES["file"]["type"] == "image/jpeg")
//     || ($_FILES["file"]["type"] == "application/pdf")
//     || ($_FILES["file"]["type"] == "image/png")
//     || ($_FILES["file"]["type"] == "image/gif")) {
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $ruta="docs/".$fechaActual.'_'.$idDerivacion.'_'.$_FILES['file']['name'])) {


		echo $ruta;

    } else {
        echo 'no';
    }
// }