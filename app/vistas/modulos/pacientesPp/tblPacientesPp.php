<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';


$estado = $_REQUEST['estado'];
$vencidas = $_REQUEST['vencidas'];
date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');
$fechaLimite = date("Y-m-d",strtotime($hoy."+ 10 days"));

$usuario = $_SESSION['dni'];


require_once('asignarCita/modalAsignarCitaPp.php');
require_once('cerrarCaso/modalCerrarCasoPp.php');
require_once('atenderPaciente/modalAtenderPacientePp.php');
require_once('reasignarCaso/modalReasignarCasoPp.php');
require_once('asignarCaso/modalAsignarCaso.php');
// require_once('bitacora/modals/modalBitacora.php');
// require_once('asignarPrestadorCaso/modalAsignarPrestadorCaso.php');
require_once('derivacion/modalDerivacion.php');
require_once('asignarMedicoCaso/modalAsignarMedicoCaso.php');
require_once('asignarTeamGestion/modalTeamGestion.php');
require_once('asignarPatologiaEtapaCanasta/modalAsignarPatologiaEtapaCanasta.php');
require_once('contactarPaciente/modalContactarPaciente.php');
require_once('aceptarCaso/modalAceptarCasoPp.php');
// require_once('informacionPaciente/modalEditaInformacionPacienteSupervisora.php');
require_once('enviaInfoPacACorreo/modalEnviarACorreo.php');
// require_once('../../../vistas/modulos/asignarAdministrativa/modalAsignarAdministrativa.php');

$estado = $_REQUEST['estado'];
$vencidas = $_REQUEST['vencidas'];
date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');


if ($estado == '') {
	$query_qrDerivacion = "
	SELECT 
		derivaciones_pp.N_DERIVACION,
		derivaciones_pp.ID_DERIVACION,
		pacientes.COD_RUTPAC AS RUT_PACIENTE,
		pacientes.NOMBRE AS NOMBRE_PACIENTE,
		login.NOMBRE AS NOMBRE_PROFESIONAL,
		login.USUARIO AS RUT_PROFESIONAL,
		prevision.PREVISION,
		derivaciones_pp.ESTADO,
		derivaciones_pp.DECRETO,
		derivaciones_pp.FECHA_DERIVACION,
		tipo_patologia.DESC_TIPO_PATOLOGIA,
		patologia_pp.DESC_PATOLOGIA,
		derivaciones_pp.REASIGNADA,
		derivaciones_pp.RUT_PRESTADOR,
		derivaciones_pp.CODIGO_TIPO_PATOLOGIA,
		derivaciones_pp.CODIGO_PATOLOGIA,
		prestador.DESC_PRESTADOR,
		derivaciones_pp.ADMINISTRATIVA

		FROM derivaciones_pp

		LEFT JOIN login 
		ON derivaciones_pp.ENFERMERA = login.USUARIO

		LEFT JOIN pacientes 
		ON derivaciones_pp.ID_PACIENTE = pacientes.ID

		LEFT JOIN prevision 
		ON derivaciones_pp.ID_CONVENIO = prevision.ID

		LEFT JOIN tipo_patologia 
		ON derivaciones_pp.CODIGO_TIPO_PATOLOGIA = tipo_patologia.ID_TIPO_PATOLOGIA

		LEFT JOIN patologia_pp 
		ON derivaciones_pp.ID_PATOLOGIA = patologia_pp.ID_PATOLOGIA

		LEFT JOIN prestador 
		ON derivaciones_pp.RUT_PRESTADOR = prestador.ID_PRESTADOR 

	where 
	ESTADO!='cerrada'
	"; 
	$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
	$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

}

