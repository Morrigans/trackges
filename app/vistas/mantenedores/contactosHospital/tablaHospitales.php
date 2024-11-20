<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';
//require_once('vistas/mantenedores/pacientes/modalInfoPaciente.php');

$query_func = "SELECT * FROM $MM_oirs_DATABASE.hospitales order by region_id asc";
$func = $oirs->SelectLimit($query_func) or die($oirs->ErrorMsg());
$totalRows_func = $func->RecordCount();



?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Hospitales Nacionales</h3>
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
				<table id="tbHospitalesNacional" class="table table-bordered table-striped table-hover table-sm">
					<thead class="table-dark">
						<tr align="center">
				
							<th>Hospital</th>
							<th>Agregar Contacto</th>
							

							
						</tr>
					</thead>
					<tbody>
						<?php

					while (!$func->EOF) {		
				 $idHospital=$func->Fields('ID_HOSPITAL') 
			    	?>			    	
						<tr>			
					
<!-- 
							$query_numeroContactos = "SELECT * FROM $MM_oirs_DATABASE.hospitales_contactos where ID_HOSPITAL='$idHospital'";
								$numeroContactos = $oirs->SelectLimit($query_numeroContactos) or die($oirs->ErrorMsg());
								$totalRows_numeroContactos = $numeroContactos->RecordCount(); -->
						

						</td>
							<td><?php echo utf8_encode($func->Fields('HOSPITAL')) ?></td>
							<td><div align="center"><a href="#" class="btn btn-info btn-sm"  data-toggle="modal" data-target="#modalContactosHospital" onclick="fncargafrmContactoHospital(<?php echo $idHospital ?>)">Agregar/Ver</a></div></td>
							
						<!-- 	<td><div align="center"><a href="#" class="btn btn-success btn-sm"  data-toggle="modal" data-target="#modalVerContactos" onclick="fnVerContactos(<?php echo $idHospital ?>)">  Ver</a></div></td> -->
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


<?php 
	$func->Close();
?>



<script type="text/javascript">


 

 function fnVerContactos(idHospital) {
 $('#tablaContactos').load('vistas/mantenedores/contactosHospital/tablaVerContactos.php?idHospital='+idHospital);
    }

  function fncargafrmContactoHospital(idHospital) {
 $('#frmContactosHospital').load('vistas/mantenedores/contactosHospital/frmContactosHospital.php?idHospital='+idHospital);
  }

    function fnCerrar(){
  $('#contenido_principal').load('vistas/mantenedores/contactosHospital/tablaHospitales.php');
    }




		$(function () {
	    $('#tbHospitalesNacional').DataTable({
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
</script>