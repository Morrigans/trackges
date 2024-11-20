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

$query_qrAsignarPrestador= "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO = '7'";
$qrAsignarPrestador = $oirs->SelectLimit($query_qrAsignarPrestador) or die($oirs->ErrorMsg());
$totalRows_qrAsignarPrestador = $qrAsignarPrestador->RecordCount();

$query_qrProfesion= "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '4' OR ID = '6'"; 
$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
$totalRows_qrProfesion = $qrProfesion->RecordCount();



//busca agenda en estado cita para ver si es primera o segunda consulta*******************************************************************
$query_qrEvents = "SELECT * FROM $MM_oirs_DATABASE.2_events WHERE ID_DERIVACION = '$idDerivacion' AND ESTADO_CITA = 'CITA'";
$qrEvents = $oirs->SelectLimit($query_qrEvents) or die($oirs->ErrorMsg());
$totalRows_qrEvents = $qrEvents->RecordCount();

$tipoAtencion = $qrEvents->Fields('TIPO_ATENCION');//obtengo el tipo de atencion
$idCitacion = $qrEvents->Fields('id');//obtengo el id de la cita

$query_qrEventsTipoAtencion = "SELECT * FROM $MM_oirs_DATABASE.2_events WHERE TIPO_ATENCION = '$tipoAtencion'";
$qrEventsTipoAtencion = $oirs->SelectLimit($query_qrEventsTipoAtencion) or die($oirs->ErrorMsg());
$totalRows_qrEventsTipoAtencion = $qrEventsTipoAtencion->RecordCount();
//*****************************************************************************************************************************************

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('FOLIO'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Atender paciente:</h2></div>

						<div class="col-md-6" id="dvInfoVentanasOpcionesAtenderPaciente"></div>

						<div id="dvFrmAtenderPaciente" class="col-md-6">
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Tipo atención</span>
							    </div>
							    <input type="text" name="tipoAtencionAgendada" id="tipoAtencionAgendada" class="form-control input-sm" value="<?php echo $qrEventsTipoAtencion->Fields('TIPO_ATENCION'); ?>" readonly>
							</div>

							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Atiende</span>
							    </div>
							    <select name="slAtiendePaciente" id="slAtiendePaciente" class="form-control input-sm" onchange="fnOpcionesAtiende(this.value)">
							        <option value="">Seleccione...</option>
							        <option value="no">No</option>
							        <option value="si">Si</option>
							       
							    </select>
							</div>

							<div id="dvQuirurgico" class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">¿Quirúrgica?</span>
							    </div>
							    <select name="slQuirurgico" id="slQuirurgico" class="form-control input-sm">
							        <option value="">Seleccione...</option>
							        <option value="no">No</option>
							        <option value="si">Si</option>
							       
							    </select>
							</div>

							<!-- <div id="dvCorrespondeCanasta" class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">¿Corresponde Canasta?</span>
							    </div>
							    <select name="slCorrespondeCanasta" id="slCorrespondeCanasta" class="form-control input-sm">
							        <option value="">Seleccione...</option>
							        <option value="no">No</option>
							        <option value="si">Si</option>
							       
							    </select>
							</div> -->

							<div class="input-group mb-3 col-sm-12">
							    <textarea name="slComentarioAtiendePaciente" id="slComentarioAtiendePaciente" cols="11" rows="6" class="form-control input-sm" placeholder="Indique un comentario de la atención, este comentario se vera reflejado en la bitacora del paciente"></textarea>
							</div>
							<input type="hidden" name="hdRutaAdjuntaDocAtencion" id="hdRutaAdjuntaDocAtencion" class="form-control input-sm">

							
							<div id="dvMuestraBtnAdjuntarDoc">
								<a href="#" onclick="$('#dvCargaAdjuntarAtiendePaciente').load('2.0/vistas/bitacora/adjuntaDoc/adjuntaDocumentoAtencionPaciente.php?idDerivacion='+<?php echo $idDerivacion?>), $('#dvCargaAdjuntarAtiendePaciente').show();"><span class="badge badge-warning"><i class="fas fa-paperclip"></i>Adjuntar documento</span></a>
							</div>

							<div id="dvMuestraPreDocAdjunto">
								[<span class=""><a target="_blank" class="btn btn-xs btn-success" href="#" onclick='fnMuestraAdjuntoAtencion()' ><i class="far fa-file-pdf"></i> Visualizar</a></span>
								<button class="btn btn-xs btn-danger" onclick="preguntarSiNoEliminaAdjuntoAtencion()"><span class=" fas fa-trash-alt"></span></button>]
							</div>

							<div id="dvCargaAdjuntarAtiendePaciente"></div>

						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					    <button type="button" class="btn btn-success" onclick="fnGuardaAtencionPaciente('<?php echo $idDerivacion ?>','<?php echo $idCitacion ?>','<?php echo $tipoUsuario ?>')">Guardar atención</button>
			  	</div>     
	  		</div>
	  		
	  		<input type="hidden" id="idDerivacionAtenderPaciente" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
	$("#dvQuirurgico").hide();
	$("#dvCorrespondeCanasta").hide();
	$("#dvMuestraPreDocAdjunto").hide();

	idDerivacion = $('#idDerivacionAtenderPaciente').val();	
	$('#dvInfoVentanasOpcionesAtenderPaciente').load('2.0/vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

	tipoAtencionAgendada = $("#tipoAtencionAgendada").val();
	if (tipoAtencionAgendada == '') {
		$("#dvFrmAtenderPaciente").html('El paciente no tiene citas agendadas');

	}else{
		function fnGuardaAtencionPaciente(idDerivacion,idCitacion,tipoUsuario){
			  tipoAtencionAgendada = $("#tipoAtencionAgendada").val();
			  slAtiendePaciente = $('#slAtiendePaciente').val();
			  slComentarioAtiendePaciente = $("#slComentarioAtiendePaciente").val();
			  slCorrespondeCanasta = $("#slCorrespondeCanasta").val();
			  slQuirurgico = $("#slQuirurgico").val();
			  rutaAdjuntaDocAtencion = $("#hdRutaAdjuntaDocAtencion").val();

			  if (tipoAtencionAgendada == 'primeraConsulta') {
			  	if (slAtiendePaciente == '') {
			  		Swal.fire({
			  		  position: 'top-end',
			  		  icon: 'warning',
			  		  title: 'Debe completar todos los datos del formulario',
			  		  showConfirmButton: false,
			  		  timer: 2000
			  		})
			  	}else{
					  		  	cadena = 'tipoAtencionAgendada=' + tipoAtencionAgendada +
					  	  	         '&idDerivacion=' + idDerivacion +
					  	  	         '&idCitacion=' + idCitacion +
					  	  	         '&slAtiendePaciente=' + slAtiendePaciente +
					  	  	         '&slCorrespondeCanasta=' + slCorrespondeCanasta +
					  	  	         '&rutaAdjuntaDocAtencion=' + rutaAdjuntaDocAtencion +
					  	  	         '&slComentarioAtiendePaciente=' + slComentarioAtiendePaciente; 

					  	  	$.ajax({
					  	  	  type: "POST",
					  	  	  url: "2.0/vistas/modulos/atenderPaciente/guardarAtencionPacientePrimeraCita.php",
					  	  	  data: cadena,
					  	  	  success:function(r){

					  	  	  	$('#modalAtenderPaciente').modal('hide');

				  	  	        Swal.fire({
				  	  	          position: 'top-end',
				  	  	          icon: 'success',
				  	  	          title: 'Atención guardada con exito',
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
					  	  	    // 	setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1000);
					  	  	    // }
					  	  	    if (tipoUsuario == 6) {//tens
					  	  	    	setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioTens/inicioTens.php'); }, 1000);
					  	  	    }
					  	  	    //setTimeout(function(){ $('#contenido_principal').load('2.0/vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 2100);

					  	  	    }

					  	  	}); 
			  	}

			  }//fin primera consulta

			    if (tipoAtencionAgendada == 'segundaConsulta') {
			    	if (slAtiendePaciente == '') {
			    		Swal.fire({
			    		  position: 'top-end',
			    		  icon: 'warning',
			    		  title: 'Debe completar todos los datos del formulario',
			    		  showConfirmButton: false,
			    		  timer: 2000
			    		})
			    	}else{
			  		  	if (slAtiendePaciente == 'si' && slQuirurgico == '') { 
			  		  		Swal.fire({
			  		  		  position: 'top-end',
			  		  		  icon: 'warning',
			  		  		  title: 'Debe completar todos los datos del formulario',
			  		  		  showConfirmButton: false,
			  		  		  timer: 2000
			  		  		})
			  		  	}else{
			  		  	  	cadena = 'tipoAtencionAgendada=' + tipoAtencionAgendada +
			  		  	  	         '&idDerivacion=' + idDerivacion +
			  		  	  	         '&idCitacion=' + idCitacion +
			  		  	  	         '&slAtiendePaciente=' + slAtiendePaciente +
			  		  	  	         '&slQuirurgico=' + slQuirurgico +
			  		  	  	         '&slCorrespondeCanasta=' + slCorrespondeCanasta +
			  		  	  	         '&rutaAdjuntaDocAtencion=' + rutaAdjuntaDocAtencion +
			  		  	  	         '&slComentarioAtiendePaciente=' + slComentarioAtiendePaciente; 

			  		  	  	$.ajax({
			  		  	  	  type: "POST",
			  		  	  	  url: "2.0/vistas/modulos/atenderPaciente/guardarAtencionPacienteSegundaCita.php",
			  		  	  	  data: cadena,
			  		  	  	  success:function(r){
			  		  	  	  		$('#modalAtenderPaciente').modal('hide');
			  		  	  	        Swal.fire({
			  		  	  	          position: 'top-end',
			  		  	  	          icon: 'success',
			  		  	  	          title: 'Atención guardada con exito',
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
			  		  	  	        // 	setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1000);
			  		  	  	        // }
			  		  	  	        if (tipoUsuario == 6) {//tens
			  		  	  	        	setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioTens/inicioTens.php'); }, 1000);
			  		  	  	        }

			  		  	  	        //setTimeout(function(){ $('#contenido_principal').load('2.0/vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 2100);
			  		  	  	    }
			  		  	  	  }); 
			  		  	}
			    	}

			    }//fin segunda consulta

			    if (tipoAtencionAgendada == 'otraConsulta') {
			      	if (slAtiendePaciente == '') {
			      		Swal.fire({
			      		  position: 'top-end',
			      		  icon: 'warning',
			      		  title: 'Debe completar todos los datos del formulario',
			      		  showConfirmButton: false,
			      		  timer: 2000
			      		})
			      	}else{
	    		  	  	cadena = 'tipoAtencionAgendada=' + tipoAtencionAgendada +
	    		  	  	         '&idDerivacion=' + idDerivacion +
	    		  	  	         '&idCitacion=' + idCitacion +
	    		  	  	         '&slAtiendePaciente=' + slAtiendePaciente +
	    		  	  	         '&slQuirurgico=' + slQuirurgico +
	    		  	  	         '&slCorrespondeCanasta=' + slCorrespondeCanasta +
	    		  	  	         '&rutaAdjuntaDocAtencion=' + rutaAdjuntaDocAtencion +
	    		  	  	         '&slComentarioAtiendePaciente=' + slComentarioAtiendePaciente; 

	    		  	  	$.ajax({
	    		  	  	  type: "POST",
	    		  	  	  url: "2.0/vistas/modulos/atenderPaciente/guardarAtencionPacienteOtraCita.php",
	    		  	  	  data: cadena,
	    		  	  	  success:function(r){
	    		  	  	  		$('#modalAtenderPaciente').modal('hide');
	    		  	  	        Swal.fire({
	    		  	  	          position: 'top-end',
	    		  	  	          icon: 'success',
	    		  	  	          title: 'Atención guardada con exito',
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
	    		  	  	        // 	setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1000);
	    		  	  	        // }
	    		  	  	        if (tipoUsuario == 6) {//tens
	    		  	  	        	setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioTens/inicioTens.php'); }, 1000);
	    		  	  	        }


	    		  	  	        //setTimeout(function(){ $('#contenido_principal').load('2.0/vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 2100);
	    		  	  	    }
	    		  	  	}); 
			      	}

			      }//fin otra consulta
			  
			}
	}

	function fnOpcionesAtiende(valor){
		tipoAtencionAgendada = $("#tipoAtencionAgendada").val();
		slAtiendePaciente = $("#slAtiendePaciente").val();

		if (tipoAtencionAgendada == 'primeraConsulta' && slAtiendePaciente == 'si') { 
			$("#dvQuirurgico").hide();
			$("#slQuirurgico").val('');
		}

		if (tipoAtencionAgendada == 'primeraConsulta' && slAtiendePaciente == 'no') { 
			$("#dvQuirurgico").hide();
			$("#slQuirurgico").val('');
		}


		if (tipoAtencionAgendada == 'segundaConsulta' && slAtiendePaciente == 'si') {
			$("#dvQuirurgico").show();
			$("#slQuirurgico").val('');
		}

		if (tipoAtencionAgendada == 'segundaConsulta' && slAtiendePaciente == 'no') {
			$("#dvQuirurgico").hide();
			$("#slQuirurgico").val('');
		}

	}
	function preguntarSiNoEliminaAdjuntoAtencion() {


				Swal.fire({
			  title: 'Estas Seguro?',
			  text: "No podras revertir la eliminación!",
			  icon: 'warning',
			  showCancelButton: true,
			  confirmButtonColor: '#3085d6',
			  cancelButtonColor: '#d33',
			  confirmButtonText: 'Si, Eliminar!'
			}).then((result) => {
			  if (result.isConfirmed) {
			  	fnQuitarEliminaAdjuntoAtencion()
			    Swal.fire(
			      'Eliminado!',
			      'Tu archivo fue eliminado.',
			      'success'
			    )
			  }
			})
		}
		
	function fnQuitarEliminaAdjuntoAtencion(){
		rutaAdjuntaDocAtencion = $("#hdRutaAdjuntaDocAtencion").val();
		cadena = 'rutaAdjuntaDocAtencion=' + rutaAdjuntaDocAtencion;
	  $.ajax({
	      type: "POST",
	      url: "2.0/vistas/bitacora/adjuntaDoc/docs/eliminaAdjuntoAtencion.php",
	      data: cadena,
	      success: function(r) {
	      	$("#hdRutaAdjuntaDocAtencion").val('');
	      	$("#dvMuestraPreDocAdjunto").hide();
	      	$("#dvMuestraBtnAdjuntarDoc").show();
	        //$('#dvTablaBitacora').load('2.0/vistas/bitacora/modals/tablaBitacora.php?idDerivacion=' + idDerivacion);
			
	      }
	  });

	}
	
	function fnMuestraAdjuntoAtencion(){

	rutaAdjuntaDocAtencion = $("#hdRutaAdjuntaDocAtencion").val();
	//alert(rutaAdjuntaDocAtencion);
	window.open('2.0/vistas/bitacora/adjuntaDoc/'+rutaAdjuntaDocAtencion, '_blank');
	//window.location.href = "2.0/vistas/bitacora/adjuntaDoc/"+rutaAdjuntaDocAtencion, "_blank";

	}
</script>