if ($estado == 'isapre') {
	$query_qrDerivacion = "
	SELECT 
		derivaciones_pp.N_DERIVACION,
		derivaciones_pp.ID_DERIVACION,
		pacientes.COD_RUTPAC AS RUT_PACIENTE,
		pacientes.NOMBRE AS NOMBRE_PACIENTE,
		login.NOMBRE AS NOMBRE_PROFESIONAL,
		login.USUARIO AS RUT_PROFESIONAL,
		prevision.PREVISION,
		derivaciones_pp.ESTADO,
		derivaciones_pp.DECRETO,
		derivaciones_pp.FECHA_DERIVACION,
		tipo_patologia.DESC_TIPO_PATOLOGIA,
		patologia_pp.DESC_PATOLOGIA,
		derivaciones_pp.REASIGNADA,
		derivaciones_pp.RUT_PRESTADOR,
		derivaciones_pp.CODIGO_TIPO_PATOLOGIA,
		derivaciones_pp.CODIGO_PATOLOGIA,
		prestador.DESC_PRESTADOR,
		derivaciones_pp.ADMINISTRATIVA

		FROM derivaciones_pp

		LEFT JOIN login 
		ON derivaciones_pp.ENFERMERA = login.USUARIO

		LEFT JOIN pacientes 
		ON derivaciones_pp.ID_PACIENTE = pacientes.ID

		LEFT JOIN prevision 
		ON derivaciones_pp.ID_CONVENIO = prevision.ID

		LEFT JOIN tipo_patologia 
		ON derivaciones_pp.CODIGO_TIPO_PATOLOGIA = tipo_patologia.ID_TIPO_PATOLOGIA

		LEFT JOIN patologia_pp 
		ON derivaciones_pp.ID_PATOLOGIA = patologia_pp.ID_PATOLOGIA

		LEFT JOIN prestador 
		ON derivaciones_pp.RUT_PRESTADOR = prestador.ID_PRESTADOR 

	where 
	ESTADO!='cerrada' and ORIGEN = 'isapre'
	"; 
	$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
	$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

}

if ($estado == 'icrs') {
	$query_qrDerivacion = "
	SELECT 
		derivaciones_pp.N_DERIVACION,
		derivaciones_pp.ID_DERIVACION,
		pacientes.COD_RUTPAC AS RUT_PACIENTE,
		pacientes.NOMBRE AS NOMBRE_PACIENTE,
		login.NOMBRE AS NOMBRE_PROFESIONAL,
		login.USUARIO AS RUT_PROFESIONAL,
		prevision.PREVISION,
		derivaciones_pp.ESTADO,
		derivaciones_pp.DECRETO,
		derivaciones_pp.FECHA_DERIVACION,
		tipo_patologia.DESC_TIPO_PATOLOGIA,
		patologia_pp.DESC_PATOLOGIA,
		derivaciones_pp.REASIGNADA,
		derivaciones_pp.RUT_PRESTADOR,
		derivaciones_pp.CODIGO_TIPO_PATOLOGIA,
		derivaciones_pp.CODIGO_PATOLOGIA,
		prestador.DESC_PRESTADOR,
		derivaciones_pp.ADMINISTRATIVA

		FROM derivaciones_pp

		LEFT JOIN login 
		ON derivaciones_pp.ENFERMERA = login.USUARIO

		LEFT JOIN pacientes 
		ON derivaciones_pp.ID_PACIENTE = pacientes.ID

		LEFT JOIN prevision 
		ON derivaciones_pp.ID_CONVENIO = prevision.ID

		LEFT JOIN tipo_patologia 
		ON derivaciones_pp.CODIGO_TIPO_PATOLOGIA = tipo_patologia.ID_TIPO_PATOLOGIA

		LEFT JOIN patologia_pp 
		ON derivaciones_pp.ID_PATOLOGIA = patologia_pp.ID_PATOLOGIA

		LEFT JOIN prestador 
		ON derivaciones_pp.RUT_PRESTADOR = prestador.ID_PRESTADOR 

	where 
	ESTADO!='cerrada' and ORIGEN is null
	"; 
	$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
	$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

}

