<?php
require_once '../../Connections/oirs.php';
require_once '../../includes/functions.inc.php';
require_once 'conecta.php';

$idUsuario= $_REQUEST['idUsuario'];
$idPaciente= $_REQUEST['idPaciente'];

$query_qrTipoUsuario = "SELECT * FROM $MM_oirs_DATABASE.login WHERE ID = '$idUsuario'";
$qrTipoUsuario = $oirs->SelectLimit($query_qrTipoUsuario) or die($oirs->ErrorMsg());
$totalRows_qrTipoUsuario = $qrTipoUsuario->RecordCount();

$tipo = $qrTipoUsuario->Fields('TIPO');


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
	g.MONTO

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

	LEFT JOIN montos g
	ON a.ID_DERIVACION = g.ID_DERIVACION

	WHERE
	g.TIPO_MONTO = 'inicial' AND
	a.ID_PACIENTE = '$idPaciente'
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
									<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBuscaDerivacionGestora" onclick="fnfrmBuscaDerivacionGestora('.$d.')">Ver detalle</a>
									<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('.$d.')">Bitacora</a> 
									</div>
								</div>';
					

	;}), 
	array( 'db' => 'N_DERIVACION', 'dt' => 1 , "formatter" => function($d, $row) {return '<html><span class="badge badge-warning"><font size="3">'.$d.'</font></span></html>';}),

	array( 'db' => 'ESTADO',  'dt' => 2 , "formatter" => function($d, $row) { 
						if ($row[2]=='pendiente') {
							return '<html><span class="badge" style="background-color: #4B5380;color: white;width: 80px;height: 50px;display:block;"><font size="2">derivacion <br/>pendiente</font></font></span></html>';
						}
						if ($row[2]=='aceptada') {
							return '<html><span class="badge" style="background-color: #4B5380;color: white;width: 80px;height: 50px;display:block;"><font size="2">derivacion <br/>aceptada</font></span></html>';
						}
						if ($row[2]=='prestador') {
							return '<html><span class="badge" style="background-color: #4B5380;color: white;width: 80px;height: 50px;display:block;"><font size="2">medico <br/>tratante <br/>asignado</font></span></html>';
						}
						if ($row[2]=='cerrada') {
							return '<html><span class="badge" style="background-color: #4B5380;color: white;width: 80px;height: 50px;display:block;"><font size="2">'.$d.'</font></span></html>';
						}
						if ($row[2]=='primeraConsultaAgendada') {
							return '<html><span style="background-color: #4B5380;color: white;width: 80px;height: 50px;display:block;" class="badge"><font size="2">primera <br/>consulta <br/>agendada</font></span></html>';
						}
						if ($row[2]=='segundaConsultaAgendada') {
							return '<html><span style="background-color: #4B5380;color: white;width: 80px;height: 50px;display:block;" class="badge"><font size="2">segunda <br/>consulta <br/>agendada</font></span></html>';
						}
						if ($row[2]=='otraConsultaAgendada') {
							return '<html><span style="background-color: #4B5380;color: white;width: 80px;height: 50px;display:block;" class="badge"><font size="2">otra <br/>consulta <br/>agendada</font></span></html>';
						}
						if ($row[2]=='primeraConsultaAtendida') {
							return '<html><span style="background-color: #4B5380;color: white;width: 80px;height: 50px;display:block;" class="badge"><font size="2">primera <br/>consulta <br/>atendida</font></span></html>';
						}
						if ($row[2]=='segundaConsultaAtendida') {
							return '<html><span style="background-color: #4B5380;color: white;width: 80px;height: 50px;display:block;" class="badge"><font size="2">segunda <br/>consulta <br/>atendida</font></span></html>';
						}
						if ($row[2]=='otraConsultaAtendida') {
							return '<html><span style="background-color: #4B5380;color: white;width: 80px;height: 50px;display:block;" class="badge"><font size="2">otra <br/>consulta <br/>atendida</font></span></html>';
						}

						;}),

	array( 'db' => 'FECHA_DERIVACION',  'dt' => 3 , "formatter" => function($d, $row) {
						$fechaLatino = date("d-m-Y",strtotime($row[3]));
						return '<html><font size="3">'.$fechaLatino.'</font></html>';
						}),

	array( 'db' => 'MONTO',  'dt' => 4 , "formatter" => function($d, $row) {
						$monto = number_format($d);
						return '<html><font size="3">$'.$monto.'</font></html>';}),

	array( 'db' => 'RUT_PACIENTE',  'dt' => 5 , "formatter" => function($d, $row) {
						// $codRutPac = explode(".", $row[5]);
						// $rut0 = $codRutPac[0]; // porción1
						// $rut1 = $codRutPac[1]; // porción2
						// $rut2 = $codRutPac[2]; // porción2
						// $codRutPac = $rut0.$rut1.$rut2;
						return '<html><font size="3">'.$d.'</font></html>'; 
						;}),

	array( 'db' => 'NOMBRE_PACIENTE',  'dt' => 6 , "formatter" => function($d, $row) {
						return '<html><font size="3"><a href="#" data-toggle="modal" data-target="#modalEditaInformacionPacienteSupervisora" onclick="fnFrmEditaInformacionPacienteSupervisora('.$row[0].')">'.$row[6].'</a></font></html>'; 
						;}),

	array( 'db' => 'DESC_TIPO_PATOLOGIA',  'dt' => 7 ),
	array( 'db' => 'DESC_PATOLOGIA',  'dt' => 8 ),
	array( 'db' => 'DESC_CONVENIO',  'dt' => 9 ),
	array( 'db' => 'NOMBRE_PROFESIONAL',  'dt' => 10),
	array( 'db' => 'ID_DERIVACION',  'dt' => 11, "formatter" => function($d, $row) {return '<html><font size="3">'.$row[0].'</font></html>';}),
	
);

require( 'ssp.class.php' );

echo json_encode(
	SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $whereAll )
);


