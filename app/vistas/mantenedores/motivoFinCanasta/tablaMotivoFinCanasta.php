<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_creaMotivo = "SELECT * FROM $MM_oirs_DATABASE.motivos_fin_canastas order by ID_MOTIVO asc";
$creaMotivo = $oirs->SelectLimit($query_creaMotivo) or die($oirs->ErrorMsg());
$totalRows_creaMotivo = $creaMotivo->RecordCount();
?>

<!DOCTYPE html>
<html>
	<body>

        <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Tabla motivos</h3>
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
				<table id="tblPatologias" class="table table-bordered table-striped table-hover table-sm">
					<thead class="table-dark">
						<tr align="center">
							<!-- <th>Tipo motivo </th> -->
							<th>Descripcion</th>
							<th><strong>Quitar</strong></th>
						</tr>
					</thead>
					<tbody>
						<?php
					while (!$creaMotivo->EOF) { ?>
						<tr>
						
							<!-- <td><?php echo $creaMotivo->Fields('TIPO_MOTIVO'); ?></td> -->
							<td><?php echo $creaMotivo->Fields('DESC_MOTIVO'); ?></td>
							<td><div align="center"><a href="#" class="btn btn-danger btn-sm" onclick="preguntarSiNoEliminaPac(<?php echo $creaMotivo->Fields('ID_MOTIVO'); ?>)">Quitar</a></div></td>
						</tr>
						<?php
			   	 	$creaMotivo->MoveNext();
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
	    $('#tblPatologias').DataTable({
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
			  	fnQuitarMotivo(id)
			    Swal.fire(
			      'Eliminado!',
			      'el motivo fue eliminado.',
			      'success'
			    )
			  }
			})
		}

	function	fnQuitarMotivo(id){
		cadena = "id=" + id;
	  $.ajax({
	      type: "POST",
	      url: "vistas/mantenedores/motivoFinCanasta/eliminarMotivoFinCanasta.php",
	      data: cadena,
	      success: function(r) {
	          if (r == 1) {
	             	$('#contenido_principal').load('vistas/mantenedores/motivoFinCanasta/frmMotivoFinCanasta.php'); 
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



