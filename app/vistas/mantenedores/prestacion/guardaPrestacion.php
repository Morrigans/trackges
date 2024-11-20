<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

// session_start();
// // Si el usuario no se ha logueado se le regresa al inicio.
// if (!isset($_SESSION['loggedin'])) {
// header('Location: ../../../index.php');
// exit; }

// $usuario = $_SESSION['dni'];

$inpCodPrestacion=$_REQUEST['inpCodPrestacion'];
$inpPrestacion=$_REQUEST['inpPrestacion'];
$inpTiempoPrestacion=$_REQUEST['inpTiempoPrestacion'];



$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.prestaciones (CODIGO_PRESTACION, PRESTACION, TIEMPO_LIMITE) VALUES (%s,%s,%s)",
    GetSQLValueString($inpCodPrestacion, "text"),
    GetSQLValueString(utf8_decode($inpPrestacion), "text"),
    GetSQLValueString($inpTiempoPrestacion, "int"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$Result1->Close(); 

if($Result1){
    echo 1;
}else {
    echo 0;
}