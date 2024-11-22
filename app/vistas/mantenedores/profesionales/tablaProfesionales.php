<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_func = "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO <> '0'";
$func = $oirs->SelectLimit($query_func) or die($oirs->ErrorMsg());
$totalRows_func = $func->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
			<div class="card card-secondary">
        <div class="card-header">
          <h3 class="card-title">Tabla de profesionales</h3>
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
					<table id="tprofesionales" class="table table-bordered table-striped table-hover table-sm">
						<thead class="table-dark">
							<tr align="center">
								<th><strong>Rut</strong></th>
								<th><strong>Nombre</strong></th>
								<th><strong>Telefono</strong></th>
								<th><strong>Correo electrónico</strong></th>
								<th><strong>Restablecer contraseña</strong></th>
								<th><strong>Quitar</strong></th>
							</tr>
						</thead>
						<tbody>
							<?php
						while (!$func->EOF) {
							$idProfesion = $func->Fields('TIPO');

							$query_qrProfesion= "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '$idProfesion'";
							$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
							$totalRows_qrProfesion = $qrProfesion->RecordCount();
				    	?>
							<tr>
								<td><?php echo $func->Fields('USUARIO'); ?></td>
								<td>
									<?php echo $func->Fields('NOMBRE'); ?>
									<br>
									<small style="background-color:<?php echo $qrProfesion->Fields('COLOR'); ?>;"> <?php echo $qrProfesion->Fields('PROFESION'); ?></i></small>
								</td>
								<td><?php echo $func->Fields('FONO'); ?></td>
								<td><?php echo $func->Fields('MAIL'); ?></td>
								<td><div align="center"><a href="#" class="btn btn-success btn-sm" onclick="preguntarSiNoResetPass(<?php echo $func->Fields('ID'); ?>)">Restablecer</a></div></td>
								<td><div align="center"><a href="#" class="btn btn-danger btn-sm" onclick="preguntarSiNoEliminaProf(<?php echo $func->Fields('ID'); ?>)">Quitar</a></div></td>
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

function resetPw(id){
		cadena = 'id=' + id;
			$.ajax({
				type:"post",
				data:cadena,
				url:'php/resetPw.php',
				success:function(r){
					if (r == 1) {
						$('#tablaProfesionales').load('componentes/tablaProfesionales.php');  
						swal("Genial!", "la contraseña fue reestablecida", "success");
		            } else {
		            }
					
				}
			});
	}


	
	function fnModalModificaProfesional(id){
	$('#dvModificaProfesional').load('vistas/frmModificaProfesional.php?id='+id);
}

	$(function () {
	    $('#tprofesionales').DataTable({
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

	function preguntarSiNoEliminaProf(id) {
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
		  	fnQuitarProfesionalProfesional(id)
		    Swal.fire(
		      'Eliminado!',
		      'Tu archivo fue eliminado.',
		      'success'
		    )
		  }
		})
	}

function	fnQuitarProfesionalProfesional(id){
	cadena = "id=" + id;
  $.ajax({
      type: "POST",
      url: "vistas/mantenedores/profesionales/eliminarDatosProf.php",
      data: cadena,
      success: function(r) {
          if (r == 1) {
              $('#tablaProfesionales').load('vistas/mantenedores/profesionales/tablaProfesionales.php');
          } else {
          }
      }
  });

}






function preguntarSiNoResetPass(id) {
			Swal.fire({
		  title: '¿Estas Seguro restablecer la contraseña ?',
		  text: "No podras revertir este proceso!",
		  icon: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Si, restablecer!'
		}).then((result) => {
		  if (result.isConfirmed) {
		  	fnResetPass(id);
		    Swal.fire(
		      '¡restablecida!',
		      'Nueva contraseña, trackGes',
		      'success'
		    )
		  }
		})
	}

function	fnResetPass(id){
	cadena = "id=" + id;
  $.ajax({
      type: "POST",
      url: "vistas/mantenedores/profesionales/resetearContrasenaProf.php",
      data: cadena,
      success: function(r) {
          if (r == 1) {
              $('#tablaProfesionales').load('vistas/mantenedores/profesionales/tablaProfesionales.php');
          } else {
          }
      }
  });

}




</script>
<?php 
	$func->Close();
?>



