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
require_once('../../modulos/cerrarCaso/modalCerrarCaso.php');
require_once('../../modulos/reasignarCaso/modalReasignarCaso.php');
require_once('../../modulos/asignarMedicoCaso/modalAsignarMedicoCaso.php');
require_once('../../modulos/asignarTeamGestion/modalTeamGestion.php');
//require_once('../../modulos/contactarPaciente/modalContactarPaciente.php');
require_once('../../modulos/asignarCita/modalAsignarCita.php');
require_once('../../modulos/atenderPaciente/modalAtenderPaciente.php');
require_once('../../modulos/derivacion/modalDerivacion.php');
require_once('../../modulos/informacionPaciente/modalEditaInformacionPacienteSupervisora.php');
require_once('../../modulos/enviaInfoPacACorreo/modalEnviarACorreo.php');
require_once('../../modulos/agregarMarca/modalAgregarMarca.php');

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
if($estado != 'pendiente' and $estado != 'aceptada' and $estado != 'prestador' and $estado != 'cerrada' and $estado !='' and $estado!='cumplidas' and $vencidas!='vencidas' and $estado!='mas24HrsSinAgenda'  and $estado!='mas10DiasSinAtencion' and $estado!='quirurgicas' and $estado!='alta_paciente' and $estado!='autorizado_para_pago' and $estado!='validado_para_pago' and $estado!='para_cierre' and $estado!='solicita_autorizacion'and $estado!='solicita_autorizacion_en_plazo' and $estado!='solicita_autorizacion_retrasada'){ ?>
	<input type="hidden" id="filtro" value="porVencer">
<?php }

//QUERY CALCULA VENCIDAS
if($estado != 'pendiente' and $estado != 'aceptada' and $estado != 'prestador'  and $estado != 'cerrada'  and $estado !='' and $estado!='cumplidas' and $vencidas=='vencidas' and $estado!='mas24HrsSinAgenda' and $estado!='mas10DiasSinAtencion' and $estado!='quirurgicas' and $estado!='alta_paciente' and $estado!='autorizado_para_pago' and $estado!='validado_para_pago' and $estado!='para_cierre' and $estado!='solicita_autorizacion'and $estado!='solicita_autorizacion_en_plazo' and $estado!='solicita_autorizacion_retrasada'){  ?>
	<input type="hidden" id="filtro" value="vencidas">
<?php } 

//MUESTRA POR cumplidas
if ($estado =='cumplidas') { ?>
	<input type="hidden" id="filtro" value="cumplidas">
<?php } 

//MUESTRA POR DERIVACION SIN AGENDA PRIMERA CONSULTA DESPUES DE 24 HRS
if ($estado =='mas24HrsSinAgenda') { ?>
	<input type="hidden" id="filtro" value="mas24HrsSinAgenda">
<?php } 

//MUESTRA POR DERIVACION SIN ATENCION PRIMERA CONSULTA DESPUES DE 10 DIAS
if ($estado =='mas10DiasSinAtencion') { ?>
	<input type="hidden" id="filtro" value="mas10DiasSinAtencion">
<?php }

if ($estado =='quirurgicas') { ?>
	<input type="hidden" id="filtro" value="quirurgicas">
<?php } 

if ($estado =='alta_paciente') { ?>
	<input type="hidden" id="filtro" value="Alta Paciente">
<?php }

if ($estado =='autorizado_para_pago') { ?>
	<input type="hidden" id="filtro" value="Autorizado para pago">
<?php }

if ($estado =='validado_para_pago') { ?>
	<input type="hidden" id="filtro" value="Validado para Pago">
<?php }

if ($estado =='para_cierre') { ?>
	<input type="hidden" id="filtro" value="para_cierre">
<?php }
if ($estado =='solicita_autorizacion') { ?>
	<input type="hidden" id="filtro" value="solicita_autorizacion">
<?php }

if ($estado =='solicita_autorizacion_en_plazo') { ?>
	<input type="hidden" id="filtro" value="solicita_autorizacion_en_plazo">
<?php }

if ($estado =='solicita_autorizacion_retrasada') { ?>
	<input type="hidden" id="filtro" value="solicita_autorizacion_retrasada">
<?php }

?>

<!-- cargo el id de usuario para capturarlo con datatables y pasarlo al server_proccessin -->
<input type="hidden" id="idUsuario" value="<?php echo $idUsuario ?>">

