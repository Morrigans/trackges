<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';
require_once '../../../vistas/mantenedores/paquete/modalAsociaPrestaciones.php';


$query_func = "SELECT * FROM $MM_oirs_DATABASE.paquetes";
$func = $oirs->SelectLimit($query_func) or die($oirs->ErrorMsg());
$totalRows_func = $func->RecordCount();
?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Detalle paquetes</h3>
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
				<table id="tblPaquetes" class="table table-bordered table-striped table-hover table-sm">
					<thead class="table-dark">
						<tr align="center">
							
							<th>Nombre paquete</th>
							<th>Canasta</th>
							<th><strong>Quitar</strong></th>
						</tr>
					</thead>
					<tbody>
						<?php
					while (!$func->EOF) {
						$idCanasta = $func->Fields('ID_CANASTA');

						$query_qrCanasta = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE ID_CANASTA_PATOLOGIA = '$idCanasta'";
						$qrCanasta = $oirs->SelectLimit($query_qrCanasta) or die($oirs->ErrorMsg());
						$totalRows_qrCanasta = $qrCanasta->RecordCount();
				
			    	?>
						<tr>
							
							<td><?php echo $func->Fields('DESC_PAQUETE'); ?></td>
							<td><?php echo $qrCanasta->Fields('DESC_CANASTA_PATOLOGIA'); ?></td>
							<td>
								<div align="center" class="row">
									<a href="#" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalAsociaPrestaciones" onclick="fnCargaFrmAsociaPrestaciones(<?php echo $func->Fields('ID_PAQUETE'); ?>)">Prestaciones</a>
								<a href="#" class="btn btn-danger btn-sm" onclick="preguntarSiNoEliminaPaq(<?php echo $func->Fields('ID_PAQUETE'); ?>)">Quitar</a>
							</div>
							</td>
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
	    $('#tblPaquetes').DataTable({
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
	
		function preguntarSiNoEliminaPaq(id) {

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
			  	fnQuitarPaquete(id)
			    Swal.fire(
			      'Eliminado!',
			      'Paquete eliminado.',
			      'success'
			    )
			  }
			})
		}

	function	fnQuitarPaquete(id){
		cadena = "id=" + id;
	  $.ajax({
	      type: "POST",
	      url: "vistas/mantenedores/paquete/eliminarPaquete.php",
	      data: cadena,
	      success: function(r) {
	          if (r == 1) {
	              $('#tablaPaquete').load('vistas/mantenedores/paquete/tablaPaquete.php');
	          } else {
	              // alertify.error("Fallo el servidor :(");
	          }
	      }
	  });

	}

	function	fnCargaFrmAsociaPrestaciones(idPaquete){

		$('#dvCargaFrmAsociaPrestacion').load('vistas/mantenedores/paquete/frmAsociaPrestaciones.php?idPaquete='+idPaquete);
	}
</script>
<?php 
	$func->Close();
?>



