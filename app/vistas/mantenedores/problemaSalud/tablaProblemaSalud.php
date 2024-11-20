<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_patologia = "SELECT * FROM $MM_oirs_DATABASE.2_problemas_salud order by PROBLEMA_SALUD asc";
$patologia = $oirs->SelectLimit($query_patologia) or die($oirs->ErrorMsg());
$totalRows_patologia = $patologia->RecordCount();
?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Tabla detalle problemas de salud</h3>
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
				<table id="tblProblemaSalud" class="table table-bordered table-striped table-hover table-sm">
					<thead class="table-dark">
						<tr align="center">
							<th>código</th>
							<th>Problema salud</th>
						</tr>
					</thead>
					<tbody>
						<?php
					while (!$patologia->EOF) { ?>
						<tr>
							<td><?php echo utf8_encode($patologia->Fields('ID_PROBLEMA_SALUD')); ?></td>
							<td><?php echo utf8_encode($patologia->Fields('PROBLEMA_SALUD')); ?></td>
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
	    $('#tblProblemaSalud').DataTable({
	      "paging": true,
	      "lengthChange": false,
	      "searching": true,
	      "ordering": true,
	      "info": true,
	      "autoWidth": true,
	      "responsive": true,
	      "order": [[ 0, 'desc' ]],
	      dom: 'lBfrtip',
		    buttons: [ 'excel' ],
		    "language": {
		        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
		        }
	    });
	  });

</script>
<?php 
	$patologia->Close();
?>



