<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

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
	$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE ESTADO_RN ='Anulado'";
	$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
	$totalRows_qrDerivacion = $qrDerivacion->RecordCount();
}
if ($tipoProfiesional == 3) {
	$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE ESTADO_RN ='anulado'";
	$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
	$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

	// and ENFERMERA='$usuario'
}

?>
<div class="table-responsive-sm">
<table id="tPacientesAnulados" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Opciones</font></th>
			<th><font size="2">Folio RN</font></th>
			<th><font size="2">Estado</font></th>
			<th><font size="2">Fecha derivación</font></th>
			<th><font size="2">Rut</font></th>
			<th><font size="2">Nombre</font></th>
			<th><font size="2">Categoria</font></th>
			<th><font size="2">Patologia</font></th>
			<th><font size="2">Int. Sanitaria</font></th> 
			<th><font size="2">Folio hijo</font></th>
			<th><font size="2">Estado</font></th>
			<th><font size="2">Gestora</font></th>
			<th><font size="2">Tipo compra</font></th>
		</tr>
	</thead>
</table>
</div>

<!-- cargo el id de usuario para capturarlo con datatables y pasarlo al server_proccessin -->
<input type="hidden" id="idUsuario" value="<?php echo $idUsuario ?>">

<script type="text/javascript">
	
		$(function () {
		    $('#tPacientesAnulados').DataTable({
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
		                 "url": "2.0/vistas/serverProcessing/anuladas.php",
		                 data: function (d) {
		                     d.idUsuario = $('#idUsuario').val();//paso el id del usuario para filtrar por sesion en el serverSide de datatable
		                 },
		             },
		      "order": [[ 1, 'desc' ]],
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
		                      }
		                  }
		                  
		              ],
		        "lengthMenu": [[5, 10, -1], [5, 10, "Todos"]],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
</script>