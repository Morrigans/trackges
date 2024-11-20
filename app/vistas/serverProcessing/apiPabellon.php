<?php
require_once '../../Connections/oirs.php';
require_once '../../includes/functions.inc.php';
require_once 'conecta.php';

$idUsuario= $_REQUEST['idUsuario'];

$query_qrTipoUsuario = "SELECT * FROM $MM_oirs_DATABASE.login WHERE ID = '$idUsuario'";
$qrTipoUsuario = $oirs->SelectLimit($query_qrTipoUsuario) or die($oirs->ErrorMsg());
$totalRows_qrTipoUsuario = $qrTipoUsuario->RecordCount();

$tipo = $qrTipoUsuario->Fields('TIPO');
$idClinica = $qrTipoUsuario->Fields('ID_PRESTADOR');

date_default_timezone_set('America/Santiago');
 $hoy= date('Y-m-d');


if ($tipo == 1 or $tipo == 2) {
	// DB table to use
	$table = <<<EOT
	 (
	  SELECT 
	    a.N_DERIVACION,
	    a.ID_DERIVACION,
	    b.COD_RUTPAC AS RUT_PACIENTE,
	    b.NOMBRE AS NOMBRE_PACIENTE,
	    d.DESC_CONVENIO,
	    a.ESTADO,
	    a.FECHA_DERIVACION,
	    e.DESC_TIPO_PATOLOGIA,
	    f.DESC_PATOLOGIA,
	    c.NOMBRE AS NOMBRE_PROFESIONAL,
	    a.MONTO_ACUMULADO_RN,
	    a.ESTADO_RN,
	    a.FOLIO,
	    api_pabellones_historico.fecha_reserva,
	    api_pabellones_historico.id_reserva,
	    api_pabellones_historico.fecha_inicio_pabellon,
	    api_pabellones_historico.nombre_medico,
	    api_pabellones_historico.codigo_prestacion,
	    api_pabellones_historico.nombre_cirugia,
	    api_pabellones_historico.estado as estado_apiPabellon,
	    api_pabellones_historico.ESTADO_VALIDACION

	    FROM api_pabellones_historico

	    LEFT JOIN derivaciones a
	    ON api_pabellones_historico.ID_DERIVACION= a.ID_DERIVACION

	    LEFT JOIN login c
	    ON a.ENFERMERA = c.ID

	    LEFT JOIN pacientes b
	    ON a.ID_PACIENTE = b.ID

	    LEFT JOIN convenio d
	    ON a.ID_CONVENIO = d.ID_CONVENIO

	    LEFT JOIN tipo_patologia e
	    ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

	    LEFT JOIN patologia f
	    ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

	    WHERE
	    a.ESTADO != 'cerrada' AND a.ESTADO_ANULACION = 'activo' AND
	    (a.ESTADO_RN = 'Prestador Asignado' or a.ESTADO_RN = 'Derivacion Aceptada' or a.ESTADO_RN = 'Solicita autorizacion')

	     ) temp
	EOT;
}

 // AND
// api_pabellones_historico.fecha_registro = '$hoy'

