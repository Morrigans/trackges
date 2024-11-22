<?php
require_once '../../../../Connections/oirs.php';
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];




$estado = $_REQUEST['estado'];
$vencidas = $_REQUEST['vencidas'];
date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');



	$query_qrDerivacion = "
	SELECT 
		derivaciones_pp.N_DERIVACION,
		derivaciones_pp.ID_DERIVACION,
		derivaciones_pp.ID_DERIVACION_PP,
		pacientes.COD_RUTPAC AS RUT_PACIENTE,
		pacientes.NOMBRE AS NOMBRE_PACIENTE,
		login.NOMBRE AS NOMBRE_PROFESIONAL,
		login.USUARIO AS RUT_PROFESIONAL,
		prevision.PREVISION,
		derivaciones_pp.ESTADO,
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
	ESTADO='cerrada'"; 


	$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
	$totalRows_qrDerivacion = $qrDerivacion->RecordCount();





?>
<div class="table-responsive-sm ">
<table id="tblCasosCerradosPp" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Opciones</font></th>
			<th><font size="2">Derivación</font></th>
			<th><font size="2">Derivación ICRS</font></th>
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
						<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacoraPp" onclick="fnfrmBitacoraPp('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Bitacora</a> 

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
			<td><span class="badge badge-warning">
					<font size="2">
					<?php 
						
						echo 'D0'.$qrDerivacion->Fields('ID_DERIVACION_PP'); 
					?>
						
					</font>
				</span>
			</td>

			<td>

				<font size="2"><?php echo $qrDerivacion->Fields('ESTADO'); ?></font>
			</td>
		

			
			<td>

				<font size="2"><?php echo date("d-m-Y",strtotime($qrDerivacion->Fields('FECHA_DERIVACION'))); ?></font></td>
			<td>
				<font size="2">
					<?php
					if ($qrDerivacionCanasta->Fields('FECHA_LIMITE') == '0000-00-00' or $qrDerivacionCanasta->Fields('FECHA_LIMITE') == null or $qrDerivacion->Fields('DESC_TIPO_PATOLOGIA') == 'CAEC' or $totalRows_qrDerivacionCanasta == 0) {
						echo 'Sin Limite';
					}else{
						echo date("d-m-Y",strtotime($qrDerivacionCanasta->Fields('FECHA_LIMITE')));
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

								$query_qrCanastaPatologia= "SELECT DESC_CANASTA_PATOLOGIA FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanastaPatologia'";
								$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
								$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

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




function fnfrmBitacoraPp(idDerivacion){
    $('#dvfrmBitacoraPp').load('vistas/modulos/pacientesPp/bitacora/modals/frmBitacora.php?idDerivacion=' + idDerivacion);
}

	
	
	$(function () {
	    $('#tblCasosCerradosPp').DataTable({
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