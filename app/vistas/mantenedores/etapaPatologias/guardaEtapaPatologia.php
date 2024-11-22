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

$inpEtapaPatologia=$_REQUEST['inpEtapaPatologia']; 
$codigoPatologia=$_REQUEST['codigoPatologia'];


$query_numPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia where CODIGO_PATOLOGIA='$codigoPatologia'";
$numPatologia = $oirs->SelectLimit($query_numPatologia) or die($oirs->ErrorMsg());
$totalRows_numPatologia = $numPatologia->RecordCount();

$aumentaCodPatologia=($totalRows_numPatologia+1);


$nuevoCodEtapaPatologia=$codigoPatologia.'.'.$aumentaCodPatologia;

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');



$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.etapa_patologia(CODIGO_PATOLOGIA, DESC_ETAPA_PATOLOGIA, CODIGO_ETAPA_PATOLOGIA) VALUES (%s, %s, %s)",
    GetSQLValueString($codigoPatologia, "text"),
    GetSQLValueString($inpEtapaPatologia, "text"),
    GetSQLValueString($nuevoCodEtapaPatologia, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());




echo 1;

$Result1->Close(); 