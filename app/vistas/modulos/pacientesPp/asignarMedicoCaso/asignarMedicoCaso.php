<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
require_once '../../../../Connections/icrs.php';

//Aditional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

$idDerivacion = $_POST['idDerivacion'];
$idDerivacionPp = $_POST['idDerivacionPp'];
$slAsignarMedicoDerivacion = $_POST['slAsignarMedicoDerivacion'];
$canasta = $_POST['canasta'];

$estado = 'prestador';  

$query_qrDerivacionPp = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacionPp = $oirs->SelectLimit($query_qrDerivacionPp) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionPp = $qrDerivacionPp->RecordCount();

$gestora = $qrDerivacionPp->Fields('ENFERMERA');


$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_pp SET ESTADO=%s, RUT_PRESTADOR=%s WHERE ID_DERIVACION= '$idDerivacion'",
            GetSQLValueString($estado, "text"),
            GetSQLValueString($slAsignarMedicoDerivacion, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

if ($canasta > 0) {
    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.derivaciones_canastas_pp SET RUT_PRESTADOR=%s WHERE ID_DERIVACION = '$idDerivacion'",
            GetSQLValueString($slAsignarMedicoDerivacion, "text"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());
}




$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.prestador_asignado (ID_DERIVACION, RUT_PRESTADOR, SESION, AUDITORIA) VALUES (%s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"),
    GetSQLValueString($slAsignarMedicoDerivacion, "text"),
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($auditoria, "date"));
$Result2 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());





$comentarioBitacora = $_POST['comentarioBitacoraAsignarMedicoCaso'];

$nDerivacion = 'P0'.$idDerivacion;
$nDerivacionPp = 'D0'.$idDerivacionPp;

function reemplazarTexto($cadena, $nDerivacion, $nDerivacionPp) {
    $nuevaCadena = str_replace($nDerivacion, $nDerivacionPp, $cadena);
    return $nuevaCadena;
}

// Ejemplo de uso:
$nDerivacionOriginal = $nDerivacion;
$nDerivacionNueva = $nDerivacionPp;
$textoOriginal = $comentarioBitacora;
$comentarioBitacoraPp = reemplazarTexto($textoOriginal, $nDerivacionOriginal, $nDerivacionNueva);



$asunto= 'Prestador asignado';
$hora= date('G:i');

$idUsuario = $_SESSION['idUsuario'];

//inserto estado en tabla de estados**********************************************************************************************************************************
    $insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.estados_derivacion_pp (ID_DERIVACION, ESTADO, FECHA_REGISTRO, HORA_REGISTRO, SESION) VALUES (%s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacion, "int"), 
        GetSQLValueString($estado, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($idUsuario, "int"));
        $Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());
//********************************************************************************************************************************************************************

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora_pp (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());




//#############################################################  guarda daTos ICRS##########################################################
if ($idDerivacionPp != '') {
    $query_qrDerivacion = "SELECT * FROM $MM_icrs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacionPp'";
    $qrDerivacion = $icrs->SelectLimit($query_qrDerivacion) or die($icrs->ErrorMsg());
    $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

    $gestoraPp = $qrDerivacion->Fields('ENFERMERA');

    $query_qrUltimaBitacora = ("SELECT max(ID_BITACORA) as ID_BITACORA_PP FROM $MM_oirs_DATABASE.bitacora");
    $qrUltimaBitacora = $oirs->SelectLimit($query_qrUltimaBitacora) or die($oirs->ErrorMsg());
    $totalRows_qrUltimaBitacora = $qrUltimaBitacora->RecordCount();

    $asuntoPp= 'Prestador asignado  (D0'.$idDerivacionPp.')';
    $estadoNoti='nuevo';


    $ultimoIdBitacoraPp = $qrUltimaBitacora->Fields('ID_BITACORA');

    $insertSQL = sprintf("INSERT INTO $MM_icrs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA,ID_BITACORA_REMOTO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacionPp, "int"),     
        GetSQLValueString('CRSS', "text"),
        GetSQLValueString(utf8_decode($comentarioBitacoraPp), "text"),
        GetSQLValueString($asunto, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($ultimoIdBitacoraPp, "int"));
    $Result1 = $icrs->Execute($insertSQL) or die($icrs->ErrorMsg());



    $insertSQL2 = sprintf("INSERT INTO $MM_icrs_DATABASE.notificaciones ( USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO,ORIGEN) VALUES ( %s, %s, %s, %s, %s, %s, %s)",
      
        GetSQLValueString($gestoraPp, "text"),
        GetSQLValueString($asuntoPp, "text"),
        GetSQLValueString(utf8_decode($comentarioBitacoraPp), "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
        GetSQLValueString($estadoNoti, "text"),
        GetSQLValueString('CRSS', "text"));
    $Result2 = $icrs->Execute($insertSQL2) or die($icrs->ErrorMsg());
}

echo 1;

