<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';
require_once '../conecta.php';

$idUsuario= $_REQUEST['idUsuario'];

$query_qrTipoUsuario = "SELECT * FROM $MM_oirs_DATABASE.login WHERE ID = '$idUsuario'";
$qrTipoUsuario = $oirs->SelectLimit($query_qrTipoUsuario) or die($oirs->ErrorMsg());
$totalRows_qrTipoUsuario = $qrTipoUsuario->RecordCount();

$tipo = $qrTipoUsuario->Fields('TIPO');
$idClinica = $qrTipoUsuario->Fields('ID_PRESTADOR');

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
	g.NOMBRE as TENS,
	h.MOTIVO

	FROM derivaciones a

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

	LEFT JOIN login g
	ON a.TENS = g.ID

	LEFT JOIN motivo_vencida_no_finalizada h
	ON a.MOTIVO_VENCIDA_NO_FINALIZADA = h.ID_MOTIVO

	    	WHERE
	    	a.ESTADO != 'cerrada' AND
	    	
	        g.ID = '$idUsuario' and
	a.ESTADO_ANULACION = 'activo'
	        group by a.ID_DERIVACION
	 ) temp
	EOT;


// Table's primary key
$primaryKey = 'ID_DERIVACION';



$columns = array(
	array( 'db' => 'ID_DERIVACION', 'dt' => 0 , "formatter" => function($d, $row) {


					if ($row[4]=='Solicita autorización') {// se coloca primero por que asi esta opcion se cargar para todos los estados locales de mas abajo
						return ' 
							<div class="btn-group">
								<button type="button" class="btn btn-default"><i class="fas fa-cog"></i></button>
								<button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
								<div class="dropdown-menu" role="menu">								
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAgregarMarca" onclick="fnfrmAgregarMarca('.$d.')">Para cierre</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalContactarPaciente" onclick="fnfrmContactarPaciente('.$d.')">Contactar Paciente</a> 
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarPatologiaEtapaCanasta" onclick="fnfrmAsignarPatologiaEtapaCanasta('.$d.')">Asignar Patología</a>
								<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('.$d.')">Bitacora</a> 
								</div>
							</div>';
					}
						return ' 
								<div class="btn-group">
									<button type="button" class="btn btn-default"><i class="fas fa-cog"></i></button>
									<button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
									<div class="dropdown-menu" role="menu">
									<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalContactarPaciente" onclick="fnfrmContactarPaciente('.$d.')">Contactar Paciente</a>
									<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarPatologiaEtapaCanasta" onclick="fnfrmAsignarPatologiaEtapaCanasta('.$d.')">Asignar Patología</a>
									<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('.$d.')">Bitacora</a> 
									</div>
								</div>';

	;}), 
	array( 'db' => 'N_DERIVACION', 'dt' => 1 , "formatter" => function($d, $row) {return '<html><span class="badge badge-warning"><font size="3">'.$d.'</font></span></html>';}),

	array( 'db' => 'ESTADO',  'dt' => 2 , "formatter" => function($d, $row) { 
						if ($row[2]=='pendiente') {
							return '<html><span class="badge badge-success"><font size="2">pendiente</font></font></span></html>';
						}
						if ($row[2]=='aceptada') {
							return '<html><span class="badge badge-info"><font size="2">aceptada</font></span></html>';
						}
						if ($row[2]=='prestador') {
							return '<html><span class="badge badge-primary"><font size="2">medico asignado</font></span></html>';
						}
						if ($row[2]=='cerrada') {
							return '<html><span class="badge badge-danger"><font size="2">'.$d.'</font></span></html>';
						}
						if ($row[2]=='primeraConsultaAgendada') {
							return '<html><span class="badge badge-warning"><font size="2">1° consulta agendada</font></span></html>';
						}
						if ($row[2]=='segundaConsultaAgendada') {
							return '<html><span class="badge badge-warning"><font size="2">2° consulta agendada</font></span></html>';
						}
						if ($row[2]=='otraConsultaAgendada') {
							return '<html><span class="badge badge-warning"><font size="2">otra consulta agendada</font></span></html>';
						}
						if ($row[2]=='primeraConsultaAtendida') {
							return '<html><span class="badge badge-warning"><font size="2">1° consulta atendida</font></span></html>';
						}
						if ($row[2]=='segundaConsultaAtendida') {
							return '<html><span class="badge badge-warning"><font size="2">2° consulta atendida</font></span></html>';
						}
						if ($row[2]=='otraConsultaAtendida') {
							return '<html><span class="badge badge-warning"><font size="2">otra consulta atendida</font></span></html>';
						}

						;}),

	array( 'db' => 'FOLIO', 'dt' => 3 , "formatter" => function($d, $row) {return '<html><span class="badge badge-default"><font size="2">'.$d.'</font></span></html>';}),


	array( 'db' => 'ESTADO_RN',  'dt' => 4 , "formatter" => function($d, $row) {return '<html><span class="badge badge-default"><font size="2">'.$d.'</font></span></html>';}),

	array( 'db' => 'FECHA_DERIVACION',  'dt' => 5 , "formatter" => function($d, $row) {
						$fechaLatino = date("d-m-Y",strtotime($row[5]));
						return '<html><font size="3">'.$fechaLatino.'</font></html>';
						}),

	// array( 'db' => 'MONTO',  'dt' => 5, "formatter" => function($d, $row) {
	// 					$monto = number_format($d);
	// 					return '<html><font size="3">$'.$monto.'</font></html>';}),

	array( 'db' => 'MONTO_ACUMULADO_RN',  'dt' => 6, "formatter" => function($d, $row) {
						$montoRn = number_format($d);
						return '<html><font size="3">$'.$montoRn.'</font></html>';}),

	array( 'db' => 'RUT_PACIENTE',  'dt' => 7 , "formatter" => function($d, $row) {
						// $codRutPac = explode(".", $row[7]);
						// $rut0 = $codRutPac[0]; // porción1
						// $rut1 = $codRutPac[1]; // porción2
						// $rut2 = $codRutPac[2]; // porción2
						// $codRutPac = $rut0.$rut1.$rut2;
						return '<html><font size="3">'.$d.'</font></html>'; 
						;}),

	array( 'db' => 'NOMBRE_PACIENTE',  'dt' => 8 , "formatter" => function($d, $row) {
						return '<html><font size="2"><a href="#" data-toggle="modal" data-target="#modalEditaInformacionPacienteSupervisora" onclick="fnFrmEditaInformacionPacienteSupervisora('.$row[0].')">'.$row[8].'</a></font></html>'; 
						;}),

	array( 'db' => 'DESC_PATOLOGIA',  'dt' => 9 , "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),
	array( 'db' => 'MOTIVO',  'dt' => 10 , "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),
	array( 'db' => 'DESC_CONVENIO',  'dt' => 11 , "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),
	array( 'db' => 'NOMBRE_PROFESIONAL',  'dt' => 12, "formatter" => function($d, $row) {return '<html><font size="3">'.$d.'</font></html>';}),
	array( 'db' => 'ID_DERIVACION',  'dt' => 13, "formatter" => function($d, $row) {return '<html><font size="2">'.$row[0].'</font></html>';}),
	
);


require( '../ssp.class.php' );

echo json_encode(
	SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $whereAll )
);


