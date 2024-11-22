<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

// session_start();
//  Si el usuario no se ha logueado se le regresa al inicio.
// if (!isset($_SESSION['loggedin'])) {
// header('Location: ../../../index.php');
// exit; }



date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

 $rutPac = $_POST['rutPac'];
 $nombrePac = $_POST['nombrePac'];
 $telefonoPac = $_POST['telefonoPac'];
 $correoPac = $_POST['correoPac'];
 $direccionPac = $_POST['direccionPac'];
 $region = $_POST['region'];
 $provincia = $_POST['provincia'];
 $comuna = $_POST['comuna'];
 $prevision = $_POST['prevision'];
 $nacPac = $_POST['nacPac'];
 $nacPac = date("Y-m-d", strtotime($nacPac));



$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.pacientes SET NOMBRE=%s, FEC_NACIMI=%s, FONO=%s, MAIL=%s, DIRECCION=%s, REGION=%s, PROVINCIA=%s, COMUNA=%s, PREVISION=%s WHERE COD_RUTPAC= '$rutPac'",
            GetSQLValueString($nombrePac, "text"),
            GetSQLValueString($nacPac, "date"),
            GetSQLValueString($telefonoPac, "text"),
            GetSQLValueString($correoPac, "text"),
            GetSQLValueString($direccionPac, "text"),
      		GetSQLValueString($region, "int"),
			GetSQLValueString($provincia, "int"),
			GetSQLValueString($comuna, "int"),
			GetSQLValueString($prevision, "int"));	
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());



echo 1;
