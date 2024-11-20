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

$usuario = $_SESSION['dni'];
$idUsuario = $_SESSION['idUsuario'];
$tipoUsuario = $_SESSION['tipoUsuario'];

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idPaciente = $qrDerivacion->Fields('ID_PACIENTE');

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$idPaciente'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$codRutPac = $qrPaciente->Fields('COD_RUTPAC');

$codTipoPatologia = $qrDerivacion->Fields('CODIGO_TIPO_PATOLOGIA');

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia WHERE ID_TIPO_PATOLOGIA = '$codTipoPatologia'";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

$codPatologia = $qrDerivacion->Fields('ID_PATOLOGIA');

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE ID_PATOLOGIA = '$codPatologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$codEtapaPatologia = $qrDerivacion->Fields('CODIGO_ETAPA_PATOLOGIA');

$query_qrEtapaPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA = '$codEtapaPatologia'";
$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

$codCanastaPatologia = $qrDerivacion->Fields('CODIGO_CANASTA_PATOLOGIA');

$query_qrCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanastaPatologia'";
$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

$query_qrAsignarEnfermeria= "SELECT * FROM $MM_oirs_DATABASE.login order by NOMBRE asc";
$qrAsignarEnfermeria = $oirs->SelectLimit($query_qrAsignarEnfermeria) or die($oirs->ErrorMsg());
$totalRows_qrAsignarEnfermeria = $qrAsignarEnfermeria->RecordCount();

$query_qrAsignarMedico= "
	SELECT 
	login.ID,
	login.USUARIO,
	login.NOMBRE,
	especialidades_medicas.ESPECIALIDAD,
  subespecialidades_medicas.SUBESPECIALIDAD

	FROM login

	LEFT JOIN especialidades_medicas
	ON login.ID_ESPECIALIDAD = especialidades_medicas.ID_ESPECIALIDAD
    
    LEFT JOIN subespecialidades_medicas
	ON login.ID_SUBESPECIALIDAD = subespecialidades_medicas.ID_SUBESPECIALIDAD

	WHERE TIPO = '5'
	GROUP BY login.ID";
$qrAsignarMedico = $oirs->SelectLimit($query_qrAsignarMedico) or die($oirs->ErrorMsg());
$totalRows_qrAsignarMedico = $qrAsignarMedico->RecordCount();

$codConvenio = $qrDerivacion->Fields('ID_CONVENIO');

$query_qrConvenio= "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$codConvenio'";
$qrConvenio = $oirs->SelectLimit($query_qrConvenio) or die($oirs->ErrorMsg());
$totalRows_qrConvenio = $qrConvenio->RecordCount();

$query_qrDerivacionCanasta= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
$qrDerivacionCanasta = $oirs->SelectLimit($query_qrDerivacionCanasta) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanasta = $qrDerivacionCanasta->RecordCount();

$query_qrDerivacionEtapa= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_etapas WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacionEtapa = $oirs->SelectLimit($query_qrDerivacionEtapa) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionEtapa = $qrDerivacionEtapa->RecordCount();

//consulta que busca canasta inicial de la derivacion para pasarla a la API de Medico para insertarse en derivaciones_canastas de Medico
$query_qrDerivacionCanastaInicial= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION = '$idDerivacion' and INICIAL ='si'";
$qrDerivacionCanastaInicial = $oirs->SelectLimit($query_qrDerivacionCanastaInicial) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanastaInicial = $qrDerivacionCanastaInicial->RecordCount();


