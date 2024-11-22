<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

$usuario = $_SESSION['dni'];
$idUsuario = $_SESSION['idUsuario'];

$query_verProfesion = "SELECT * FROM $MM_oirs_DATABASE.login where ID='$idUsuario'";
$verProfesion = $oirs->SelectLimit($query_verProfesion) or die($oirs->ErrorMsg());
$totalRows_verProfesion = $verProfesion->RecordCount();

$idClinica=$verProfesion->Fields('ID_PRESTADOR');

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

$rutProfesional = $_POST['rutProfesional'];
$nombreProfesional = $_POST['nombreProfesional'];
$tipoProfesional = $_POST['tipoProfesional'];
$fonoProfesional = $_POST['fonoProfesional'];
$mailProfesional = $_POST['mailProfesional'];
$slEspecialidad = $_POST['slEspecialidad'];
$slSubespecialidad = $_POST['slSubespecialidad'];

$password = 'trackGes';

$options = array("cost"=>4);
$hashPassword = password_hash($password,PASSWORD_BCRYPT,$options);

$nivel = 'profesional';

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.login (USUARIO, ID_PRESTADOR, NOMBRE,TIPO,ID_ESPECIALIDAD, ID_SUBESPECIALIDAD, FONO, MAIL, RUT_SESION, FECHA_AUDITORIA, PASS, NIVEL) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($rutProfesional, "text"),
    GetSQLValueString($idClinica, "int"),
    GetSQLValueString($nombreProfesional, "text"),
    GetSQLValueString($tipoProfesional, "text"),
    GetSQLValueString($slEspecialidad, "int"),
    GetSQLValueString($slSubespecialidad, "int"),
    GetSQLValueString($fonoProfesional, "text"),
    GetSQLValueString($mailProfesional, "text"),
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($auditoria, "text"),
    GetSQLValueString($hashPassword, "text"),
    GetSQLValueString($nivel, "text")); 
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;

$Result1->Close(); 