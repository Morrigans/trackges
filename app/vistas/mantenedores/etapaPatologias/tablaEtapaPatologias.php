<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_func = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia order by ID_ETAPA_PATOLOGIA desc";
$func = $oirs->SelectLimit($query_func) or die($oirs->ErrorMsg());
$totalRows_func = $func->RecordCount();
?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Tabla Etapa patologias</h3>
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
				<table id="tablaEtapaPatologia" class="table table-bordered table-striped table-hover table-sm">
					<thead class="table-dark">
						<tr align="center">
							<!-- <th>iD</th> -->
							<th>Descripción etapa patología</th>
							<th>Código Etapa </th>
							<th>Descripción patología</th>
							<th><strong>Quitar</strong></th>
						</tr>
					</thead>
					<tbody>
						<?php
					while (!$func->EOF) {

				
			    	?>
						<tr>
						<!-- 	<td><?php echo $func->Fields('ID_ETAPA_PATOLOGIA'); ?></td> -->
							<td><?php echo $func->Fields('DESC_ETAPA_PATOLOGIA'); ?></td>
							<td><?php echo $func->Fields('CODIGO_ETAPA_PATOLOGIA'); ?></td>
							<td>
								<?php 
									 	$codPatologia=$func->Fields('CODIGO_PATOLOGIA');
									 	$query_NomPatologia = "SELECT * FROM $MM_oirs_DATABASE.patologia where ID_PATOLOGIA='$codPatologia'";
										$NomPatologia = $oirs->SelectLimit($query_NomPatologia) or die($oirs->ErrorMsg());
										$totalRows_NomPatologia = $NomPatologia->RecordCount();
									 echo $NomPatologia->Fields('DESC_PATOLOGIA');


								  ?>
							</td>
							<td><div align="center"><a href="#" class="btn btn-danger btn-sm" onclick="preguntarSiNoEliminaPac(<?php echo $func->Fields('ID_ETAPA_PATOLOGIA'); ?>)">Quitar</a></div></td>
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
	    $('#tablaEtapaPatologia').DataTable({
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
	      url: "vistas/mantenedores/etapaPatologias/eliminarEtapaPatologia.php",
	      data: cadena,
	      success: function(r) {
	          if (r == 1) {
	              $('#tablaEtapaPatologias').load('vistas/mantenedores/etapaPatologias/tablaEtapaPatologias.php');
	          } else {
	              // alertify.error("Fallo el servidor :(");
	          }
	      }
	  });

	}
</script>
<?php 
	$func->Close();
?>



