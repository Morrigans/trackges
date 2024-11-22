<?php 

$idUsuario = $_REQUEST['idUsuario'];

require_once '../../../../Connections/oirs.php';
require_once '../../../../includes/functions.inc.php';
require_once '../conecta.php';

$query_qrTipoUsuario = "SELECT * FROM $MM_oirs_DATABASE.login WHERE ID = '$idUsuario'";
$qrTipoUsuario = $oirs->SelectLimit($query_qrTipoUsuario) or die($oirs->ErrorMsg());
$totalRows_qrTipoUsuario = $qrTipoUsuario->RecordCount();

$tipo = $qrTipoUsuario->Fields('TIPO');

date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d'); 

    // DB table to use
    $table = <<<EOT
    (
        SELECT DISTINCT
        a.ID_DERIVACION,
        a.FOLIO,
        a.N_DERIVACION,
        a.FECHA_DERIVACION,
        a.COD_RUTPAC,
        a.INTERVENCION_SANITARIA,
        a.ESTADO_RN,
        REPLACE(a.COD_RUTPAC, '.', '') AS COD_RUTPAC_SIN_PUNTOS,
        b.NOMBRE AS NOMBRE_PACIENTE,
        med.NOMBRE AS MEDICO,
        adm.NOMBRE AS ADMINISTRATIVA,
        c.NOMBRE AS NOMBRE_PROFESIONAL,
        2_api_censo.fecha_censo,
        2_api_censo.id_admision,
        2_api_censo.fecha_ingreso,
        2_api_censo.dias_ingresado,
        2_api_censo.codigo_prestacion,
        2_api_censo.diagnostico,
        2_api_censo.nombre_convenio,
        2_api_censo.ley_urgencia,
        2_api_censo.fecha_foto,
        2_api_censo.ESTADO AS ESTADO_CENSO 
    
        FROM 2_api_censo  

          LEFT JOIN 2_derivaciones a
          ON 2_api_censo.ID_DERIVACION = a.ID_DERIVACION

          LEFT JOIN 2_derivaciones_hijos di 
          ON a.ID_DERIVACION = di.ID_DERIVACION

          LEFT JOIN pacientes b 
          ON a.COD_RUTPAC = b.COD_RUTPAC

          LEFT JOIN login g 
          ON a.TENS = g.USUARIO

          LEFT JOIN login c 
          ON a.ENFERMERA = c.USUARIO

          LEFT JOIN login med 
          ON a.MEDICO = med.USUARIO

          LEFT JOIN login adm 
          ON a.ADMINISTRATIVA = adm.USUARIO
          
          WHERE
          a.ESTADO_RN != 'anulado' AND
          2_api_censo.fecha_registro = '$hoy'
    ) temp
    EOT;

// Table's primary key
$primaryKey = 'ID_DERIVACION';

$columns = array(
    array( 'db' => 'ID_DERIVACION', 'dt' => 0 , "formatter" => function($d, $row) {
                            return ' 
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default"><i class="fas fa-cog"></i></button>
                                    <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
                                    <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalValidarRechazar" onclick="fnfrmValidarRechazar('.$d.')">Validar/Rechazar para GES</a>
                                    <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('.$d.')">Bitacora</a> 
                                    </div>
                                </div>';

    ;}), 

    array( 'db' => 'FOLIO', 'dt' => 1 , "formatter" => function($d, $row) {return '<html><span class="badge badge-default"><font size="2">'.$d.'</font></span></html>';}),


    array( 'db' => 'ESTADO_RN',  'dt' => 2 , "formatter" => function($d, $row) {return '<html><span class="badge badge-default"><font size="2">'.$d.'</font></span></html>';}),


    array( 'db' => 'COD_RUTPAC_SIN_PUNTOS',  'dt' => 3 , "formatter" => function($d, $row) {
                        // $codRutPac = explode(".", $row[7]);
                        // $rut0 = $codRutPac[0]; // porción1
                        // $rut1 = $codRutPac[1]; // porción2
                        // $rut2 = $codRutPac[2]; // porción2
                        // $codRutPac = $rut0.$rut1.$rut2;
                        return '<html><font size="3">'.$d.'</font></html>'; 
                        ;}),

    array( 'db' => 'NOMBRE_PACIENTE',  'dt' => 4 , "formatter" => function($d, $row) {
                        return '<html><font size="2"><a href="#" data-toggle="modal" data-target="#modalEditaInformacionPacienteSupervisora" onclick="fnFrmEditaInformacionPacienteSupervisora('.$row[0].')">'.$row[4].'</a></font></html>'; 
                        ;}),

    array( 'db' => 'INTERVENCION_SANITARIA',  'dt' => 5 , "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

    array( 'db' => 'fecha_censo',  'dt' => 6 , "formatter" => function($d, $row) {
                        $fechaLatino = date("d-m-Y",strtotime($row[6]));
                        return '<html><font size="3">'.$fechaLatino.'</font></html>';
                        }),

    array( 'db' => 'id_admision',  'dt' => 7, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

    array( 'db' => 'fecha_ingreso',  'dt' => 8 , "formatter" => function($d, $row) {
                        $fechaLatino = date("d-m-Y",strtotime($row[8]));
                        return '<html><font size="3">'.$fechaLatino.'</font></html>';
                        }),

    array( 'db' => 'dias_ingresado',  'dt' => 9, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

    array( 'db' => 'codigo_prestacion',  'dt' => 10, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

    array( 'db' => 'diagnostico',  'dt' => 11, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

    array( 'db' => 'nombre_convenio',  'dt' => 12, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

    array( 'db' => 'ley_urgencia',  'dt' => 13, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

    array( 'db' => 'NOMBRE_PROFESIONAL',  'dt' => 14, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

    array( 'db' => 'ESTADO_CENSO',  'dt' => 15 , "formatter" => function($d, $row) {return '<html><span class="badge badge-default"><font size="2">'.$d.'</font></span></html>';}),

    array( 'db' => 'ID_DERIVACION',  'dt' => 16, "formatter" => function($d, $row) {return '<html><font size="2">'.$row[0].'</font></html>';}),
    
);




require( '../ssp.class.php' );

echo json_encode(
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $whereAll )
);

