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
if ($estado == '') { ?>
	<input type="hidden" id="filtro" value="todo">
<?php } 

//MUESTRA sin bitacora
if ($estado =='actividad_quirurgica') { ?>
	<input type="hidden" id="filtro" value="actividad_quirurgica">
<?php } 


?>



<!-- cargo el id de usuario para capturarlo con datatables y pasarlo al server_proccessin -->
<input type="hidden" id="idUsuario" value="<?php echo $idUsuario ?>">

<div class="table-responsive-sm">
<table id="tActividadQuirurgica" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Opciones</font></th>
			<th><font size="2">Folio RN</font></th>
			<th><font size="2">Estado</font></th>
			<th><font size="2">Fecha derivación</font></th>
			<th><font size="2">Rut paciente</font></th>
			<th><font size="2">Nombre paciente</font></th>
			<th><font size="2">Especialidad P/S</font></th>
			<th><font size="2">Fecha reserva</font></th>
			<th><font size="2">Id reserva</font></th>
			<th><font size="2">Fecha inicio pab</font></th>
			<th><font size="2">Médico</font></th>
			<th><font size="2">Código prestación</font></th>
			<th><font size="2">Nombre cirugía</font></th>
			<th><font size="2">Estado</font></th>
			<th><font size="2">Gestora</font></th>
		</tr>
	</thead>
</table>
</div>

<script type="text/javascript">
	filtro = $('#filtro').val();

	if (filtro == 'actividad_quirurgica') { 
		$(function () {
		    $('#tActividadQuirurgica').DataTable({
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
		                 "url": "2.0/vistas/serverProcessing/actividadQuirurgica.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11,12,13,14 ]
		                      }

		                  },
		                  {
		                      extend: 'pdfHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11,12,13,14  ]
		                      }
		                  }
		                  
		              ],
		        
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
	}// fin if todo




</script>