if ($tipo == 3) {
	// DB table to use
	$table = <<<EOT
	 (
	  SELECT 
	    a.N_DERIVACION,
	    a.ID_DERIVACION,
	    b.COD_RUTPAC AS RUT_PACIENTE,
	    b.NOMBRE AS NOMBRE_PACIENTE,
	    d.DESC_CONVENIO,
	    a.ESTADO,
	    a.FECHA_DERIVACION,
	    e.DESC_TIPO_PATOLOGIA,
	    f.DESC_PATOLOGIA,
	    c.NOMBRE AS NOMBRE_PROFESIONAL,
	    a.MONTO_ACUMULADO_RN,
	    a.ESTADO_RN,
	    a.FOLIO,
	    api_pabellones_historico.fecha_reserva,
	    api_pabellones_historico.id_reserva,
	    api_pabellones_historico.fecha_inicio_pabellon,
	    api_pabellones_historico.nombre_medico,
	    api_pabellones_historico.codigo_prestacion,
	    api_pabellones_historico.nombre_cirugia,
	    api_pabellones_historico.estado as estado_apiPabellon,
	    api_pabellones_historico.ESTADO_VALIDACION

	    FROM api_pabellones_historico

	    LEFT JOIN derivaciones a
	    ON api_pabellones_historico.ID_DERIVACION= a.ID_DERIVACION

	    LEFT JOIN login c
	    ON a.ENFERMERA = c.ID

	    LEFT JOIN pacientes b
	    ON a.ID_PACIENTE = b.ID

	    LEFT JOIN convenio d
	    ON a.ID_CONVENIO = d.ID_CONVENIO

	    LEFT JOIN tipo_patologia e
	    ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

	    LEFT JOIN patologia f
	    ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

	    WHERE
	    a.ESTADO != 'cerrada' AND a.ESTADO_ANULACION = 'activo' AND
	    (a.ESTADO_RN = 'Prestador Asignado' or a.ESTADO_RN = 'Derivacion Aceptada' or a.ESTADO_RN = 'Solicita autorizacion')

	     ) temp
	EOT;
}
// Table's primary key
$primaryKey = 'ID_DERIVACION';

/*BANDEJA FILTRADA POR EQUIPO GESTION
SELECT 
	a.N_DERIVACION,
	a.ID_DERIVACION,
	b.COD_RUTPAC AS RUT_PACIENTE,
	b.NOMBRE AS NOMBRE_PACIENTE,
	d.PREVISION,
	a.ESTADO,
	a.FECHA_DERIVACION,
	e.DESC_TIPO_PATOLOGIA,
	f.DESC_PATOLOGIA,
	c.NOMBRE AS NOMBRE_PROFESIONAL,
	g.MONTO,
    login.NOMBRE

	FROM derivaciones a

	LEFT JOIN login c
	ON a.ENFERMERA = c.ID

	LEFT JOIN pacientes b
	ON a.ID_PACIENTE = b.ID

	LEFT JOIN prevision d
	ON a.ID_CONVENIO = d.ID

	LEFT JOIN tipo_patologia e
	ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

	LEFT JOIN patologia f
	ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

	LEFT JOIN montos g
	ON a.ID_DERIVACION = g.ID_DERIVACION
    
    LEFT JOIN team_gestion
	ON a.ID_DERIVACION = team_gestion.ID_DERIVACION
    
    LEFT JOIN login
	ON team_gestion.ID_PROFESIONAL = login.ID

	WHERE
	a.ESTADO != 'cerrada' AND
	g.TIPO_MONTO = 'inicial' AND
    login.USUARIO = '17.876.465-9' AND
    c.ID_PRESTADOR = '$idClinica'
    
    group by a.ID_DERIVACION
*/


/* BANDEJA CON ADMINISTRATIVOS Y TENS
SELECT 
	a.N_DERIVACION,
	a.ID_DERIVACION,
	b.COD_RUTPAC AS RUT_PACIENTE,
	b.NOMBRE AS NOMBRE_PACIENTE,
	d.PREVISION,
	a.ESTADO,
	a.FECHA_DERIVACION,
	e.DESC_TIPO_PATOLOGIA,
	f.DESC_PATOLOGIA,
	c.NOMBRE AS NOMBRE_PROFESIONAL,
	g.MONTO,
    (SELECT
     group_concat(distinct login.NOMBRE)
     FROM team_gestion, login 
     WHERE 
     a.ID_DERIVACION = team_gestion.ID_DERIVACION AND
     login.ID = team_gestion.ID_PROFESIONAL) as TEAM_GESTION

	FROM derivaciones a

	LEFT JOIN login c
	ON a.ENFERMERA = c.ID

	LEFT JOIN pacientes b
	ON a.ID_PACIENTE = b.ID

	LEFT JOIN prevision d
	ON a.ID_CONVENIO = d.ID

	LEFT JOIN tipo_patologia e
	ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

	LEFT JOIN patologia f
	ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

	LEFT JOIN montos g
	ON a.ID_DERIVACION = g.ID_DERIVACION
    
	WHERE
	a.ESTADO != 'cerrada' AND
	g.TIPO_MONTO = 'inicial' AND
	c.ID_PRESTADOR = '$idClinica'
    group by a.ID_DERIVACION
*/



