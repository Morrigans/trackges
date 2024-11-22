<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';
require_once 'conecta.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];
$idUsuario = $_REQUEST['idUsuario'];
$misCasos = $_REQUEST['misCasos'];

$query_qrTipoUsuario = "SELECT * FROM $MM_oirs_DATABASE.login WHERE ID = '$idUsuario'";
$qrTipoUsuario = $oirs->SelectLimit($query_qrTipoUsuario) or die($oirs->ErrorMsg());
$totalRows_qrTipoUsuario = $qrTipoUsuario->RecordCount();

$tipo = $qrTipoUsuario->Fields('TIPO');
$idClinica = $qrTipoUsuario->Fields('ID_PRESTADOR');

$table = '';

    // DB table to use
    $table = <<<EOT
    (
        SELECT 
    DISTINCT a.ID_DERIVACION,
    a.FOLIO,
    a.N_DERIVACION,
    a.FECHA_DERIVACION,
    a.LATERALIDAD,
    a.COD_RUTPAC,
    REPLACE(a.COD_RUTPAC, '.', '') AS COD_RUTPAC_SIN_PUNTOS,
    a.PROCESO,
    a.CATEGORIA,
    a.INTERVENCION_SANITARIA,
    a.ESTADO_RN,
    a.PROBLEMA_SALUD,
    b.NOMBRE AS NOMBRE_PACIENTE,
    g.NOMBRE AS TENS,
    c.NOMBRE AS GESTORA,
    med.NOMBRE AS MEDICO,
    adm.NOMBRE AS ADMINISTRATIVA,
    a.ESTADO_AVANCE,
    a.MOTIVO_RECHAZO,
    a.COMENTARIO_AVANCE,
    a.TIPO_CONTACTO_AVANCE
    FROM 2_derivaciones a
    LEFT JOIN pacientes b ON a.COD_RUTPAC = b.COD_RUTPAC 
    LEFT JOIN login g ON a.TENS = g.USUARIO
    LEFT JOIN login c ON a.ENFERMERA = c.USUARIO
    LEFT JOIN login med ON a.MEDICO = med.USUARIO
    LEFT JOIN login adm ON a.ADMINISTRATIVA = adm.USUARIO
    


    ) temp
    EOT;


// Table's primary key
$primaryKey = 'ID_DERIVACION';

$columns = array(
    array('db' => 'ID_DERIVACION', 'dt' => 0, 'formatter' => function($d, $row) {
        return ' 
            <div class="btn-group">
                <button type="button" class="btn btn-default"><i class="fas fa-cog"></i></button>
                <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
                <div class="dropdown-menu" role="menu">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalEstadoAvance" onclick="fnfrmEstadoAvance('.$d.')">Estado de avance</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('.$d.')">Bitacora</a> 
                </div>
            </div>';
    }),
    array('db' => 'PROBLEMA_SALUD', 'dt' => 1, 'formatter' => function($d, $row) {
        return '<html><font size="3">'.$d.'</font></html>';
    }),
     array('db' => 'FOLIO', 'dt' => 2, 'formatter' => function($d, $row) {
        return '<html><span class="badge badge-dark"><font size="2">'.$d.'</font></span></html>';
    }),
    array('db' => 'CATEGORIA', 'dt' => 3, 'formatter' => function($d, $row) {
        return '<html><span><font size="3">'.$d.'</font></span></html>';
    }),
    array('db' => 'COD_RUTPAC_SIN_PUNTOS', 'dt' => 4, 'formatter' => function($d, $row) {
        return '<html><span><font size="3">'.$d.'</font></span></html>';
    }),
    array('db' => 'FECHA_DERIVACION', 'dt' => 5, 'formatter' => function($d, $row) {
        $fechaLatino = date("d-m-Y", strtotime($row[5]));
        return '<html><font size="3">'.$fechaLatino.'</font></html>';
    }),
    array('db' => 'ESTADO_AVANCE', 'dt' => 6, 'formatter' => function($d, $row) {
        return '<html><font size="3">'.$d.'</font></html>';
    }),
    array('db' => 'MOTIVO_RECHAZO', 'dt' => 7, 'formatter' => function($d, $row) {
        return '<html><font size="3">'.$d.'</font></html>';
    }),
    array('db' => 'TIPO_CONTACTO_AVANCE', 'dt' => 8, 'formatter' => function($d, $row) {
        return '<html><font size="3">'.$d.'</font></html>';
    }),
    array('db' => 'GESTORA', 'dt' => 9, 'formatter' => function($d, $row) {
        return '<html><font size="3">'.$d.'</font></html>';
    }),
);


require('ssp.class.php');

echo json_encode(
    SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $whereAll)
);