?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Asignar Médico Tratante:</h2></div>

						<div class="col-md-6" id="dvInfoVentanasOpcionesAsignaMedico"></div>
						
						<div class="col-md-6">
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Asignar a</span>
							    </div>
							    <select name="slAsignarMedicoDerivacion" id="slAsignarMedicoDerivacion" class="form-control input-sm select2bs4" onchange="fnCambiaComentarioBitacora(this.value,'<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>','<?php echo utf8_encode($qrPaciente->Fields('NOMBRE')); ?>','<?php echo $codRutPac; ?>','<?php echo $canasta; ?>')">
							        <option value="">Seleccione...</option>
							        <?php while (!$qrAsignarMedico->EOF) {?>
							          <option value="<?php echo $qrAsignarMedico->Fields('ID') ?>"><?php echo utf8_encode($qrAsignarMedico->Fields('NOMBRE')) ?> - <?php echo utf8_encode($qrAsignarMedico->Fields('ESPECIALIDAD')) ?> - <?php echo utf8_encode($qrAsignarMedico->Fields('SUBESPECIALIDAD')) ?></option>
							        <?php $qrAsignarMedico->MoveNext(); } ?>
							    </select>
							</div>
							<span class="label label-default">Comentario bitácora<br></span>
		  				<textarea name="comentarioBitacoraAsignarMedicoCaso" id="comentarioBitacoraAsignarMedicoCaso" cols="11" rows="10" class="form-control input-sm"><?php if ($qrTipoPatologia->Fields('DESC_TIPO_PATOLOGIA') == 'GES') { ?>A la derivación número <?php echo $qrDerivacion->Fields('N_DERIVACION'); ?> del paciente <?php echo utf8_encode($qrPaciente->Fields('NOMBRE')); ?> rut <?php echo $codRutPac; ?> se le asigno el médico tratante: (seleccione el médico).<?php }else{ ?>A la derivación número <?php echo $qrDerivacion->Fields('N_DERIVACION'); ?> del paciente <?php echo utf8_encode($qrPaciente->Fields('NOMBRE')); ?> rut <?php echo $codRutPac; ?> se le asigno el Medico: (seleccione el médico).
		  					<?php } ?>
		  					 </textarea>  
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-info" data-dismiss="modal" onclick="fnAsignarMedicoCaso('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>','<?php echo $canasta; ?>')">Asignar Médico Tratante</button> 
			  	</div>     
	  		</div>
	  		<!-- lleno este hidden desde la funcion fnConsultaSiTieneModuloMedico, para capturarlo en la funcion  fnAsignarMedicoCaso y evaluar si llama o no a la API que le manda la derivacion-->
	  		<input type="hidden" id="hdModuloMedico">
	  		<input type="hidden" id="idDerivacion" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
//Initialize Select2 Elements
  $('.select2bs4').select2({
    theme: 'bootstrap4'
  })

idDerivacion = $('#idDerivacion').val();	
$('#dvInfoVentanasOpcionesAsignaMedico').load('vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

function fnConsultaSiTieneModuloMedico(){
	slAsignarMedicoDerivacion = $('#slAsignarMedicoDerivacion').val();

	cadena = 'slAsignarMedicoDerivacion='+slAsignarMedicoDerivacion;

	$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/asignarMedicoCaso/consultaSiTieneModuloMedico.php', 
			success:function(r){
				//lleno el hidden con la respuesta si tiene modulo de Medico o no
				$('#hdModuloMedico').val(r);				
			}
		});
}

function fnAsignarMedicoCaso(idDerivacion,tipoUsuario,canasta){
	slAsignarMedicoDerivacion = $('#slAsignarMedicoDerivacion').val();
	comentarioBitacoraAsignarMedicoCaso = $('#comentarioBitacoraAsignarMedicoCaso').val(); 
	moduloMedico = $('#hdModuloMedico').val();

	if (slAsignarMedicoDerivacion == '' || comentarioBitacoraAsignarMedicoCaso == '') { 
		Swal.fire({
		  icon: 'error',
		  title: 'Oops...',
		  text: 'No se asigno el caso, complete los datos requeridos!',
		})
	}else{

	cadena = 'idDerivacion=' + idDerivacion +
			 '&slAsignarMedicoDerivacion=' + slAsignarMedicoDerivacion +
			 '&comentarioBitacoraAsignarMedicoCaso=' + comentarioBitacoraAsignarMedicoCaso +
			 '&canasta=' + canasta;

		
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/asignarMedicoCaso/asignarMedicoCaso.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'La derivacion ha sido asignada al Medico con exito',
					  showConfirmButton: false,
					  timer: 800
					})

					if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
					}
					if (tipoUsuario == 4) {// administrativa
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioAdministrativa/inicioSAdministrativa.php'); }, 1);
					}
					// if (tipoUsuario == 5) {//medico
					// 	setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
					// }
					if (tipoUsuario == 6) {//tens
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioTens/inicioTens.php'); }, 1);
					}
					
	    	}
				
			}
		});
	}
}

function fnCambiaComentarioBitacora(Medico,nderivacion,paciente,rutPaciente,canasta){
	nMedico = $('select[name="slAsignarMedicoDerivacion"] option:selected').text();

	if (canasta == '') {
		$('#comentarioBitacoraAsignarMedicoCaso').val('A la derivación número '+ nderivacion + ' del paciente '+paciente + ' rut '+rutPaciente + ' se le asigno el médico tratante: '+ nMedico  +'.');
	}else{
		$('#comentarioBitacoraAsignarMedicoCaso').val('A la derivación número '+ nderivacion + ' del paciente '+paciente + ' rut '+rutPaciente + ' se le asigno el médico tratante: '+ nMedico  +'.');
	}
	
}
	
</script>



