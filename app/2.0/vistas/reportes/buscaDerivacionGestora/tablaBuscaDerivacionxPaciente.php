<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';
require_once('modal/modalBuscaDerivacionGestora.php');
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

$idUsuario = $_SESSION['idUsuario'];
$rutPaciente = $_REQUEST['rutPaciente'];

$query_qrPacientes = "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC='$rutPaciente'";
$qrPacientes = $oirs->SelectLimit($query_qrPacientes) or die($oirs->ErrorMsg());
$totalRows_qrPacientes = $qrPacientes->RecordCount();

$idPaciente = $qrPacientes->Fields('ID');

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_PACIENTE='$idPaciente'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

?>
<!-- cargo el id de usuario para capturarlo con datatables y pasarlo al server_proccessin -->
<input type="hidden" id="idUsuario" value="<?php echo $idUsuario ?>">
<input type="hidden" id="idPaciente" value="<?php echo $idPaciente ?>">

<div class="table-responsive-sm">
<table id="tPacientesDerivados" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Opciones</font></th>
			<th><font size="2">Derivación</font></th>
			<th><font size="2">Estado</font></th>
			<th><font size="2">Fecha derivación</font></th>
			<th><font size="2">Monto derivación</font></th>
			<th><font size="2">Rut paciente</font></th>
			<th><font size="2">Nombre paciente</font></th>
			<th><font size="2">Tipo</font></th>
			<th><font size="2">Patología</font></th>
			<th><font size="2">Convenio</font></th> 
			<th><font size="2">Gestora</font></th>
			<th><font size="2">N</font></th>
		</tr>
	</thead>
</table>
</div>

<script type="text/javascript">
	filtro = $('#filtro').val();

		$(function () {
		    $('#tPacientesDerivados').DataTable({
		      "paging": true,
		      "lengthChange": true,
		      "searching": true,
		      "ordering": true,
		      "info": true,
		      "autoWidth": true,
		      "responsive": true,
		      "processing": true,
		      "serverSide": true,
		      "ajax": {
		                 "url": "vistas/serverProcessing/derivacionesPorPaciente.php",
		                 data: function (d) {
		                     d.idPaciente = $('#idPaciente').val();//paso el id del paciente para filtrar por paciente en el serverSide de datatable
		                     d.idUsuario = $('#idUsuario').val();//paso el id del Usuario para filtrar por Usuario en el serverSide de datatable
		                 },
		             },
		      "order": [[ 11, 'desc' ]],
		      dom: 'lBfrtip',
		      buttons: [
		                  {
		                      extend: 'excelHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11 ]
		                      }
		                  },
		                  {
		                      extend: 'pdfHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11 ]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });

	function fnfrmBuscaDerivacionGestora(idDerivacion){
	
	    $('#dvBuscaDerivacionGestora').load('vistas/reportes/buscaDerivacionGestora/modal/frmDetalleBuscaDerivacionGestora.php?idDerivacion=' + idDerivacion);
	}
</script>