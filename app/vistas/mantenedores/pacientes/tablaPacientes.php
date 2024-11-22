<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';
//require_once('vistas/mantenedores/pacientes/modalInfoPaciente.php');

$query_func = "SELECT * FROM $MM_oirs_DATABASE.pacientes order by NOMBRE asc";
$func = $oirs->SelectLimit($query_func) or die($oirs->ErrorMsg());
$totalRows_func = $func->RecordCount();
?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Tabla de pacientes</h3>
                <div class="card-tools">
                  <button type="button" class="btn bg-secondary btn-sm" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn bg-secondary btn-sm" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">    
				<table id="tPacientes" class="table table-bordered table-striped table-hover table-sm">
					<thead class="table-dark">
						<tr align="center">
							<th>Rut</th>
							<th>Nombre</th>
							<th>Fecha Nacimiento</th>
							<th>Télefono</th>
							<th>Correo Electrónico</th>
							<th>Comuna</th>
							<th>Dirección</th>
							<th>Ocupación</th>
							<th>Previsión</th>
							<th>Plan Salud</th>
							<th>Seguro Compl.</th>
							<th>Compañía Seguro</th>
							<th><strong>Quitar</strong></th>
						</tr>
					</thead>
					<tbody>
						<?php
					while (!$func->EOF) {

						$idComuna = $func->Fields('COMUNA');
						$rutPaciente = $func->Fields('COD_RUTPAC');

						$query_VerComuna = "SELECT * FROM $MM_oirs_DATABASE.comunas WHERE comuna_id = '$idComuna'";
						$VerComuna = $oirs->SelectLimit($query_VerComuna) or die($oirs->ErrorMsg());
						$totalRows_VerComuna = $VerComuna->RecordCount();
						$comuna = $VerComuna->Fields('comuna_nombre');

						$idPrevision = $func->Fields('PREVISION');

						$query_previ = "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$idPrevision'";
						$previ = $oirs->SelectLimit($query_previ) or die($oirs->ErrorMsg());
						$totalRows_previ = $previ->RecordCount();
						$prevision = $previ->Fields('PREVISION');
			    	?>			    	
						<tr>
							<td><?php echo $func->Fields('COD_RUTPAC'); ?></td>
							<td><!-- <?php echo $func->Fields('NOMBRE') ?>
							<button type="button" class="btn" data-toggle="modal" data-target="#modalInfoPaciente" onclick="fnLevantaModalInfoPaciente(<?php echo $rutPaciente ?>)"><i class="fas fa-info-circle"></i></button> -->
								<a href="#" onclick="fnLevantaModalInfoPaciente('<?php echo $rutPaciente ?>')"><?php echo $func->Fields('NOMBRE') ?></a>	
							</td>
							<td><?php echo date("d-m-Y", strtotime($func->Fields('FEC_NACIMI'))); ?></td>
							<td><?php echo $func->Fields('FONO') ?></td>
							<td><?php echo $func->Fields('MAIL') ?></td>
							<td><?php echo $comuna ?></td>
							<td><?php echo $func->Fields('DIRECCION') ?></td>
							<td><?php echo $func->Fields('OCUPACION') ?></td>
							<td><?php echo $prevision ?></td>
							<td><?php echo $func->Fields('PLAN_SALUD') ?></td>
							<td><?php echo $func->Fields('SEGURO_COMPLEMENTARIO') ?></td>
							<td><?php echo $func->Fields('COMPANIA_SEGURO') ?></td>
							<td><div align="center"><a href="#" class="btn btn-danger btn-sm" onclick="preguntarSiNoEliminaPac(<?php echo $func->Fields('ID'); ?>)">Quitar</a></div></td>
						</tr>
						<?php
			   	 	$func->MoveNext();
					}
					?>
					</tbody>
				</table>
			 </div>
           </div>
	</body>
</html>

<script>

	$(function () {
	    $('#tPacientes').DataTable({
	      "paging": true,
	      "lengthChange": false,
	      "searching": true,
	      "ordering": true,
	      "info": true,
	      "autoWidth": true,
	      "responsive": true,
	      "order": [[ 1, 'asc' ]],
	      dom: 'lBfrtip',
		    buttons: [ 'copy', 'excel', 'csv' ],
		    "language": {
		        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
		        }
	    });
	  });
	
		function preguntarSiNoEliminaPac(id) {
				Swal.fire({
			  title: 'Estas Seguro?',
			  text: "No podras revertir la eliminación!",
			  icon: 'warning',
			  showCancelButton: true,
			  confirmButtonColor: '#3085d6',
			  cancelButtonColor: '#d33',
			  confirmButtonText: 'Si, Eliminar!'
			}).then((result) => {
			  if (result.isConfirmed) {
			  	fnQuitarProfesionalPac(id)
			    Swal.fire(
			      'Eliminado!',
			      'Tu archivo fue eliminado.',
			      'success'
			    )
			  }
			})
		}

	function	fnQuitarProfesionalPac(id){
		cadena = "id=" + id;
	  $.ajax({
	      type: "POST",
	      url: "vistas/mantenedores/pacientes/eliminarDatosPac.php",
	      data: cadena,
	      success: function(r) {
	          if (r == 1) {
	              $('#tablaPacientes').load('vistas/mantenedores/pacientes/tablaPacientes.php');
	          } else {
	              // alertify.error("Fallo el servidor :(");
	          }
	      }
	  });

	}

	function fnLevantaModalInfoPaciente(rutPaciente){

		//var rutPaciente= $("#rutPac").val();
		$("#modalInfoPaciente").show();
	   	$('#dvCargaInfoPaciente').load('vistas/mantenedores/pacientes/frmInfoPaciente.php?rutPaciente=' + rutPaciente);
	}

</script>
<?php 
	$func->Close();
?>



