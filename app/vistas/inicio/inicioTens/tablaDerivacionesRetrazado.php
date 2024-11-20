<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

$usuario = $_SESSION['dni'];
$idUsuario = $_SESSION['idUsuario'];

require_once('../../modulos/aceptarCaso/modalAceptarCaso.php');
require_once('../../modulos/asignarPatologiaEtapaCanasta/modalAsignarPatologiaEtapaCanasta.php');
require_once('../../modulos/cerrarCaso/modalCerrarCaso.php');
require_once('../../modulos/reasignarCaso/modalReasignarCaso.php');
require_once('../../modulos/asignarCaso/modalAsignarCaso.php');
require_once('../../modulos/asignarMedicoCaso/modalAsignarMedicoCaso.php');
require_once('../../modulos/asignarTeamGestion/modalTeamGestion.php');
//require_once('../../modulos/contactarPaciente/modalContactarPaciente.php');
require_once('../../modulos/asignarCita/modalAsignarCita.php');
require_once('../../modulos/atenderPaciente/modalAtenderPaciente.php');
require_once('../../modulos/derivacion/modalDerivacion.php');
require_once('../../modulos/informacionPaciente/modalEditaInformacionPacienteSupervisora.php');
require_once('../../modulos/enviaInfoPacACorreo/modalEnviarACorreo.php');
require_once('../../modulos/agregarMarca/modalAgregarMarca.php');
require_once('../../modulos/motivoVencidaNoFinalizada/modalMotivoVencidaNoFinalizada.php');

$estado = $_REQUEST['estado'];
$vencidas = $_REQUEST['vencidas'];
date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');

//CARGA AL INICIAR O PRESIONAR MOSTRAR TODO

if ($estado =='solicita_autorizacion_retrasada') { ?>
	<input type="hidden" id="filtro" value="solicita_autorizacion_retrasada">
<?php }


?>



<!-- cargo el id de usuario para capturarlo con datatables y pasarlo al server_proccessin -->
<input type="hidden" id="idUsuario" value="<?php echo $idUsuario ?>">

<div class="table-responsive-sm">
<table id="tPacientesDerivadosRetrazado" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Opciones</font></th>
			<th><font size="2">Derivación</font></th>
			<th><font size="2">Estado</font></th>
			<th><font size="2">Folio RN</font></th>
			<th><font size="2">Estado RN</font></th>
			<th><font size="2">Fecha derivación</font></th>
			<!-- <th><font size="2">Monto inicial</font></th> -->
			<th><font size="2">Monto acumulado</font></th>
			<th><font size="2">Rut paciente</font></th>
			<th><font size="2">Nombre paciente</font></th>
			<th><font size="2">Patología</font></th>
			<th><font size="2">Motivo</font></th> 
			<th><font size="2">Tens</font></th>
			<th><font size="2">Gestora</font></th>
			<th><font size="2">N</font></th>
		</tr>
	</thead>
</table>
</div>

<script type="text/javascript">
	filtro = $('#filtro').val();


	if (filtro == 'solicita_autorizacion_retrasada') {
		$(function () {
		    $('#tPacientesDerivadosRetrazado').DataTable({
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
		                 "url": "vistas/serverProcessing/tens/solicitaAutorizacionRetrasada.php",
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
	}//fin if Solicita autorizacion


</script>