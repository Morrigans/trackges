<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';
//require_once('vistas/mantenedores/pacientes/modalInfoPaciente.php');

$idHospital = $_REQUEST['idHospital'];

$query_func = "SELECT * FROM $MM_oirs_DATABASE.hospitales_contactos where ID_HOSPITAL='$idHospital'";
$func = $oirs->SelectLimit($query_func) or die($oirs->ErrorMsg());
$totalRows_func = $func->RecordCount();
?>

<!DOCTYPE html>
<html>
	<body>

        <div  class="card card-secondary ">
              <div class="card-header">
                <h3 class="card-title">Contactos</h3>
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
   
         <div class="container" >
         	  <div class="row" >
				<table id="tbHospitales" class="table table-bordered table-striped table-hover table-sm">
					<thead class="table-dark">
						<tr align="center">

		
							<th width="10">Nombre</th>					
							<th width="10">Email</th>					
							<th width="10">Telefono</th>					
							<th width="10">Cargo/Unidad</th>					
							<th width="10" >Obs</th>
						<!-- 	<th width="10">Quitar</th>  --> 

							
						</tr>
					</thead>
					<tbody>
						<?php
					while (!$func->EOF) {

				
			    	?>			    	
						<tr>
						<?php  $idHospital=$func->Fields('ID_HOSPITAL'); ?> 
		 				<?php   $idHospitalContacto=$func->Fields('ID_HOSPITAL_CONTACTO'); ?>	
							<td><?php echo utf8_encode($func->Fields('NOMBRE')) ?></td>
							<td><?php echo utf8_encode($func->Fields('EMAIL')) ?></td>
							<td><?php echo utf8_encode($func->Fields('TELEFONO')) ?></td>
							<td><?php echo utf8_encode($func->Fields('CARGO_UNIDAD')) ?></td>
							<td><?php echo utf8_encode($func->Fields('OBSERVACION')) ?></td>													
					<!-- 		<td><div align="center"><a href="#" class="btn btn-danger btn-sm" onclick="preguntarSiNoEliminaPac('<?php echo $idHospitalContacto ?>','<?php echo $idHospital ?>')">Quitar</a></div></td> -->
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
     </div>
	</body>
</html>


<?php 
	$func->Close();
?>

<!-- 
<script type="text/javascript">

		function preguntarSiNoEliminaPac(idHospitalContacto,idHospital) {


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
			  	
		cadena = 'idHospitalContacto=' + idHospitalContacto;
	  $.ajax({
	      type: "POST",
	      url: "vistas/mantenedores/contactosHospital/eliminarContacto.php",
	      data: cadena,
	      success: function(r) {
	     $('#tablaContactos').load('vistas/mantenedores/contactosHospital/tablaContactos.php?idHospital='+idHospital);
			
	      }
	  });
			    Swal.fire(
			      'Eliminado!',
			      'Tu archivo fue eliminado.',
			      'success'
			    )
			  }
			})
		}



</script> -->