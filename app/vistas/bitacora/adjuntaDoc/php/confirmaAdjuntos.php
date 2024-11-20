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
 $estado ='adjuntofinalizado';


$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.rrhh_postulaciones SET  ESTADO=%s WHERE RUT_PROFESIONAL = '$rutPostulante'",
    
    GetSQLValueString($estado, "text"));

$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

echo 1;

    // $updateSQL1 = sprintf("UPDATE $MM_oirs_DATABASE.rrhh_postulaciones SET ESTADO='$estado' WHERE RUT_PROFESIONAL='$rutPostulante'");
    // $Result1 = $oirs->Execute($updateSQL1) or die($oirs->ErrorMsg());
            




?>

