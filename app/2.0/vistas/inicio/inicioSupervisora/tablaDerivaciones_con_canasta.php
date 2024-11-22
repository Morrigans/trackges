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
require_once('../../modulos/cerrarCaso/modalCerrarCaso.php');
require_once('../../modulos/reasignarCaso/modalReasignarCaso.php');
require_once('../../modulos/asignarPrestadorCaso/modalAsignarPrestadorCaso.php');
require_once('../../modulos/asignarTeamGestion/modalTeamGestion.php');
require_once('../../modulos/contactarPaciente/modalContactarPaciente.php');
require_once('../../modulos/asignarCita/modalAsignarCita.php');
require_once('../../modulos/atenderPaciente/modalAtenderPaciente.php');
require_once('../../modulos/derivacion/modalDerivacion.php');
require_once('../../modulos/informacionPaciente/modalEditaInformacionPacienteSupervisora.php');
require_once('../../modulos/enviaInfoPacACorreo/modalEnviarACorreo.php');

$estado = $_REQUEST['estado'];
$vencidas = $_REQUEST['vencidas'];
date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');

//CARGA AL INICIAR O PRESIONAR MOSTRAR TODO
if ($estado == '') { ?>
	<input type="hidden" id="filtro" value="todo">
<?php } 

//MUESTRA POR pendiente
if ($estado == 'pendiente') { ?>
	<input type="hidden" id="filtro" value="pendiente">
<?php } 

//MUESTRA POR aceptada
if ($estado =='aceptada') { ?>
	<input type="hidden" id="filtro" value="aceptada">
<?php } 

//QUERY CALCULA POR VENCER
if($estado != 'pendiente' and $estado != 'aceptada' and $estado != 'prestador' and $estado != 'cerrada' and $estado !='' and $estado!='cumplidas' and $vencidas!='vencidas'){ ?>
	<input type="hidden" id="filtro" value="porVencer">
<?php }

//QUERY CALCULA VENCIDAS
if($estado != 'pendiente' and $estado != 'aceptada' and $estado != 'prestador'  and $estado != 'cerrada'  and $estado !='' and $estado!='cumplidas' and $vencidas=='vencidas' ){  ?>
	<input type="hidden" id="filtro" value="vencidas">
<?php } 

//MUESTRA POR aceptada
if ($estado =='cumplidas') { ?>
	<input type="hidden" id="filtro" value="cumplidas">
<?php } ?>

<!-- cargo el id de usuario para capturarlo con datatables y pasarlo al server_proccessin -->
<input type="hidden" id="idUsuario" value="<?php echo $idUsuario ?>">

<div class="table-responsive-sm">
<table id="tPacientesDerivados" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Opciones</font></th>
			<th><font size="2">Derivación</font></th>
			<th><font size="2">Estado</font></th>
			<th><font size="2">Fecha derivación</font></th>
			<th><font size="2">Fecha activación</font></th>
			<th><font size="2">Rut paciente</font></th>
			<th><font size="2">Nombre paciente</font></th>
			<th><font size="2">Tipo</font></th>
			<th><font size="2">Patología</font></th>
			<th><font size="2">Cod canasta</font></th>
			<th><font size="2">Nombre canasta</font></th>
			<th><font size="2">Isapre</font></th> 
			<th><font size="2">Gestora</font></th>
			<th><font size="2">N</font></th>
		</tr>
	</thead>
</table>
</div>

<script type="text/javascript">
	filtro = $('#filtro').val();

	if (filtro == 'todo') {
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
		                 "url": "2.0/vistas/serverProcessing/derivaciones.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
	}// fin if todo

	if (filtro == 'pendiente') {
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
		                 "url": "2.0/vistas/serverProcessing/pendientes.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
	}//fin if pendiente

	if (filtro == 'aceptada') {
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
		                 "url": "2.0/vistas/serverProcessing/aceptadas.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
	}//fin if aceptada

	if (filtro == 'porVencer') {
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
		                 "url": "2.0/vistas/serverProcessing/por_vencer.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
	}// fin if por vencer

	if (filtro == 'vencidas') {
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
		                 "url": "2.0/vistas/serverProcessing/vencidas.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
	}// fin if vencidas	

	if (filtro == 'cumplidas') {
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
		                 "url": "2.0/vistas/serverProcessing/cumplidas.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
	}// fin if cumplidas	

</script>