<div class="table-responsive-sm">
<table id="tPacientesDerivadosTens" class="table table-bordered table-striped table-hover table-sm">
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
			<th><font size="2">Convenio</font></th> 
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
		    $('#tPacientesDerivadosTens').DataTable({
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
		      "ajax": {
		                 "url": "vistas/serverProcessing/tens/derivaciones.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
		                      }
		                  },
		                  {
		                      extend: 'pdfHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12  ]
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
		    $('#tPacientesDerivadosTens').DataTable({
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
		      "ajax": {
		                 "url": "vistas/serverProcessing/tens/pendientes.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
		                      }
		                  },
		                  {
		                      extend: 'pdfHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12  ]
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
		    $('#tPacientesDerivadosTens').DataTable({
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
		      "ajax": {
		                 "url": "vistas/serverProcessing/tens/aceptadas.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
		                      }
		                  },
		                  {
		                      extend: 'pdfHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12  ]
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
		    $('#tPacientesDerivadosTens').DataTable({
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
		      "ajax": {
		                 "url": "vistas/serverProcessing/tens/por_vencer.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
		                      }
		                  },
		                  {
		                      extend: 'pdfHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12  ]
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
		    $('#tPacientesDerivadosTens').DataTable({
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
		      "ajax": {
		                 "url": "vistas/serverProcessing/tens/vencidas.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
		                      }
		                  },
		                  {
		                      extend: 'pdfHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12  ]
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
		    $('#tPacientesDerivadosTens').DataTable({
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
		      "ajax": {
		                 "url": "vistas/serverProcessing/tens/cumplidas.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
		                      }
		                  },
		                  {
		                      extend: 'pdfHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12  ]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
	}// fin if cumplidas	

	// if mas 24 Hrs Sin Agenda
	if (filtro == 'mas24HrsSinAgenda') {
		$(function () {
		    $('#tPacientesDerivadosTens').DataTable({
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
		      "ajax": {
		                 "url": "vistas/serverProcessing/tens/mas24HrsSinAgenda.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
		                      }
		                  },
		                  {
		                      extend: 'pdfHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12  ]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
	}// fin if mas 24 Hrs Sin Agenda	

	// if mas 10 dias Sin Atención
	if (filtro == 'mas10DiasSinAtencion') {
		$(function () {
		    $('#tPacientesDerivadosTens').DataTable({
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
		      "ajax": {
		                 "url": "vistas/serverProcessing/tens/mas10DiasSinAtencion.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
		                      }
		                  },
		                  {
		                      extend: 'pdfHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12  ]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
	}// fin if mas 10 dias Sin Atención

	// if quirurgicas
	if (filtro == 'quirurgicas') {
		$(function () {
		    $('#tPacientesDerivadosTens').DataTable({
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
		      "ajax": {
		                 "url": "vistas/serverProcessing/tens/quirurgicas.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
		                      }
		                  },
		                  {
		                      extend: 'pdfHtml5',
		                      exportOptions: {
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12  ]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
	}// fin if quirurgicas

	if (filtro == 'Alta Paciente') {
		$(function () {
		    $('#tPacientesDerivadosTens').DataTable({
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
		      "ajax": {
		                 "url": "vistas/serverProcessing/administrativa/altaPaciente.php",
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
	}//fin if Alta paciente

	if (filtro == 'Autorizado para pago') {
		$(function () {
		    $('#tPacientesDerivadosTens').DataTable({
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
		      "ajax": {
		                 "url": "vistas/serverProcessing/administrativa/autorizadoPago.php",
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
	}//fin if Autorizado para pago

	if (filtro == 'Validado para Pago') {
		$(function () {
		    $('#tPacientesDerivadosTens').DataTable({
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
		      "ajax": {
		                 "url": "vistas/serverProcessing/administrativa/validadoPago.php",
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
		                          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
		                      }
		                  }
		                  
		              ],
			    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
		    });

		  });
	}//fin if Validado para pago

	if (filtro == 'para_cierre') {
		$(function () {
		    $('#tPacientesDerivadosTens').DataTable({
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
		                 "url": "vistas/serverProcessing/tens/paraCierre.php",
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

	if (filtro == 'solicita_autorizacion') {
		$(function () {
		    $('#tPacientesDerivadosTens').DataTable({
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
		                 "url": "vistas/serverProcessing/tens/solicitaAutorizacion.php",
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
	if (filtro == 'solicita_autorizacion_en_plazo') {
		$(function () {
		    $('#tPacientesDerivadosTens').DataTable({
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
		                 "url": "vistas/serverProcessing/tens/solicitaAutorizacionEnPlazo.php",
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

		if (filtro == 'solicita_autorizacion_retrasada') {
		$(function () {
		    $('#tPacientesDerivadosTens').DataTable({
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