$columns = array(
	array( 'db' => 'ID_DERIVACION', 'dt' => 0 , "formatter" => function($d, $row) {
							return ' 
								<div class="btn-group">
									<button type="button" class="btn btn-default"><i class="fas fa-cog"></i></button>
									<button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
									<div class="dropdown-menu" role="menu">
									<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalValidarRechazarPab" onclick="fnfrmValidarRechazarPab('.$d.')">Validar/Rechazar para GES</a>
									<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('.$d.')">Bitacora</a> 
									</div>
								</div>';

	;}), 
	array( 'db' => 'N_DERIVACION', 'dt' => 1 , "formatter" => function($d, $row) {return '<html><span class="badge badge-warning"><font size="3">'.$d.'</font></span></html>';}),

	array( 'db' => 'FOLIO', 'dt' => 2 , "formatter" => function($d, $row) {return '<html><span class="badge badge-default"><font size="2">'.$d.'</font></span></html>';}),

	array( 'db' => 'ESTADO_RN',  'dt' => 3 , "formatter" => function($d, $row) {return '<html><span class="badge badge-default"><font size="2">'.$d.'</font></span></html>';}),

	array( 'db' => 'RUT_PACIENTE',  'dt' => 4 , "formatter" => function($d, $row) {
						// $codRutPac = explode(".", $row[7]);
						// $rut0 = $codRutPac[0]; // porción1
						// $rut1 = $codRutPac[1]; // porción2
						// $rut2 = $codRutPac[2]; // porción2
						// $codRutPac = $rut0.$rut1.$rut2;
						return '<html><font size="3">'.$d.'</font></html>'; 
						;}),

	array( 'db' => 'NOMBRE_PACIENTE',  'dt' => 5 , "formatter" => function($d, $row) {
						return '<html><font size="2"><a href="#" data-toggle="modal" data-target="#modalEditaInformacionPacienteSupervisora" onclick="fnFrmEditaInformacionPacienteSupervisora('.$row[0].')">'.$row[5].'</a></font></html>'; 
						;}),

	array( 'db' => 'DESC_PATOLOGIA',  'dt' => 6 , "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

	array( 'db' => 'fecha_reserva',  'dt' => 7 , "formatter" => function($d, $row) {
						$fechaLatino = date("d-m-Y",strtotime($row[7]));
						return '<html><font size="3">'.$fechaLatino.'</font></html>';
						}),

	array( 'db' => 'id_reserva',  'dt' => 8 , "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

	array( 'db' => 'fecha_inicio_pabellon',  'dt' => 9 , "formatter" => function($d, $row) {
						$fechaLatino = date("d-m-Y",strtotime($row[9]));
						return '<html><font size="3">'.$fechaLatino.'</font></html>';
						}),

	array( 'db' => 'nombre_medico',  'dt' => 10, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

	array( 'db' => 'codigo_prestacion',  'dt' => 11, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

	array( 'db' => 'nombre_cirugia',  'dt' => 12, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

	array( 'db' => 'estado_apiPabellon',  'dt' => 13, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

	array( 'db' => 'ESTADO_VALIDACION',  'dt' => 14 , "formatter" => function($d, $row) {return '<html><span class="badge badge-default"><font size="2">'.$d.'</font></span></html>';}),

	array( 'db' => 'NOMBRE_PROFESIONAL',  'dt' => 15, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),
	
	array( 'db' => 'ID_DERIVACION',  'dt' => 16, "formatter" => function($d, $row) {return '<html><font size="2">'.$row[0].'</font></html>';}),
	
);

require( 'ssp.class.php' );

echo json_encode(
	SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $whereAll )
);


