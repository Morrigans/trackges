<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_prestador = "SELECT * FROM $MM_oirs_DATABASE.prestador order by ID_PRESTADOR asc";
$prestador = $oirs->SelectLimit($query_prestador) or die($oirs->ErrorMsg());
$totalRows_prestador = $prestador->RecordCount();
?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Tabla detalle prestadores</h3>
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
				<table id="tblPrestadores" class="table table-bordered table-striped table-hover table-sm">
					<thead class="table-dark">
						<tr align="center">.
							<!-- <th>Rut prestador</th> -->
							<th>Nombre prestador</th>							
							<th><strong>Quitar</strong></th>
						</tr>
					</thead>
					<tbody>
						<?php
					while (!$prestador->EOF) { ?>
						<tr>
							<!-- <td><?php echo $prestador->Fields('RUT_PRESTADOR'); ?></td> -->
							<td><?php echo utf8_encode($prestador->Fields('DESC_PRESTADOR')); ?></td>
							<td><div align="center"><a href="#" class="btn btn-danger btn-sm" onclick="preguntarSiNoEliminaPac(<?php echo $prestador->Fields('ID_PRESTADOR'); ?>)">Quitar</a></div></td>
						</tr>
						<?php
			   	 	$prestador->MoveNext();
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
	    $('#tblPrestadores').DataTable({
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
			  	fnQuitarPrestador(id)
			    Swal.fire(
			      'Eliminado!',
			      'El prestador fue eliminado.',
			      'success'
			    )
			  }
			})
		}

	function	fnQuitarPrestador(id){
		cadena = "id=" + id;
	  $.ajax({
	      type: "POST",
	      url: "vistas/mantenedores/prestadores/eliminarPrestador.php",
	      data: cadena,
	      success: function(r) {
	          if (r == 1) {
	              $('#dvTablaPrestador').load('vistas/mantenedores/prestadores/tablaPrestadores.php');
	          } else {
	              // alertify.error("Fallo el servidor :(");
	          }
	      }
	  });

	}
</script>
<?php 
	$prestador->Close();
?>



