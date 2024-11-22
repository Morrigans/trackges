<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_func = "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE PROFESION != 'Administrador'";
$func = $oirs->SelectLimit($query_func) or die($oirs->ErrorMsg());
$totalRows_func = $func->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
			<div class="card card-secondary">
        <div class="card-header">
          <h3 class="card-title">Tabla de profesiones</h3>
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
					<table id="tprofesion" class="table table-bordered table-striped table-hover table-sm">
						<thead class="table-dark">
							<tr align="center">
								<th><strong>Profesion</strong></th>
								<th><strong>Color</strong></th>
							</tr>
						</thead>
						<tbody>
							<?php
						while (!$func->EOF) {
				    	?>
							<tr>
								<td><?php echo $func->Fields('PROFESION'); ?></td>
								<td><small style="background-color:<?php echo $func->Fields('COLOR'); ?>;"><?php echo $func->Fields('COLOR'); ?></small></td>
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


	$(function () {
	    $('#tprofesion').DataTable({
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

	  });
</script>
<?php 
	$func->Close();
?>



