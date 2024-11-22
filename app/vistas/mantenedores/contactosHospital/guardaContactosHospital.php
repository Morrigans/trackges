<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';


  $idHospital = $_POST['idHospital'];
  $nombre = $_POST['nombre'];
  $email = $_POST['email'];
  $telefono = $_POST['telefono'];
  $unidadServicio = $_POST['unidadServicio'];
  $obsevacion = $_POST['obsevacion'];

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.hospitales_contactos (ID_HOSPITAL,NOMBRE,EMAIL,TELEFONO,CARGO_UNIDAD,OBSERVACION) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idHospital, "int"),
    GetSQLValueString($nombre, "text"),
    GetSQLValueString($email, "text"),
    GetSQLValueString($telefono, "text"),
    GetSQLValueString($unidadServicio, "text"),
    GetSQLValueString($obsevacion, "text")); 
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 