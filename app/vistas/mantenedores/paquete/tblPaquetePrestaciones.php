<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';
require_once '../../../vistas/mantenedores/paquete/modalAsociaPrestaciones.php';

$idPaquete=$_REQUEST['idPaquete'];

$query_func = "
		SELECT 
		    pqp.ID_PAQUETE_PRESTACION,
		    pq.DESC_PAQUETE,
		    pr.PRESTACION
		FROM 
		    paquetes_prestaciones pqp
		LEFT JOIN 
		    paquetes pq ON pqp.ID_PAQUETE = pq.ID_PAQUETE
		LEFT JOIN 
		    prestaciones pr ON pqp.ID_PRESTACION = pr.ID_PRESTACION
		WHERE 
		    pqp.ID_PAQUETE = '$idPaquete' 
		";
$func = $oirs->SelectLimit($query_func) or die($oirs->ErrorMsg());
$totalRows_func = $func->RecordCount();
?>

<!DOCTYPE html>
<html>
	<body>
      <div class="card card-secondary">
        <div class="card-header">
          <h3 class="card-title">Detalle paquete: <KBD><?php echo utf8_encode($func->Fields("DESC_PAQUETE")); ?></KBD></h3>
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
				<table id="tblPaquetesPrestaciones" class="table table-bordered table-striped table-hover table-sm">
					<thead class="table-dark">
						<tr align="center">
							<th>Prestación</th>
							<th><strong>Quitar</strong></th>
						</tr>
					</thead>
					<tbody>
						<?php
					while (!$func->EOF) {
						$prestacion = utf8_encode($func->Fields('PRESTACION'));
				
			    	?>
						<tr>
							
							<td><?php echo $prestacion; ?></td>
							<td>
								<div align="center" class="row">
								<a href="#" class="btn btn-danger btn-sm" onclick="preguntarSiNoEliminaPaquetePrestacion('<?php echo $func->Fields('ID_PAQUETE_PRESTACION'); ?>', '<?php echo $idPaquete; ?>')">Quitar</a>
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
	    $('#tblPaquetesPrestaciones').DataTable({
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
	
		function preguntarSiNoEliminaPaquetePrestacion(idPaquetePrestacion, idPaquete) {

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
			  	fnQuitarPaquetePrestacion(idPaquetePrestacion, idPaquete)
			    Swal.fire(
			      'Eliminada!',
			      'Relacion eliminada.',
			      'success'
			    )
			  }
			})
		}

	function	fnQuitarPaquetePrestacion(idPaquetePrestacion, idPaquete){
		cadena = "idPaquetePrestacion=" + idPaquetePrestacion;
	  $.ajax({
	      type: "POST",
	      url: "vistas/mantenedores/paquete/eliminarPaquetePrestacion.php",
	      data: cadena,
	      success: function(r) {
	          if (r == 1) {
	              $('#dvMuestraTblPaquetePrestaciones').load('vistas/mantenedores/paquete/tblPaquetePrestaciones.php?idPaquete='+idPaquete);
	          } else {
	              // alertify.error("Fallo el servidor :(");
	          }
	      }
	  });

	}

	// function	fnCargaFrmAsociaPrestaciones(idPaquete){

	// 	$('#dvCargaFrmAsociaPrestacion').load('vistas/mantenedores/paquete/frmAsociaPrestaciones.php?idPaquete='+idPaquete);
	// }
</script>
<?php 
	$func->Close();
?>



