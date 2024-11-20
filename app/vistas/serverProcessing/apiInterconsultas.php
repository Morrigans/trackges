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
	   a.FOLIO,
	   a.ESTADO_RN,
	   b.COD_RUTPAC AS RUT_PACIENTE,
	   b.NOMBRE AS NOMBRE_PACIENTE,
	   d.DESC_CONVENIO,
	   a.ESTADO,
	   a.FECHA_DERIVACION,
	   e.DESC_TIPO_PATOLOGIA,
	   f.DESC_PATOLOGIA,
	   c.NOMBRE AS NOMBRE_PROFESIONAL,
	   a.MONTO_ACUMULADO_RN,
	   api_interconsultas.ID_INGRESO,
	   api_interconsultas.ESTADO AS ESTADO_INTERCONSULTA,
	   api_interconsultas.ESPECIALIDAD,
	   api_interconsultas.FECHA_SOLICITUD,
	   api_interconsultas.FECHA_FINALIZADA,
	   api_interconsultas.PROFESIONAL,
	   api_interconsultas.DEMORA
	     
	   FROM api_interconsultas
	   
	   LEFT JOIN derivaciones a
	   ON api_interconsultas.ID_DERIVACION = a.ID_DERIVACION

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

	     ) temp
	EOT;
}

if ($tipo == 3) {
	// DB table to use
	$table = <<<EOT
	 (
	   SELECT 
	   a.N_DERIVACION,
	   a.ID_DERIVACION,
	   a.FOLIO,
	   a.ESTADO_RN,
	   b.COD_RUTPAC AS RUT_PACIENTE,
	   b.NOMBRE AS NOMBRE_PACIENTE,
	   d.DESC_CONVENIO,
	   a.ESTADO,
	   a.FECHA_DERIVACION,
	   e.DESC_TIPO_PATOLOGIA,
	   f.DESC_PATOLOGIA,
	   c.NOMBRE AS NOMBRE_PROFESIONAL,
	   a.MONTO_ACUMULADO_RN,
	   api_interconsultas.ID_INGRESO,
	   api_interconsultas.ESTADO AS ESTADO_INTERCONSULTA,
	   api_interconsultas.ESPECIALIDAD,
	   api_interconsultas.FECHA_SOLICITUD,
	   api_interconsultas.FECHA_FINALIZADA,
	   api_interconsultas.PROFESIONAL,
	   api_interconsultas.DEMORA
	     
	   FROM api_interconsultas
	   
	   LEFT JOIN derivaciones a
	   ON api_interconsultas.ID_DERIVACION = a.ID_DERIVACION

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

	     ) temp
	EOT;
}
// Table's primary key
$primaryKey = 'ID_DERIVACION';

$columns = array(
	array( 'db' => 'ID_DERIVACION', 'dt' => 0 , "formatter" => function($d, $row) {
							return ' 
								<div class="btn-group">
									<button type="button" class="btn btn-default"><i class="fas fa-cog"></i></button>
									<button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
									<div class="dropdown-menu" role="menu">
									
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

	array( 'db' => 'ID_INGRESO',  'dt' => 7 , "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

	array( 'db' => 'ESTADO_INTERCONSULTA',  'dt' => 8 , "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

	array( 'db' => 'ESPECIALIDAD',  'dt' => 9 , "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

	array( 'db' => 'FECHA_SOLICITUD',  'dt' => 10, "formatter" => function($d, $row) {
						$fechaLatino = date("d-m-Y",strtotime($row[10]));
						return '<html><font size="3">'.$fechaLatino.'</font></html>';
						}),

	array( 'db' => 'FECHA_FINALIZADA',  'dt' => 11, "formatter" => function($d, $row) {
						$fechaLatino = date("d-m-Y",strtotime($row[11]));
						return '<html><font size="3">'.$fechaLatino.'</font></html>';
						}),

	array( 'db' => 'PROFESIONAL',  'dt' => 12, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),
	
	array( 'db' => 'DEMORA',  'dt' => 13, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),

	array( 'db' => 'ID_DERIVACION',  'dt' => 14, "formatter" => function($d, $row) {return '<html><font size="2">'.$row[0].'</font></html>';}),
	
);


require( 'ssp.class.php' );

echo json_encode(
	SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $whereAll )
);


