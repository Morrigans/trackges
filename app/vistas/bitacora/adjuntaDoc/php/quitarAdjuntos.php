<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: index.php');
exit; }

 // $rutPostulante=$_REQUEST['rutPostulante'];

 $rutPostulante = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$fecha= date('Y-m-d');
 $estado ='registrado';
 $borraCurriculum='';
  $borraCertificado='';

$query_select = ("SELECT * FROM $MM_oirs_DATABASE.rrhh_postulaciones WHERE RUT_PROFESIONAL='$rutPostulante'");
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();

echo $curriculum=$select->Fields('CURRICULUM');
$certificado=$select->Fields('CERTIFICADO');


list($carpeta, $archivo) = explode("/", $curriculum);
// list($carpetaCert, $archivoCert) = explode("/", $certificado);        
echo $archivo;
unlink($archivo);
// unlink($archivoCert);


// $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.rrhh_postulaciones SET  CURRICULUM=%s,CERTIFICADO=%s, ESTADO=%s WHERE RUT_PROFESIONAL = '$rutPostulante'",
// 	GetSQLValueString($borraCurriculum, "text"),
//     GetSQLValueString($borraCertificado, "text"),  
//     GetSQLValueString($estado, "text"));

// $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg()); 

// echo 1;

    // $updateSQL1 = sprintf("UPDATE $MM_oirs_DATABASE.rrhh_postulaciones SET ESTADO='$estado' WHERE RUT_PROFESIONAL='$rutPostulante'");
    // $Result1 = $oirs->Execute($updateSQL1) or die($oirs->ErrorMsg());
            




?>

