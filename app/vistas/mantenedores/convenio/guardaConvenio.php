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

$inpConvenio=$_REQUEST['inpConvenio']; 



$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.convenio (DESC_CONVENIO) VALUES (%s)",
   
    GetSQLValueString(utf8_decode($inpConvenio), "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());




echo 1;

$Result1->Close(); 