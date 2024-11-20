<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_patologia = "SELECT * FROM $MM_oirs_DATABASE.patologia order by ID_PATOLOGIA asc";
$patologia = $oirs->SelectLimit($query_patologia) or die($oirs->ErrorMsg());
$totalRows_patologia = $patologia->RecordCount();
?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Tabla detalle patologías</h3>
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
							<th>Descripción Patología</th>
							<th>Código Patología</th>
							<th>Días Vigencia</th>
							<th><strong>Quitar</strong></th>
						</tr>
					</thead>
					<tbody>
						<?php
					while (!$patologia->EOF) { ?>
						<tr>
							<td><?php echo utf8_encode($patologia->Fields('DESC_PATOLOGIA')); ?></td>
							<td><?php echo $patologia->Fields('CODIGO_PATOLOGIA'); ?></td>
							<td><?php echo $patologia->Fields('DIAS_VIGENCIA'); ?></td>
							<td>
								<div align="center">
									<a href="#" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEditarPatologia" onclick="fnCargaFrmEditaPatologia(<?php echo $patologia->Fields('ID_PATOLOGIA'); ?>)">Editar</a>
									<a href="#" class="btn btn-danger btn-sm" onclick="preguntarSiNoEliminaPac(<?php echo $patologia->Fields('ID_PATOLOGIA'); ?>)">Quitar</a>
								</div>
							</td>
						</tr>
						<?php
			   	 	$patologia->MoveNext();
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
		  	fnQuitarPatologia(id)
		    Swal.fire(
		      'Eliminado!',
		      'La patología fue eliminada.',
		      'success'
		    )
		  }
		})
	}

	function	fnQuitarPatologia(id){
		cadena = "id=" + id;
	  $.ajax({
	      type: "POST",
	      url: "vistas/mantenedores/patologias/eliminarPatologia.php",
	      data: cadena,
	      success: function(r) {
	          if (r == 1) {
	              $('#dvTablaPatologias').load('vistas/mantenedores/patologias/tablaPatologias.php');
	          } else {
	              // alertify.error("Fallo el servidor :(");
	          }
	      }
	  });
	}

	function	fnCargaFrmEditaPatologia(idPatologia){

		$('#dvCargaFrmEditarPatologia').load('vistas/mantenedores/patologias/frmEditarPatologia.php?idPatologia='+idPatologia);
	}



</script>
<?php 
	$func->Close();
?>



