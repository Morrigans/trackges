<?php
require_once '../../../../Connections/oirs.php';
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];
$idUsuario = $_SESSION['idUsuario'];

$estado = $_REQUEST['estado'];
$vencidas = $_REQUEST['vencidas'];
date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');

if ($estado =='oncoIcrs') { ?>
	<input type="hidden" id="filtro" value="oncoIcrs">
<?php }
?>

<!-- cargo el id de usuario para capturarlo con datatables y pasarlo al server_proccessin -->
<input type="hidden" id="idUsuario" value="<?php echo $idUsuario ?>">

<div class="table-responsive-sm">
<table id="tblPacientesOncoIcrs" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Opciones</font></th>
			<th><font size="2">Derivación</font></th>
			<th><font size="2">Estado</font></th>
			<th><font size="2">Folio RN</font></th>
			<th><font size="2">Estado RN</font></th>
			<th><font size="2">Fecha derivación</font></th>
			<th><font size="2">Dias</font></th>
			<th><font size="2">Monto acumulado</font></th>
			<th><font size="2">Monto dev</font></th>
			<th><font size="2">dias d/qx</font></th>
			<th><font size="2">Rut paciente</font></th>
			<th><font size="2">Nombre paciente</font></th>
			<th><font size="2">Patología</font></th>
			<th><font size="2">Tens</font></th> 
			<th><font size="2">Gestora</font></th>
			<th><font size="2">Medico</font></th>
			<th><font size="2">N</font></th>
		</tr>
	</thead>
</table>
</div>

<script type="text/javascript">
	filtro = $('#filtro').val();

	if (filtro == 'oncoIcrs') { 
		$(function () {
		    $('#tblPacientesOncoIcrs').DataTable({
		      "paging": true,
		      "lengthChange": true,
		      "searching": true,
		      "ordering": true,
		      "info": true,
		      "autoWidth": true,
		      "responsive": true,
		      "processing": true,
		      "serverSide": true,
		      "stateSave": true,
		      "lengthMenu": [[10, 50, -1], [10, 50, "Todos"]],
		      "ajax": {
		                 "url": "2.0/vistas/serverProcessing/oncoIcrs.php",
		                 data: function (d) {
		                     d.idUsuario = $('#idUsuario').val();//paso el id del usuario para filtrar por sesion en el serverSide de datatable
		                 },
		             },
		      "order": [[ 12, 'desc' ]],
		      dom: 'lBfrtip',
		      buttons: [
		                  {
		                      extend: 'excelHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 ]
		                      }
		                  },
		                  {
		                      extend: 'pdfHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 ]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
	}
</script>