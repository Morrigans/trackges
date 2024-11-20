<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

// Solo se permite el ingreso con el inicio de sesion.
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: index.php');
exit; }

$usuario = $_SESSION['dni'];

$id = $_POST['id'];
$estado = 'leido';
date_default_timezone_set('America/Santiago');
$fecha= date('Y-m-d');
$hora= date('G:i');

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.notificaciones_pp SET ESTADO=%s, FECHA_LEIDO=%s, HORA_LEIDO=%s, USUARIO_LECTOR=%s WHERE ID= '$id'",
            GetSQLValueString($estado, "text"),
            GetSQLValueString($fecha, "date"),
            GetSQLValueString($fecha, "date"),
            GetSQLValueString($usuario, "date"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg()); 