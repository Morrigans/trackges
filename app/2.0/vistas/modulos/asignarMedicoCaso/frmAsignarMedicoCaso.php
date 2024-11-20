<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];
$idUsuario = $_SESSION['idUsuario'];
$tipoUsuario = $_SESSION['tipoUsuario'];

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$codRutPac = $qrDerivacion->Fields('COD_RUTPAC');

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

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

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('FOLIO'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Asignar Médico Tratante:</h2></div>

						<div class="col-md-6" id="dvInfoVentanasOpcionesAsignaMedico"></div>
						
						<div class="col-md-6">
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Asignar a</span>
							    </div>
							    <select name="slAsignarMedicoDerivacion" id="slAsignarMedicoDerivacion" class="form-control input-sm select2bs4">
							        <option value="">Seleccione...</option>
							        <?php while (!$qrAsignarMedico->EOF) {?>
							          <option value="<?php echo $qrAsignarMedico->Fields('USUARIO') ?>"><?php echo utf8_encode($qrAsignarMedico->Fields('NOMBRE')) ?> - <?php echo utf8_encode($qrAsignarMedico->Fields('ESPECIALIDAD')) ?> - <?php echo utf8_encode($qrAsignarMedico->Fields('SUBESPECIALIDAD')) ?></option>
							        <?php $qrAsignarMedico->MoveNext(); } ?>
							    </select>
							</div>
							
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-info" data-dismiss="modal" onclick="fnAsignarMedicoCaso('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>','<?php echo $canasta; ?>')">Asignar Médico Tratante</button> 
			  	</div>     
	  		</div>
	  		<!-- lleno este hidden desde la funcion fnConsultaSiTieneModuloMedico, para capturarlo en la funcion  fnAsignarMedicoCaso y evaluar si llama o no a la API que le manda la derivacion-->
	  		<input type="hidden" id="hdModuloMedico">
	  		<input type="hidden" id="idDerivacionAsignaMedico" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
//Initialize Select2 Elements
  $('.select2bs4').select2({
    theme: 'bootstrap4'
  })

idDerivacion = $('#idDerivacionAsignaMedico').val();
$('#dvInfoVentanasOpcionesAsignaMedico').load('2.0/vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

function fnConsultaSiTieneModuloMedico(){
	slAsignarMedicoDerivacion = $('#slAsignarMedicoDerivacion').val();

	cadena = 'slAsignarMedicoDerivacion='+slAsignarMedicoDerivacion;

	$.ajax({
			type:"post",
			data:cadena,
			url:'2.0/vistas/modulos/asignarMedicoCaso/consultaSiTieneModuloMedico.php', 
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
			url:'2.0/vistas/modulos/asignarMedicoCaso/asignarMedicoCaso.php',
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
						setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1000);
					}
					if (tipoUsuario == 4) {// administrativa
						setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioAdministrativa/inicioSAdministrativa.php'); }, 1000);
					}
					// if (tipoUsuario == 5) {//medico
					// 	setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
					// }
					if (tipoUsuario == 6) {//tens
						setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioTens/inicioTens.php'); }, 1000);
					}
					
	    	}
				
			}
		});
	}
}

	
</script>



