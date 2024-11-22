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

require_once('../../modulos/estadoAvance/modalEstadoAvance.php');


$estado = $_REQUEST['estado'];
$vencidas = $_REQUEST['vencidas'];
date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');

?>



<!-- cargo el id de usuario para capturarlo con datatables y pasarlo al server_proccessin -->
<input type="hidden" id="idUsuario" value="<?php echo $idUsuario ?>">

<div class="table-responsive-sm">
<table id="tPacientesEstAvMisCasos" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Opciones</font></th>
			<th><font size="2">PROBLEMA DE SALUD</font></th>
			<th><font size="2">FOLIO</font></th>
			<th><font size="2">TIPO PS</font></th>
			<th><font size="2">RUT</font></th>
			<th><font size="2">DERIVACION</font></th>
			<th><font size="2">AVANCE</font></th>
			<th><font size="2">motivo_rechazo</font></th>
			<th><font size="2">tipo_contacto</font></th> 
			<th><font size="2">GESTORA</font></th> 
		</tr>
	</thead>
</table>
</div>

<script type="text/javascript">
	$(function () {
	    $('#tPacientesEstAvMisCasos').DataTable({
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
	                 "url": "2.0/vistas/serverProcessing/estadoAvanceMisCasos.php",
	                 data: function (d) {
	                     d.idUsuario = $('#idUsuario').val();//paso el id del usuario para filtrar por sesion en el serverSide de datatable
	                 },
	             },
	      
	      "order": [[ 2, 'desc' ]],
	      dom: 'lBfrtip',
	      buttons: [
	                  {
	                      extend: 'excelHtml5',
	                      exportOptions: {
	                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9]
	                      }

	                  },
	                  {
	                      extend: 'pdfHtml5',
	                      exportOptions: {
	                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9]
	                      }
	                  }
	                  
	              ],
	        
		    "language": {
		        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json" 
		        },
	    });

	  });




</script>