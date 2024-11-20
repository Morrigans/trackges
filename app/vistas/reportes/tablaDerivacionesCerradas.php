<?php
//Connection statement
require_once '../../Connections/oirs.php';
//Aditional Functions
require_once '../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }
//require_once('modals/informacionPaciente/modalInformacionPacienteGestora.php');

$usuario = $_SESSION['dni'];
$idUsuario = $_SESSION['idUsuario'];

$query_qrProfesion = "SELECT * FROM $MM_oirs_DATABASE.login where USUARIO='$usuario'";
$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
$totalRows_qrProfesion = $qrProfesion->RecordCount();

$tipoProfiesional = $qrProfesion->Fields('TIPO');

//evaluo si es supervisora/administrador o profesional
if ($tipoProfiesional == 1 or $tipoProfiesional == 2) {
	$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ESTADO ='cerrada'";
	$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
	$totalRows_qrDerivacion = $qrDerivacion->RecordCount();
}
if ($tipoProfiesional == 3) {
	$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ESTADO ='cerrada' and ENFERMERA='$usuario'";
	$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
	$totalRows_qrDerivacion = $qrDerivacion->RecordCount();
}

?>
<div class="table-responsive-sm">
<table id="tPacientesCerrados" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Opciones</font></th>
			<th><font size="2">Derivación</font></th>
			<th><font size="2">Estado</font></th>
			<th><font size="2">Folio RN</font></th>
			<th><font size="2">Estado RN</font></th>
			<th><font size="2">Fecha derivación</font></th>
			<th><font size="2">Monto</font></th>
			<th><font size="2">Rut paciente</font></th>
			<th><font size="2">Nombre paciente</font></th> 
			<th><font size="2">Patologia</font></th>
			<th><font size="2">Convenio</font></th>
		</tr>
	</thead>
</table>
</div>

<!-- cargo el id de usuario para capturarlo con datatables y pasarlo al server_proccessin -->
<input type="hidden" id="idUsuario" value="<?php echo $idUsuario ?>">

<script type="text/javascript">
	
		$(function () {
		    $('#tPacientesCerrados').DataTable({
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
		                 "url": "vistas/serverProcessing/cerradas.php",
		                 data: function (d) {
		                     d.idUsuario = $('#idUsuario').val();//paso el id del usuario para filtrar por sesion en el serverSide de datatable
		                 },
		             },
		      "order": [[ 10, 'desc' ]],
		      dom: 'lBfrtip',
		      buttons: [
		                  {
		                      extend: 'excelHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ]
		                      }
		                  },
		                  {
		                      extend: 'pdfHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
</script>