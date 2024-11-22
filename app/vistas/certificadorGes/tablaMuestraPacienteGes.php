<?php
//Connection statement
require_once '../../Connections/oirs.php';
//Aditional Function
require_once '../../includes/functions.inc.php';

$rutPaciente = $_REQUEST['rutPaciente'];


$query_qrDerivacion = "
    SELECT 

    	derivaciones.FOLIO,
    	patologia.DESC_PATOLOGIA,
    	pacientes.COD_RUTPAC,
    	login.NOMBRE AS NOMBRE_PROFESIONAL,
    	derivaciones.FECHA_DERIVACION,
    	pacientes.NOMBRE AS NOMBRE_PACIENTE

	FROM derivaciones 

	LEFT JOIN login
	ON derivaciones.ENFERMERA = login.ID

	LEFT JOIN pacientes
	ON derivaciones.ID_PACIENTE = pacientes.ID

	LEFT JOIN patologia
	ON derivaciones.ID_PATOLOGIA = patologia.ID_PATOLOGIA

	WHERE	
	pacientes.COD_RUTPAC='$rutPaciente' AND
	derivaciones.ESTADO_ANULACION ='activo' AND
	(derivaciones.ESTADO_RN='Derivacion Aceptada' OR
	derivaciones.ESTADO_RN='Prestador asignado' OR	
	derivaciones.ESTADO_RN='Solicita autorizacion')

";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

    

?>
<div class="container">

	<?php if($totalRows_qrDerivacion!='0'){ ?>

	<div class="card card-primary card-outline">
	  <div class="card-body box-profile">
	    <div class="text-center">
	        <div align="center" class="icon">
	      		<i class="fas fa-user-circle"></i>
	        </div>
	        <h3><?php echo $qrDerivacion->Fields('NOMBRE_PACIENTE')?></h3>

	    	<p class="text-muted text-center"><span class="badge badge-success">Registrado</span></p>
	    </div>

	    <ul class="list-group list-group-unbordered mb-3">
	      <li class="list-group-item">
	        <b>Folio: </b> <a class="float-right"><?php echo  $folio= $qrDerivacion->Fields('FOLIO'); ?></a>
	      </li>
	      <li class="list-group-item">
	        <b>Fecha Derivación: </b> <a class="float-right"><?php echo date("d-m-Y", strtotime($qrDerivacion->Fields('FECHA_DERIVACION')))?></a>
	      </li>
	      <li class="list-group-item">
	        <b>Patología: </b> <a class="float-right"><?php echo  $qrDerivacion->Fields('DESC_PATOLOGIA'); ?></a>
	      </li>
	      <li class="list-group-item">
	        <b>Gestora: </b> <a class="float-right"><?php echo  $qrDerivacion->Fields('NOMBRE_PROFESIONAL'); ?></a>
	      </li>
	    </ul>

	    <a target="_BLANK" href="detallePacienteGesPdf.php?folio=<?php echo $folio ?>" class="btn btn-outline-primary btn-block"><i class="fas fa-file-pdf"></i> <b>Genera carta de resguardo</b></a>
	  </div>
	  <!-- /.card-body -->
	</div>
	<?php }else{ ?>
			<br>
			<div align="center">
	      		<h3>EL PACIENTE NO CUENTA CON BENEFICIOS ACTIVOS</h3>
	        </div>
		

	<?php 	} ?>
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