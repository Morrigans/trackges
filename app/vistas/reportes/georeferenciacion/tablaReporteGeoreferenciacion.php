<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$fecha1Geo = date("Y-m-d",strtotime($_REQUEST['fecha1Geo']));
$fecha2Geo = date("Y-m-d",strtotime($_REQUEST['fecha2Geo']));


$query_func = "SELECT * FROM $MM_oirs_DATABASE.events WHERE ESTADO_CITA = 'ATENDIDO' AND start >= '$fecha1Geo' AND start <= '$fecha2Geo'";
$func = $oirs->SelectLimit($query_func) or die($oirs->ErrorMsg());
$totalRows_func = $func->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
			<div class="card card-secondary">
        <div class="card-header">
          <h3 class="card-title">Tabla de Georeferenciaciones</h3>
          <div class="card-tools">
            <button type="button" class="btn bg-secondary btn-sm" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn bg-secondary btn-sm" data-card-widget="remove">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="card-body">

	        <div class="table-responsive-sm">
					<table id="tprofesion" class="table table-bordered table-striped table-hover table-sm">
						<thead class="table-dark">
							<tr align="center">
								<th><strong>Profesion</strong></th>
								<th><strong>Profesional</strong></th>
								<th><strong>Paciente</strong></th>
								<th><strong>Fecha Atención</strong></th>
								<th><strong>Hora Llegada</strong></th>
								<th><strong>Hora Salida</strong></th>
								<th><strong>Costo Visita</strong></th>
								<th><strong>Precio Visita</strong></th>
								<th><strong>Maps</strong></th>
							</tr>
						</thead>
						<tbody>
							<?php
						while (!$func->EOF) {
							$idProfesion = $func->Fields('ESPECIALIDAD');

							$query_qrProfesion= "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '$idProfesion'";
							$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
							$totalRows_qrProfesion = $qrProfesion->RecordCount();

							$codRutPro = $func->Fields('cod_rutpro');

							$query_qrLogin = "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$codRutPro'";
							$qrLogin = $oirs->SelectLimit($query_qrLogin) or die($oirs->ErrorMsg());
							$totalRows_qrLogin = $qrLogin->RecordCount();

							$codPrestacion = $func->Fields('COD_PRESTACION');

							$query_qrPrestacion = "SELECT * FROM $MM_oirs_DATABASE.prestacion WHERE COD_PRESTACION = '$codPrestacion'";
							$qrPrestacion = $oirs->SelectLimit($query_qrPrestacion) or die($oirs->ErrorMsg());
							$totalRows_qrPrestacion = $qrPrestacion->RecordCount();

				    	?>
							<tr>
								<td><small style="background-color:<?php echo $qrProfesion->Fields('COLOR'); ?>;"><?php echo utf8_encode($qrProfesion->Fields('PROFESION')); ?></small></td>
								<td><?php echo $func->Fields('cod_rutpro'); ?> <br><?php echo utf8_encode($qrLogin->Fields('NOMBRE')); ?></td>
								<td><?php echo $func->Fields('cod_rutpac'); ?> <br><?php echo utf8_encode($func->Fields('nombre')); ?></td>
								<td><?php echo date("d-m-Y",strtotime($func->Fields('start'))); ?></td>
								<td><?php echo $func->Fields('HORA_UBICACION'); ?></td>
								<td><?php echo $func->Fields('HORA_SALIDA_DOMICILIO'); ?></td>
								<td><?php echo $func->Fields('COSTO_PRESTACION'); ?></td>
								<td><?php echo $qrPrestacion->Fields('PRECIO_PRESTACION'); ?></td>
								<td><div align="center"><a class="btn btn-sm btn-primary" target="_blank" href="<?php echo $func->Fields('LINK_UBICACION'); ?>">Mapa</a></div></td>
							</tr>
							<?php
				   	 	$func->MoveNext();
						}
						?>
						</tbody>
					</table>
					</div>
	  		</div>
      </div>
	</body>
</html>

<script>


	$(function () {
	    $('#tprofesion').DataTable({
	      "paging": true,
	      "lengthChange": false,
	      "searching": true,
	      "ordering": true,
	      "info": true,
	      "autoWidth": true,
	      "responsive": true,
	      "order": [[ 0, 'asc' ]],
	      dom: 'lBfrtip',
		    buttons: [ 'copy', 'excel', 'csv' ],
		    "language": {
		        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
		        }
	    });

	  });
</script>
<?php 
	$func->Close();
?>



