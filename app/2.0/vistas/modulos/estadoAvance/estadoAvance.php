<?php
// Connection statement
require_once '../../../../Connections/oirs.php';

// Additional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado, se le redirige al inicio.
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../../../../index.php');
    exit;
}

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria = date('Y-m-d');

$idDerivacion = $_POST['idDerivacion'];
$slAgregarEstadoAvance = $_POST['slAgregarEstadoAvance'];
$comentarioEstadoAvance = $_POST['comentarioEstadoAvance'];
$slAgregarRechazo = $_POST['slAgregarRechazo'];
$slTipoContacto = $_POST['slTipoContacto']; // Nueva variable

// Inicializamos la variable para concatenar comentarios 
$comentarioEstadoFinal = '';

// Si hay un comentario, lo añadimos
if (!empty($comentarioEstadoAvance)) {
    $comentarioEstadoFinal .= ' comentario: ' . $comentarioEstadoAvance;
}

// Verificamos si hay un motivo de rechazo
if (strpos($slAgregarEstadoAvance, 'Rechazado') !== false && !empty($slAgregarRechazo)) {
    $comentarioEstadoFinal .= ' (motivo de rechazo: ' . $slAgregarRechazo . ')';
    
    // Si el motivo es "CONTACTO NO CORRESPONDE", añadimos el tipo de contacto
    if ($slAgregarRechazo === 'CONTACTO NO CORRESPONDE' && !empty($slTipoContacto)) {
        $comentarioEstadoFinal .= ', tipo de contacto: ' . $slTipoContacto;
    }
}

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$folio = $qrDerivacion->Fields('FOLIO');

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_derivaciones SET ESTADO_AVANCE=%s, COMENTARIO_AVANCE=%s, TIPO_CONTACTO_AVANCE=%s, MOTIVO_RECHAZO=%s WHERE ID_DERIVACION= '$idDerivacion'",
    GetSQLValueString($slAgregarEstadoAvance, "text"),
    GetSQLValueString($comentarioEstadoFinal, "text"),
    GetSQLValueString($slTipoContacto, "text"),
    GetSQLValueString($slAgregarRechazo, "text"));
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());

$asunto = 'Estado Avance';
$comentarioBitacora = 'Se actualiza estado de avance a ' . $slAgregarEstadoAvance;

// Verificamos si `comentarioEstadoAvance` tiene contenido y lo agregamos
if (!empty($comentarioEstadoAvance)) {
    $comentarioBitacora .= ', comentario: ' . $comentarioEstadoAvance;
}

// Verificamos si hay un motivo de rechazo seleccionado y que el estado incluya "Rechazado"
if (strpos($slAgregarEstadoAvance, 'Rechazado') !== false && !empty($slAgregarRechazo)) {
    $comentarioBitacora .= ', motivo de rechazo: ' . $slAgregarRechazo;

    // Si el motivo es "CONTACTO NO CORRESPONDE", añadimos el tipo de contacto
    if ($slAgregarRechazo === 'CONTACTO NO CORRESPONDE' && !empty($slTipoContacto)) {
        $comentarioBitacora .= ', tipo de contacto: ' . $slTipoContacto;
    }
}

$hora = date('G:i');
$idUsuario = $_SESSION['idUsuario'];

$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"),
    GetSQLValueString($folio, "text"),
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

// Inserto estado en la tabla de estados avances
$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_estados_avances (ID_DERIVACION, FOLIO, ESTADO, MOTIVO_RECHAZO, TIPO_CONTACTO, COMENTARIO, FECHA_REGISTRO, HORA_REGISTRO, SESION) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "int"),
    GetSQLValueString($folio, "text"),
    GetSQLValueString($slAgregarEstadoAvance, "text"),
    GetSQLValueString($slAgregarRechazo, "text"),
    GetSQLValueString($slTipoContacto, "text"), // Agregamos el tipo de contacto
    GetSQLValueString($comentarioEstadoAvance, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($usuario, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

echo 1;