if($estado != 'pendiente' and $estado != 'aceptada' and $estado != 'prestador' and $estado != 'cerrada' and $estado != 'caec' and $estado !='' and $vencidas!='vencidas' and $estado!='isapre' and $estado!='icrs'){

$query_qrDerivacion = "
SELECT 
	d.N_DERIVACION,
	d.ID_DERIVACION,
	pacientes.COD_RUTPAC AS RUT_PACIENTE,
	pacientes.NOMBRE AS NOMBRE_PACIENTE,
	login.NOMBRE AS NOMBRE_PROFESIONAL,
	login.USUARIO AS RUT_PROFESIONAL,
	prevision.PREVISION,
	d.ESTADO,
	d.DECRETO,
	d.FECHA_DERIVACION,
	tipo_patologia.DESC_TIPO_PATOLOGIA,
	patologia_pp.DESC_PATOLOGIA,
	d.REASIGNADA,
	d.RUT_PRESTADOR,
	d.CODIGO_TIPO_PATOLOGIA,
	d.CODIGO_PATOLOGIA,
	prestador.DESC_PRESTADOR,
	d.ADMINISTRATIVA

FROM $MM_oirs_DATABASE.derivaciones_pp AS d
INNER JOIN $MM_oirs_DATABASE.derivaciones_canastas_pp AS dc ON d.ID_DERIVACION = dc.ID_DERIVACION

		LEFT JOIN login 
		ON d.ENFERMERA = login.USUARIO

		LEFT JOIN pacientes 
		ON d.ID_PACIENTE = pacientes.ID

		LEFT JOIN prevision 
		ON d.ID_CONVENIO = prevision.ID

		LEFT JOIN tipo_patologia 
		ON d.CODIGO_TIPO_PATOLOGIA = tipo_patologia.ID_TIPO_PATOLOGIA

		LEFT JOIN patologia_pp 
		ON d.ID_PATOLOGIA = patologia_pp.ID_PATOLOGIA

		LEFT JOIN prestador 
		ON d.RUT_PRESTADOR = prestador.ID_PRESTADOR 

		WHERE dc.FECHA_LIMITE <= '$fechaLimite'
		  AND dc.FECHA_LIMITE != '0000-00-00'
		  AND dc.DIAS_LIMITE != '0'
		  AND dc.FECHA_LIMITE >= '$hoy'
		  AND d.ESTADO != 'cerrada'
		  AND d.CODIGO_TIPO_PATOLOGIA = '1'
		  AND dc.ESTADO != 'finalizada'";
	$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
	$totalRows_qrDerivacion = $qrDerivacion->RecordCount();
}

//QUERY CALCULA VENCIDAS
if($estado != 'pendiente' and $estado != 'aceptada' and $estado != 'prestador'  and $estado != 'cerrada' and $estado != 'caec'  and $estado !='' and $vencidas=='vencidas'  and $estado!='isapre' and $estado!='icrs'){ 


	$query_qrDerivacion = "
	SELECT 
	d.N_DERIVACION,
	d.ID_DERIVACION,
	pacientes.COD_RUTPAC AS RUT_PACIENTE,
	pacientes.NOMBRE AS NOMBRE_PACIENTE,
	login.NOMBRE AS NOMBRE_PROFESIONAL,
	login.USUARIO AS RUT_PROFESIONAL,
	prevision.PREVISION,
	d.ESTADO,
	d.DECRETO,
	d.FECHA_DERIVACION,
	tipo_patologia.DESC_TIPO_PATOLOGIA,
	patologia_pp.DESC_PATOLOGIA,
	d.REASIGNADA,
	d.RUT_PRESTADOR,
	d.CODIGO_TIPO_PATOLOGIA,
	d.CODIGO_PATOLOGIA,
	prestador.DESC_PRESTADOR,
	d.ADMINISTRATIVA

FROM $MM_oirs_DATABASE.derivaciones_pp AS d
INNER JOIN $MM_oirs_DATABASE.derivaciones_canastas_pp AS dc ON d.ID_DERIVACION = dc.ID_DERIVACION

LEFT JOIN login 
		ON d.ENFERMERA = login.USUARIO

		LEFT JOIN pacientes 
		ON d.ID_PACIENTE = pacientes.ID

		LEFT JOIN prevision 
		ON d.ID_CONVENIO = prevision.ID

		LEFT JOIN tipo_patologia 
		ON d.CODIGO_TIPO_PATOLOGIA = tipo_patologia.ID_TIPO_PATOLOGIA

		LEFT JOIN patologia_pp 
		ON d.ID_PATOLOGIA = patologia_pp.ID_PATOLOGIA

		LEFT JOIN prestador 
		ON d.RUT_PRESTADOR = prestador.ID_PRESTADOR 

		WHERE dc.FECHA_LIMITE < '$hoy'
		  AND dc.FECHA_LIMITE != '0000-00-00'
		  AND dc.DIAS_LIMITE != '0'
		  AND d.ESTADO != 'cerrada'
		  AND d.CODIGO_TIPO_PATOLOGIA = '1'
		  AND dc.ESTADO != 'finalizada'";
	$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
	$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

	
}



