<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_canasta = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia";
$canasta = $oirs->SelectLimit($query_canasta) or die($oirs->ErrorMsg());
$totalRows_canasta = $canasta->RecordCount();
?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Tabla detalle canastas</h3>
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
				<table id="tblCanastas" class="table table-bordered table-striped table-hover table-sm">
					<thead class="table-dark">
						<tr align="center">
							<th>Código Canasta</th>
							<th>Canasta</th>							
							<th>Tiempo Límite</th>
							<th><strong>Quitar</strong></th>
						</tr>
					</thead>
					<tbody>
						<?php
					while (!$canasta->EOF) { ?>
						<tr>
							<td><?php echo $canasta->Fields('CODIGO_CANASTA_PATOLOGIA'); ?></td>
							<td><?php echo $canasta->Fields('DESC_CANASTA_PATOLOGIA'); ?></td>
							<td><?php echo $canasta->Fields('TIEMPO_LIMITE'); ?></td>
							<td><div align="center"><a href="#" class="btn btn-danger btn-sm" onclick="preguntarSiNoEliminaPac(<?php echo $canasta->Fields('ID_CANASTA_PATOLOGIA'); ?>)">Quitar</a></div></td>
						</tr>
						<?php
			   	 	$canasta->MoveNext();
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
	    $('#tblCanastas').DataTable({
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
			  	fnQuitarCanasta(id)
			    Swal.fire(
			      'Eliminado!',
			      'La canasta fue eliminada.',
			      'success'
			    )
			  }
			})
		}

	function fnQuitarCanasta(id){
		cadena = "id=" + id;
	  $.ajax({
	      type: "POST",
	      url: "vistas/mantenedores/canastasPatologias/eliminarCanasta.php",
	      data: cadena,
	      success: function(r) {
	          if (r == 1) {
	              $('#dvTablaCanastas').load('vistas/mantenedores/canastasPatologias/tablaCanastas.php');
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



