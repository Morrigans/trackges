<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_patologia = "SELECT * FROM $MM_oirs_DATABASE.2_grd_referencia order by ID desc";
$patologia = $oirs->SelectLimit($query_patologia) or die($oirs->ErrorMsg());
$totalRows_patologia = $patologia->RecordCount();
?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Tabla detalle Grd Referencia</h3>
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
				<table id="tblGrdReferencia" class="table table-bordered table-striped table-hover table-sm">
					<thead class="table-dark">
						<tr align="center">
							<th>código</th>
							<th>Descripción Patología</th>
							<th>avg estancia</th>
							<th>limite inferior peso grd</th>
							<th>avg peso grd</th>
							<th>avg monto grd total</th>
							<th>peso grd real</th>
							<th>monto grd total real</th>
							<th>monto x linea</th>
							<th>diferencia montos</th>
						</tr>
					</thead>
					<tbody>
						<?php
					while (!$patologia->EOF) { ?>
						<tr>
							<td><?php echo $patologia->Fields('ID'); ?></td>
							<td><?php echo $patologia->Fields('descripcion_patologia'); ?></td>
							<td><?php echo $patologia->Fields('avg_estancia'); ?></td>
							<td><?php echo $patologia->Fields('limite_inferior_peso_grd'); ?></td>
							<td><?php echo $patologia->Fields('avg_peso_grd'); ?></td>
							<td><?php echo $patologia->Fields('avg_monto_grd_total'); ?></td>
							<td><?php echo $patologia->Fields('peso_grd_real'); ?></td>
							<td><?php echo $patologia->Fields('monto_grd_total_real'); ?></td>
							<td><?php echo $patologia->Fields('monto_x_linea'); ?></td>
							<td><?php echo $patologia->Fields('diferencia_montos'); ?></td>
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
	    $('#tblGrdReferencia').DataTable({
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