?>
<meta charset="UTF-8">
<div class="table-responsive-sm ">
<table id="tPacientesDerivados" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Opciones</font></th>
			<th><font size="2">Derivación</font></th>
			<th><font size="2">Estado</font></th>
			<th><font size="2">Fecha derivación</font></th>
			<th><font size="2">Prox. a vencer</font></th>
			<th><font size="2">Rut paciente</font></th>
			<th><font size="2">Nombre paciente</font></th>
			<th><font size="2">Tipo</font></th>
			<th><font size="2">Patología</font></th>
			<!-- <th><font size="2">Etapa patología</font></th> -->
			<th><font size="2">Canasta patología</font></th> 			 
			<th><font size="2">Isapre</font></th> 
			<th><font size="2">Gestora</font></th> 
			<th><font size="2">N°</font></th>
			
		</tr>
	</thead>
	<tbody>
	<?php
		$n=1;
	while (!$qrDerivacion->EOF) {
		$codRutPac = $qrDerivacion->Fields('RUT_PACIENTE');
		$idDerivacion = $qrDerivacion->Fields('ID_DERIVACION');
		$decreto = $qrDerivacion->Fields('DECRETO');
		$codTipoPatologia = $qrDerivacion->Fields('CODIGO_TIPO_PATOLOGIA');

		// $query_qrBuscaProgramacionTarea= "SELECT ID_DERIVACION FROM $MM_oirs_DATABASE.bitacora WHERE ID_DERIVACION = '$idDerivacion' AND PROGRAMADO = 'si'";
		// $qrBuscaProgramacionTarea = $oirs->SelectLimit($query_qrBuscaProgramacionTarea) or die($oirs->ErrorMsg());
		// $totalRows_qrBuscaProgramacionTarea = $qrBuscaProgramacionTarea->RecordCount();

		$query_qrDerivacionCanasta= "SELECT FECHA_LIMITE, FECHA_CANASTA,CODIGO_CANASTA_PATOLOGIA FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='activa' order by FECHA_LIMITE ASC";
		$qrDerivacionCanasta = $oirs->SelectLimit($query_qrDerivacionCanasta) or die($oirs->ErrorMsg());
		$totalRows_qrDerivacionCanasta = $qrDerivacionCanasta->RecordCount();

		// $query_qrDerivacionEtapa= "SELECT CODIGO_ETAPA_PATOLOGIA,ID_ETAPA_PATOLOGIA FROM $MM_oirs_DATABASE.derivaciones_etapas WHERE ID_DERIVACION = '$idDerivacion'";
		// $qrDerivacionEtapa = $oirs->SelectLimit($query_qrDerivacionEtapa) or die($oirs->ErrorMsg());
		// $totalRows_qrDerivacionEtapa = $qrDerivacionEtapa->RecordCount();

		$query_qrPrestadorAsig= "SELECT RUT_PRESTADOR FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_DERIVACION = '$idDerivacion'";
		$qrPrestadorAsig = $oirs->SelectLimit($query_qrPrestadorAsig) or die($oirs->ErrorMsg());
		$totalRows_qrPrestadorAsig = $qrPrestadorAsig->RecordCount();
	?>
		<tr>
			<td>
				<div class="btn-group">
					<button type="button" class="btn btn-default"><i class="fas fa-cog"></i></button>
					<button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
					<div class="dropdown-menu" role="menu">
						<?php if ($qrDerivacion->Fields('ESTADO') == 'pendiente') {
							$colorEstado = 'badge badge-primary';?>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAceptarCasoPp" onclick="fnAceptarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Aceptar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoPp" onclick="fnfrmCerrarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>							
							
						<?php } 
						

						if ($qrDerivacion->Fields('ESTADO') == 'aceptada') { //no reasignada
							$colorEstado = 'badge badge-info';
							?>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalDerivacion" onclick="fnfrmDetalleDerivacion('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Etapas/Canastas</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCasoPp" onclick="fnfrmAsignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalContactarPacientePp" onclick="fnfrmContactarPaciente('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Contactar paciente</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarTeamPp" onclick="fnfrmAsignarTeam('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Team Gestion</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarMedicoCasoPp" onclick="fnfrmAsignarMedicotratante('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Médico tratante</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoPp" onclick="fnfrmReasignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoPp" onclick="fnfrmCerrarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>

						<?php
						}

						if ($qrDerivacion->Fields('ESTADO') == 'cerrada') {
							$colorEstado = 'badge badge-danger';
							?>
						<?php
						}

						if ($qrDerivacion->Fields('ESTADO') == 'prestador') {
							$colorEstado = 'badge badge-success';
							?>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalDerivacion" onclick="fnfrmDetalleDerivacion('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Etapas/Canastas</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarTeamPp" onclick="fnfrmAsignarTeam('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Team Gestion</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalContactarPacientePp" onclick="fnfrmContactarPaciente('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Contactar paciente</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCitaPp" onclick="fnAsignarCitaPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Cita</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoPp" onclick="fnfrmReasignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoPp" onclick="fnfrmCerrarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
							
						<?php
						}

						if ($qrDerivacion->Fields('ESTADO') == 'primeraConsultaAgendada') {
							$colorEstado = 'badge badge-success';
							?>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalDerivacion" onclick="fnfrmDetalleDerivacion('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Etapas/Canastas</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCasoPp" onclick="fnfrmAsignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAtenderPacientePp" onclick="fnAtenderPacientePp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Atender Paciente</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCitaPp" onclick="fnAsignarCitaPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Cita</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarTeamPp" onclick="fnfrmAsignarTeam('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Team Gestion</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalContactarPacientePp" onclick="fnfrmContactarPaciente('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Contactar paciente</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoPp" onclick="fnfrmReasignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoPp" onclick="fnfrmCerrarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
							
						<?php
						}

						if ($qrDerivacion->Fields('ESTADO') == 'segundaConsultaAgendada') {
							$colorEstado = 'badge badge-success';
							?>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalDerivacion" onclick="fnfrmDetalleDerivacion('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Etapas/Canastas</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCasoPp" onclick="fnfrmAsignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAtenderPacientePp" onclick="fnAtenderPacientePp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Atender Paciente</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCitaPp" onclick="fnAsignarCitaPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Cita</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarTeamPp" onclick="fnfrmAsignarTeam('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Team Gestion</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalContactarPacientePp" onclick="fnfrmContactarPaciente('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Contactar paciente</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoPp" onclick="fnfrmReasignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoPp" onclick="fnfrmCerrarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
							
						<?php
						}
						if ($qrDerivacion->Fields('ESTADO') == 'otraConsultaAgendada') {
							$colorEstado = 'badge badge-success';
							?>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalDerivacion" onclick="fnfrmDetalleDerivacion('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Etapas/Canastas</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCasoPp" onclick="fnfrmAsignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAtenderPacientePp" onclick="fnAtenderPacientePp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Atender Paciente</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCitaPp" onclick="fnAsignarCitaPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Cita</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarTeamPp" onclick="fnfrmAsignarTeam('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Team Gestion</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalContactarPacientePp" onclick="fnfrmContactarPaciente('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Contactar paciente</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoPp" onclick="fnfrmReasignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoPp" onclick="fnfrmCerrarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
							
						<?php
						}
						if ($qrDerivacion->Fields('ESTADO') == 'primeraConsultaAtendida') {
							$colorEstado = 'badge badge-success';
							?>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalDerivacion" onclick="fnfrmDetalleDerivacion('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Etapas/Canastas</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCasoPp" onclick="fnfrmAsignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAtenderPacientePp" onclick="fnAtenderPacientePp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Atender Paciente</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCitaPp" onclick="fnAsignarCitaPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Cita</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoPp" onclick="fnfrmReasignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoPp" onclick="fnfrmCerrarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
							
						<?php
						}
						if ($qrDerivacion->Fields('ESTADO') == 'segundaConsultaAtendida') {
							$colorEstado = 'badge badge-success';
							?>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalDerivacion" onclick="fnfrmDetalleDerivacion('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Etapas/Canastas</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCasoPp" onclick="fnfrmAsignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAtenderPacientePp" onclick="fnAtenderPacientePp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Atender Paciente</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCitaPp" onclick="fnAsignarCitaPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Cita</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoPp" onclick="fnfrmReasignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoPp" onclick="fnfrmCerrarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
							
						<?php
						}
						if ($qrDerivacion->Fields('ESTADO') == 'otraConsultaAtendida') {
							$colorEstado = 'badge badge-success';
							?>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalDerivacion" onclick="fnfrmDetalleDerivacion('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Etapas/Canastas</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCasoPp" onclick="fnfrmAsignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAtenderPacientePp" onclick="fnAtenderPacientePp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Atender Paciente</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarCitaPp" onclick="fnAsignarCitaPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar Cita</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoPp" onclick="fnfrmReasignarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoPp" onclick="fnfrmCerrarCasoPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
							
						<?php
						}
						?>


						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacoraPp" onclick="fnfrmBitacoraPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Bitacora</a> 
						<a class="dropdown-item" target="_BLANK" href="vistas/modulos/pacientesPp/enviaInfoPacACorreo/detallePacientePdf.php?idDerivacion=<?php echo $idDerivacion ?>&codRutPac=<?php echo $codRutPac ?>">Genera Pdf</a>
					</div>
				</div>
			</td>
			<td><span class="badge badge-warning">
					<font size="2">
					<?php 
						
						echo $qrDerivacion->Fields('N_DERIVACION'); 
					?>
						
					</font>
				</span>
			</td>
			<td><font size="1">
				<?php 
				if ($qrDerivacion->Fields('ESTADO')=='pendiente') {
					echo '<html><span class="badge badge-success"><font size="2">pendiente</font></font></span></html>';
				}
				if ($qrDerivacion->Fields('ESTADO')=='aceptada') {
					echo '<html><span class="badge badge-info"><font size="2">aceptada</font></span></html>';
				}
				if ($qrDerivacion->Fields('ESTADO')=='prestador') {
					echo '<html><span class="badge badge-primary"><font size="2">medico asignado</font></span></html>';
				}
				if ($qrDerivacion->Fields('ESTADO')=='cerrada') {
					echo '<html><span class="badge badge-danger"><font size="2">'.$d.'</font></span></html>';
				}
				if ($qrDerivacion->Fields('ESTADO')=='primeraConsultaAgendada') {
					echo '<html><span class="badge badge-warning"><font size="2">1° consulta agendada</font></span></html>';
				}
				if ($qrDerivacion->Fields('ESTADO')=='segundaConsultaAgendada') {
					echo '<html><span class="badge badge-warning"><font size="2">2° consulta agendada</font></span></html>';
				}
				if ($qrDerivacion->Fields('ESTADO')=='otraConsultaAgendada') {
					echo '<html><span class="badge badge-warning"><font size="2">otra consulta agendada</font></span></html>';
				}
				if ($qrDerivacion->Fields('ESTADO')=='primeraConsultaAtendida') {
					echo '<html><span class="badge badge-warning"><font size="2">1° consulta atendida</font></span></html>';
				}
				if ($qrDerivacion->Fields('ESTADO')=='segundaConsultaAtendida') {
					echo '<html><span class="badge badge-warning"><font size="2">2° consulta atendida</font></span></html>';
				}
				if ($qrDerivacion->Fields('ESTADO')=='otraConsultaAtendida') {
					echo '<html><span class="badge badge-warning"><font size="2">otra consulta atendida</font></span></html>';
				}
				// echo $qrDerivacion->Fields('ESTADO'); 
				?></font></td>
			<td><font size="2"><?php echo date("d-m-Y",strtotime($qrDerivacion->Fields('FECHA_DERIVACION'))); ?></font></td>
			<td>
				<font size="2">
					<?php
					if ($qrDerivacionCanasta->Fields('FECHA_LIMITE') == '0000-00-00' or $qrDerivacionCanasta->Fields('FECHA_LIMITE') == null or $qrDerivacion->Fields('DESC_TIPO_PATOLOGIA') == 'CAEC' or $totalRows_qrDerivacionCanasta == 0) {
						echo 'Sin Limite';
					}else{
						echo $qrDerivacionCanasta->Fields('FECHA_LIMITE');
					} ?>
				</font>
			</td>
			<td>
				<font size="2">
					<?php 
						$codRutPac = explode(".", $codRutPac);
						$rut0 = $codRutPac[0]; // porción1
						$rut1 = $codRutPac[1]; // porción2
						$rut2 = $codRutPac[2]; // porción2
						$codRutPac = $rut0.$rut1.$rut2;
						echo $codRutPac;
					?>
				</font>
			</td>
			<td><a href="#" data-toggle="modal" data-target="#modalEditaInformacionPacienteSupervisora" onclick="fnFrmEditaInformacionPacienteSupervisora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')"><font size="1"><b><?php echo strtoupper($qrDerivacion->Fields('NOMBRE_PACIENTE')); ?></b></font></a></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('DESC_TIPO_PATOLOGIA'); ?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('DESC_PATOLOGIA'); ?></font></td>
			
			<td>
				<font size="2">
					<?php 
						if ($totalRows_qrDerivacionCanasta == 0) {
							echo 'No hay canastas activas';
						}else{
							$i=1;
							while (!$qrDerivacionCanasta->EOF) {
								$codCanastaPatologia = $qrDerivacionCanasta->Fields('CODIGO_CANASTA_PATOLOGIA');

								if ($codTipoPatologia == 1) {
									$query_qrCanastaPatologia= "SELECT DESC_CANASTA_PATOLOGIA FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanastaPatologia' and DECRETO = '$decreto'";
									$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
									$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();
								}
								if ($codTipoPatologia == 2) {
									$query_qrCanastaPatologia= "SELECT DESC_CANASTA_PATOLOGIA FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanastaPatologia'";
									$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
									$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();
								}

									echo $i.'.- <font size="1" color="grey"><strong>'.date("d-m-Y",strtotime($qrDerivacionCanasta->Fields('FECHA_CANASTA'))).'</strong></font> '.$qrCanastaPatologia->Fields('DESC_CANASTA_PATOLOGIA').'.</br>';

								$i++;
							$qrDerivacionCanasta->MoveNext();
							}
						}
					?>
				</font>
			</td>
		
			<td>
				<font size="2">
					<?php
					echo $qrDerivacion->Fields('PREVISION'); ?>
				</font>
			</td>
			<td>
				<font size="2">
					<?php
					echo $qrDerivacion->Fields('NOMBRE_PROFESIONAL'); ?>
				</font>
			</td>
			<td><font size="2"><?php echo $idDerivacion; ?></font></td>
			
		</tr>
		<?php
		$n++;
	 	$qrDerivacion->MoveNext();
	}
	?>
	</tbody>
</table>
</div>

<script type="text/javascript">
	
	$(function () {
	    $('#tPacientesDerivados').DataTable({
	      "paging": true,
	      "lengthChange": true,
	      "searching": true,
	      "ordering": true,
	      "info": true,
	      "autoWidth": true,
	      "responsive": true,
	      "order": [[ 12, 'desc' ]],
	      dom: 'lBfrtip',
		    buttons: [
		                {
		                    extend: 'excelHtml5',
		                    exportOptions: {
		                        columns: [ 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 ]
		                    }
		                },
		                {
		                    extend: 'pdfHtml5',
		                    exportOptions: {
		                        columns: [ 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 ]
		                    }
		                }
		                
		            ],
		    "language": {
		        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
		        },
		    // "columnDefs": [
      //               {
      //                   "targets": [ 1 ],
      //                   "visible": false,
      //                   "searchable": false
      //               }
                   
      //           ]
	    });

	    // // Manejar el evento de ordenamiento de DataTables
	    //  $('#tPacientesDerivados').on('order.dt', function() {
	    //    var table = $('#tPacientesDerivados').DataTable();
	    //    var order = table.order()[0];

	    //    // Si la columna ordenada es la columna de fecha
	    //    if (order[0] === 3) {
	    //      // Convertir las fechas al formato AAAA-MM-DD
	    //      table.column(3).nodes().each(function(cell, i) {
	    //        var fecha = $(cell).text();
	    //        var fechaAAAAMMDD = moment(fecha, 'DD/MM/YYYY').format('YYYY-MM-DD');
	    //        $(cell).text(fechaAAAAMMDD);
	    //      });

	    //      // Ordenar la tabla por la columna de fecha
	    //      table.order([3, order[1]]).draw();
	    //    }
	    //  });

	  });
</script>