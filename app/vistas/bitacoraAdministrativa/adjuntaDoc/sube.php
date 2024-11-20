<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$idBitacora = $_REQUEST['idBitacora'];

date_default_timezone_set('America/Santiago');
$fechaActual= date('Y-m-d');
$horaActual= date('G:i'); 

// if (($_FILES["file"]["type"] == "image/pjpeg")
//     || ($_FILES["file"]["type"] == "image/jpeg")
//     || ($_FILES["file"]["type"] == "application/pdf")
//     || ($_FILES["file"]["type"] == "image/png")
//     || ($_FILES["file"]["type"] == "image/gif")) {
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $ruta="docs/".$fechaActual.'_'.$idBitacora.'_'.$_FILES['file']['name'])) {


    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.bitacora_administrativa SET RUTA_DOCUMENTO='$ruta' WHERE ID_BITACORA='$idBitacora'");
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());


// $query_misPagos = "SELECT MAX(ID_PAGOS) as ID_PAGOS FROM $MM_oirs_DATABASE.rrhh_pagos ";
// $misPagos = $oirs->SelectLimit($query_misPagos) or die($oirs->ErrorMsg());
// $totalRows_misPagos = $misPagos->RecordCount();

// $idUltimoIngreso= $misPagos->Fields("ID_PAGOS");

$Result1->Close();
            
echo 1;


    } else {
        echo 'no';
    }
// }