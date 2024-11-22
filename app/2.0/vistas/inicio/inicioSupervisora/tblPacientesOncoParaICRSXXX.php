<?php
require_once '../../../../Connections/crss.php';
require_once '../../../../includes/functions.inc.php';
require_once('../../../../vistas/modulos/segundoPrestador/clSantiago/bitacora/modals/modalBitacoraCRSS.php');

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }


$usuario = $_SESSION['dni'];
//require_once('vistas/bitacora/modals/modalBitacora.php');
// require_once('modals/aceptarCaso/modalAceptarCaso.php');
// require_once('modals/cerrarCaso/modalCerrarCaso.php');
// require_once('modals/reasignarCaso/modalReasignarCaso.php');
// require_once('modals/asignarPrestadorCaso/modalAsignarPrestadorCaso.php');
// require_once('modals/derivacion/modalDerivacion.php');
// require_once('modals/informacionPaciente/modalEditaInformacionPacienteSupervisora.php');
// require_once('modals/enviaInfoPacACorreo/modalEnviarACorreo.php');
// require_once('../../../vistas/modulos/asignarAdministrativa/modalAsignarAdministrativa.php');

// $estado = $_REQUEST['estado'];
// $vencidas = $_REQUEST['vencidas'];
date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');


	$query_qrDerivacion = "
		SELECT DISTINCT
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
			a.MONTO_DEVENGADO,
			a.DIAS_DESDE_CIRUGIA,
			p.QMT,
			p.DECRETO,
			g.NOMBRE as TENS,
			h.NOMBRE AS MEDICO

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

		LEFT JOIN login h
			ON a.RUT_PRESTADOR = h.ID

		LEFT JOIN derivaciones_canastas m
			ON a.ID_DERIVACION = m.ID_DERIVACION

		LEFT JOIN canasta_patologia p
			ON m.CODIGO_CANASTA_PATOLOGIA = p.CODIGO_CANASTA_PATOLOGIA

		WHERE
		
			f.ONCOLOGICO = 'si' and a.ESTADO <> 'cerrada' AND a.ESTADO_ANULACION <> 'anulado' AND
			(a.ESTADO_RN = 'Prestador Asignado' or a.ESTADO_RN = 'Derivacion Aceptada' or a.ESTADO_RN = 'Solicita autorizacion') AND
			p.QMT='1' AND p.DECRETO='LEP2225'
	"; 
	$qrDerivacion = $crss->SelectLimit($query_qrDerivacion) or die($crss->ErrorMsg());
	$totalRows_qrDerivacion = $qrDerivacion->RecordCount();


?>
<div class="table-responsive-sm">
<table id="tPacientesOncoCrss" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Opciones</font></th>
			<th><font size="2">Derivación</font></th>
			
			<th><font size="2">Folio RN</font></th>
			<th><font size="2">Estado RN</font></th>
			<th><font size="2">Fecha derivación</font></th>
			<th><font size="2">dias d/qx</font></th>
			<th><font size="2">Rut paciente</font></th>
			<th><font size="2">Nombre paciente</font></th>
			<th><font size="2">Patología</font></th>
			<th><font size="2">Gestora</font></th>
			<th><font size="2">N</font></th>			
		</tr>
	</thead>
	<tbody>
	<?php
		$n=1;
	while (!$qrDerivacion->EOF) {
		$codRutPac = $qrDerivacion->Fields('RUT_PACIENTE');
		$idDerivacion = $qrDerivacion->Fields('ID_DERIVACION');
		$estado = $qrDerivacion->Fields('ESTADO');

	?>
		<tr>
			<td>
				<div class="btn-group">
					<button type="button" class="btn btn-default"><i class="fas fa-cog"></i></button>
					<button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
					<div class="dropdown-menu" role="menu">
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacoraCRSS" onclick="fnfrmBitacoraCRSS('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Bitacora</a>
					</div>
				</div>
			</td>
			<td><span class="badge badge-warning"><font size="2"><?php echo $qrDerivacion->Fields('N_DERIVACION');?></font></span></td>
			
			<td><font size="2"><?php echo $qrDerivacion->Fields('FOLIO'); ?></font></td>
			<td><font size="2"><?php echo utf8_encode($qrDerivacion->Fields('ESTADO_RN')); ?></font></td>
			<td><font size="2"><?php echo date("Y-m-d",strtotime($qrDerivacion->Fields('FECHA_DERIVACION')))?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('DIAS_DESDE_CIRUGIA'); ?></font></td>
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
			<td><a href="#"> <font size="1"><b><?php echo utf8_encode(strtoupper($qrDerivacion->Fields('NOMBRE_PACIENTE'))); ?></b></font></a></td>
			<td><font size="2"><?php echo utf8_encode($qrDerivacion->Fields('DESC_PATOLOGIA')); ?></font></td>
			<td><font size="2"><?php echo utf8_encode($qrDerivacion->Fields('NOMBRE_PROFESIONAL')); ?></font></td>
			<td><font size="2"><?php echo $n; ?></font></td>			
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

	function fnfrmBitacoraCRSS(idDerivacion){
	    $('#dvCargaFrmBitacoraCRSS').load('vistas/modulos/segundoPrestador/clSantiago/bitacora/modals/frmBitacoraCrss.php?idDerivacion=' + idDerivacion);
	}
	
	$(function () {
	    $('#tPacientesOncoCrss').DataTable({
	      "paging": true,
	      "lengthChange": true,
	      "searching": true,
	      "ordering": true,
	      "info": true,
	      "autoWidth": true,
	      "responsive": true,
	      "order": [[ 4, 'desc' ]],
	      dom: 'lBfrtip',
		    buttons: [
		                {
		                    extend: 'excelHtml5',
		                    exportOptions: {
		                        columns: [ 2, 3, 4, 5, 6, 7, 8, 9, 10 ]
		                    }
		                },
		                {
		                    extend: 'pdfHtml5',
		                    exportOptions: {
		                        columns: [ 2, 3, 4, 5, 6, 7, 8, 9, 10 ]
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