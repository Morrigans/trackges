<?php
//Connection statement
require_once '../../../Connections/oirs.php'; 
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idBitacora = $_REQUEST['idBitacora'];
$origen = $_REQUEST['origen']; // recibo si viene de contacto paciente

if (count($_FILES) <= 0 || empty($_FILES["audio"])) { 
    exit("No hay archivos");
}

# De dónde viene el audio y en dónde lo ponemos
$rutaAudioSubido = $_FILES["audio"]["tmp_name"];
$nuevoNombre = uniqid() . ".webm";
$rutaDeGuardado = __DIR__ . "/audios/" . $nuevoNombre;
// Mover el archivo subido a la ruta de guardado
move_uploaded_file($_FILES["audio"]["tmp_name"], $rutaDeGuardado);
// Imprimir el nombre para que la petición lo lea
echo $nuevoNombre;

date_default_timezone_set('America/Santiago');
$fechaActual= date('Y-m-d');
$horaActual= date('G:i'); 

$ruta = "/audios/".$nuevoNombre;

if ($origen == 'contactoPaciente') {// no hace update
	
 }else{// si no viene de contacto paciente es una grabacion de bitacora, se asocia la ruta del audio al registro de bitacora.
	$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.bitacora SET RUTA_AUDIO='$ruta' WHERE ID_BITACORA='$idBitacora'");
	$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg()); 
}



