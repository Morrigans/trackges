<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_func = "SELECT * FROM $MM_oirs_DATABASE.prestaciones";
$func = $oirs->SelectLimit($query_func) or die($oirs->ErrorMsg());
$totalRows_func = $func->RecordCount();
?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Detalle prestaciones</h3>
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
				<table id="tblPrestaciones" class="table table-bordered table-striped table-hover table-sm">
					<thead class="table-dark">
						<tr align="center">
							<th>Prestación</th>
							<th>Tiempo límite</th>
							<th><strong>Quitar</strong></th>
						</tr>
					</thead>
					<tbody>
						<?php
					while (!$func->EOF) {

				
			    	?>
						<tr>
							<td><span class="badge badge-pill badge-secondary"><?php echo $func->Fields('CODIGO_PRESTACION'); ?></span> | <?php echo $func->Fields('PRESTACION'); ?></td>
							<td><?php echo $func->Fields('TIEMPO_LIMITE'); ?></td>
							<td><div align="center"><a href="#" class="btn btn-danger btn-sm" onclick="preguntarSiNoEliminaPrestacion(<?php echo $func->Fields('ID_PRESTACION'); ?>)">Quitar</a></div></td>
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
	    $('#tblPrestaciones').DataTable({
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
	    // table.buttons().container()
     //    .appendTo( '#tblPatologias_wrapper .col-md-6:eq(0)' );

	  });
	
		function preguntarSiNoEliminaPrestacion(idPrestacion) {

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
			  	fnEliminaPrestacion(idPrestacion)
			    Swal.fire(
			      'Eliminado!',
			      'Convenio eliminado.',
			      'success'
			    )
			  }
			})
		}

	function	fnEliminaPrestacion(idPrestacion){
		cadena = "idPrestacion=" + idPrestacion;
	  $.ajax({
	      type: "POST",
	      url: "vistas/mantenedores/prestacion/eliminarPrestacion.php",
	      data: cadena,
	      success: function(r) {
	          if (r == 1) {
	              $('#dvCargatblPrestacion').load('vistas/mantenedores/prestacion/tablaPrestacion.php');
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



