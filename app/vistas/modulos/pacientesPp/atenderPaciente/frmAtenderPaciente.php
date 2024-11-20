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

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idPaciente = $qrDerivacion->Fields('ID_PACIENTE');
$idDerivacionPp = $qrDerivacion->Fields('ID_DERIVACION_PP');

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$idPaciente'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$codRutPac = $qrPaciente->Fields('COD_RUTPAC');

$codTipoPatologia = $qrDerivacion->Fields('CODIGO_TIPO_PATOLOGIA');

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia WHERE ID_TIPO_PATOLOGIA = '$codTipoPatologia'";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

$codPatologia = $qrDerivacion->Fields('ID_PATOLOGIA');

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia_pp WHERE ID_PATOLOGIA = '$codPatologia'";
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

$query_qrAsignarPrestador= "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO = '5'";
$qrAsignarPrestador = $oirs->SelectLimit($query_qrAsignarPrestador) or die($oirs->ErrorMsg());
$totalRows_qrAsignarPrestador = $qrAsignarPrestador->RecordCount();

$codConvenio = $qrDerivacion->Fields('ID_CONVENIO');

$query_qrConvenio= "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$codConvenio'";
$qrConvenio = $oirs->SelectLimit($query_qrConvenio) or die($oirs->ErrorMsg());
$totalRows_qrConvenio = $qrConvenio->RecordCount();

$query_qrDerivacionCanasta= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
$qrDerivacionCanasta = $oirs->SelectLimit($query_qrDerivacionCanasta) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanasta = $qrDerivacionCanasta->RecordCount();

$query_qrDerivacionEtapa= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_etapas_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacionEtapa = $oirs->SelectLimit($query_qrDerivacionEtapa) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionEtapa = $qrDerivacionEtapa->RecordCount();

//consulta que busca canasta inicial de la derivacion para pasarla a la API de prestador para insertarse en derivaciones_canastas de prestador
$query_qrDerivacionCanastaInicial= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_DERIVACION = '$idDerivacion' and INICIAL ='si'";
$qrDerivacionCanastaInicial = $oirs->SelectLimit($query_qrDerivacionCanastaInicial) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanastaInicial = $qrDerivacionCanastaInicial->RecordCount();

$query_qrProfesion= "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '4' OR ID = '6'"; 
$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
$totalRows_qrProfesion = $qrProfesion->RecordCount();



//busca agenda en estado cita para ver si es primera o segunda consulta*******************************************************************
$query_qrEvents = "SELECT * FROM $MM_oirs_DATABASE.events_pp WHERE ID_DERIVACION = '$idDerivacion' AND ESTADO_CITA = 'CITA'";
$qrEvents = $oirs->SelectLimit($query_qrEvents) or die($oirs->ErrorMsg());
$totalRows_qrEvents = $qrEvents->RecordCount();

$tipoAtencion = $qrEvents->Fields('TIPO_ATENCION');//obtengo el tipo de atencion
$idCitacion = $qrEvents->Fields('id');//obtengo el id de la cita

$query_qrEventsTipoAtencion = "SELECT * FROM $MM_oirs_DATABASE.events_pp WHERE TIPO_ATENCION = '$tipoAtencion'";
$qrEventsTipoAtencion = $oirs->SelectLimit($query_qrEventsTipoAtencion) or die($oirs->ErrorMsg());
$totalRows_qrEventsTipoAtencion = $qrEventsTipoAtencion->RecordCount();
//*****************************************************************************************************************************************

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Atender paciente:</h2></div>

						<div class="col-md-6" id="dvInfoVentanasOpcionesAtenderPacientePp"></div>

						<div id="dvFrmAtenderPaciente" class="col-md-6">
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Tipo atención</span>
							    </div>
							    <input type="text" name="tipoAtencionAgendadaPp" id="tipoAtencionAgendadaPp" class="form-control input-sm" value="<?php echo $qrEventsTipoAtencion->Fields('TIPO_ATENCION'); ?>" readonly>
							</div>

							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Atiende</span>
							    </div>
							    <select name="slAtiendePacientePp" id="slAtiendePacientePp" class="form-control input-sm" onchange="fnOpcionesAtiende(this.value)">
							        <option value="">Seleccione...</option>
							        <option value="no">No</option>
							        <option value="si">Si</option>
							       
							    </select>
							</div>

							<div id="dvQuirurgicoPp" class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">¿Quirúrgica?</span>
							    </div>
							    <select name="slQuirurgicoPp" id="slQuirurgicoPp" class="form-control input-sm">
							        <option value="">Seleccione...</option>
							        <option value="no">No</option>
							        <option value="si">Si</option>
							       
							    </select>
							</div>

							<div id="dvCorrespondeCanasta" class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">¿Corresponde Canasta?</span>
							    </div>
							    <select name="slCorrespondeCanastaPp" id="slCorrespondeCanastaPp" class="form-control input-sm">
							        <option value="">Seleccione...</option>
							        <option value="no">No</option>
							        <option value="si">Si</option>
							       
							    </select>
							</div>

							<div class="input-group mb-3 col-sm-12">
							    <textarea name="slComentarioAtiendePacientePp" id="slComentarioAtiendePacientePp" cols="11" rows="6" class="form-control input-sm" placeholder="Indique un comentario de la atención, este comentario se vera reflejado en la bitacora del paciente"></textarea>
							</div>
							<input type="hidden" name="hdRutaAdjuntaDocAtencion" id="hdRutaAdjuntaDocAtencion" class="form-control input-sm">

							
							<div id="dvMuestraBtnAdjuntarDoc">
								<a href="#" onclick="$('#dvCargaAdjuntarAtiendePaciente').load('vistas/modulos/pacientesPp/bitacora/adjuntaDoc/adjuntaDocumentoAtencionPaciente.php?idDerivacion='+<?php echo $idDerivacion?>), $('#dvCargaAdjuntarAtiendePaciente').show();"><span class="badge badge-warning"><i class="fas fa-paperclip"></i>Adjuntar documento</span></a>
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
					    <button type="button" class="btn btn-success" onclick="fnGuardaAtencionPacientePp('<?php echo $idDerivacion ?>','<?php echo $idDerivacionPp ?>','<?php echo $idCitacion ?>','<?php echo $tipoUsuario ?>')">Guardar atención</button>
			  	</div>     
	  		</div>
	  		
	  		<input type="hidden" id="hdIdDerivacionAtenderPacientePp" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
	$("#dvQuirurgicoPp").hide();
	$("#dvCorrespondeCanasta").hide();
	$("#dvMuestraPreDocAdjunto").hide();

	idDerivacion = $('#hdIdDerivacionAtenderPacientePp').val();	
	$('#dvInfoVentanasOpcionesAtenderPacientePp').load('vistas/modulos/infoVentanasOpcionesPp.php?idDerivacion='+idDerivacion);

	tipoAtencionAgendada = $("#tipoAtencionAgendadaPp").val();


	if (tipoAtencionAgendada == '') {
		$("#dvFrmAtenderPaciente").html('El paciente no tiene citas agendadas');

	}else{
		function fnGuardaAtencionPacientePp(idDerivacion,idDerivacionPp,idCitacion,tipoUsuario){
			slQuirurgico = $("#slQuirurgicoPp").val();
			  tipoAtencionAgendada = $("#tipoAtencionAgendadaPp").val(); 
			  slAtiendePaciente = $('#slAtiendePacientePp').val();
			  slComentarioAtiendePaciente = $("#slComentarioAtiendePacientePp").val();
			  
			  slCorrespondeCanasta = $("#slCorrespondeCanastaPp").val();
			  
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
					  	if (slAtiendePaciente == 'si' && slCorrespondeCanasta == '') {
					  		Swal.fire({
					  		  position: 'top-end',
					  		  icon: 'warning',
					  		  title: 'Debe completar todos los datos del formularios',
					  		  showConfirmButton: false,
					  		  timer: 2000
					  		})
					  	}else{
					  	  	cadena = 'tipoAtencionAgendada=' + tipoAtencionAgendada +
					  	  	         '&idDerivacion=' + idDerivacion +
					  	  	         '&idDerivacionPp=' + idDerivacionPp +
					  	  	         '&idCitacion=' + idCitacion +
					  	  	         '&slAtiendePaciente=' + slAtiendePaciente +
					  	  	         '&slCorrespondeCanasta=' + slCorrespondeCanasta +
					  	  	         '&rutaAdjuntaDocAtencion=' + rutaAdjuntaDocAtencion +
					  	  	         '&slComentarioAtiendePaciente=' + slComentarioAtiendePaciente; 

						  	  	$.ajax({
						  	  	  type: "POST",
						  	  	  url: "vistas/modulos/pacientesPp/atenderPaciente/guardarAtencionPacientePrimeraCita.php",
						  	  	  data: cadena,
						  	  	  success:function(r){

						  	  	  	$('#modalAtenderPacientePp').modal('hide');

					  	  	        Swal.fire({
					  	  	          position: 'top-end',
					  	  	          icon: 'success',
					  	  	          title: 'Atención guardada con exito',
					  	  	          showConfirmButton: false,
					  	  	          timer: 800
					  	  	        })

									setTimeout(function (){ $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php'); }, 1000);
								

					  	  	    }

					  	  	}); 
					  	}
			  	}

			  }//fin primera consulta

			    if (tipoAtencionAgendada == 'segundaConsulta') {
			    	if (slAtiendePaciente == '') {
			    		Swal.fire({
			    		  position: 'top-end',
			    		  icon: 'warning',
			    		  title: 'Debe completar todos los datos del formulario 2',
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
			  		  	  	         '&idDerivacionPp=' + idDerivacionPp +
			  		  	  	         '&idCitacion=' + idCitacion +
			  		  	  	         '&slAtiendePaciente=' + slAtiendePaciente +
			  		  	  	         '&slQuirurgico=' + slQuirurgico +
			  		  	  	         '&slCorrespondeCanasta=' + slCorrespondeCanasta +
			  		  	  	         '&rutaAdjuntaDocAtencion=' + rutaAdjuntaDocAtencion +
			  		  	  	         '&slComentarioAtiendePaciente=' + slComentarioAtiendePaciente; 

			  		  	  	$.ajax({
			  		  	  	  type: "POST",
			  		  	  	  url: "vistas/modulos/pacientesPp/atenderPaciente/guardarAtencionPacienteSegundaCita.php",
			  		  	  	  data: cadena,
			  		  	  	  success:function(r){
			  		  	  	  		$('#modalAtenderPacientePp').modal('hide');
			  		  	  	        Swal.fire({
			  		  	  	          position: 'top-end',
			  		  	  	          icon: 'success',
			  		  	  	          title: 'Atención guardada con exito',
			  		  	  	          showConfirmButton: false,
			  		  	  	          timer: 800
			  		  	  	        })

			  		  	  	       setTimeout(function (){ $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php'); }, 1000);
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
	    		  	  	         '&idDerivacionPp=' + idDerivacionPp +
	    		  	  	         '&idCitacion=' + idCitacion +
	    		  	  	         '&slAtiendePaciente=' + slAtiendePaciente +
	    		  	  	         '&slQuirurgico=' + slQuirurgico +
	    		  	  	         '&slCorrespondeCanasta=' + slCorrespondeCanasta +
	    		  	  	         '&rutaAdjuntaDocAtencion=' + rutaAdjuntaDocAtencion +
	    		  	  	         '&slComentarioAtiendePaciente=' + slComentarioAtiendePaciente; 



	    		  	  	$.ajax({
	    		  	  	  type: "POST",
	    		  	  	  url: "vistas/modulos/pacientesPp/atenderPaciente/guardarAtencionPacienteOtraCita.php",
	    		  	  	  data: cadena,
	    		  	  	  success:function(r){
	    		  	  	  		$('#modalAtenderPacientePp').modal('hide');
	    		  	  	        Swal.fire({
	    		  	  	          position: 'top-end',
	    		  	  	          icon: 'success',
	    		  	  	          title: 'Atención guardada con exito',
	    		  	  	          showConfirmButton: false,
	    		  	  	          timer: 800
	    		  	  	        })

	    		  	  	        setTimeout(function (){ $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php'); }, 1000);
	    		  	  	    }
	    		  	  	}); 
			      	}

			      }//fin otra consulta
			  
			}
	}

	function fnOpcionesAtiende(valor){
		tipoAtencionAgendada = $("#tipoAtencionAgendadaPp").val();
		slAtiendePaciente = $("#slAtiendePacientePp").val();

		if (tipoAtencionAgendada == 'primeraConsulta' && slAtiendePaciente == 'si') { 
			$("#dvQuirurgicoPp").hide();
			$("#dvCorrespondeCanasta").show();
			$("#slQuirurgicoPp").val('');
			$("#slCorrespondeCanastapp").val('');
		}

		if (tipoAtencionAgendada == 'primeraConsulta' && slAtiendePaciente == 'no') { 
			$("#dvQuirurgicoPp").hide();
			$("#dvCorrespondeCanasta").hide();
			$("#slQuirurgicoPp").val('');
			$("#slCorrespondeCanastaPp").val('');
		}


		if (tipoAtencionAgendada == 'segundaConsulta' && slAtiendePaciente == 'si') {
			$("#dvQuirurgicoPp").show();
			$("#dvCorrespondeCanasta").hide();
			$("#slQuirurgicoPp").val('');
		}

		if (tipoAtencionAgendada == 'segundaConsulta' && slAtiendePaciente == 'no') {
			$("#dvQuirurgicoPp").hide();
			$("#dvCorrespondeCanasta").hide();
			$("#slQuirurgicoPp").val('');
			$("#slCorrespondeCanastaPp").val('');
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
	      url: "vistas/bitacora/adjuntaDoc/docs/eliminaAdjuntoAtencion.php",
	      data: cadena,
	      success: function(r) {
	      	$("#hdRutaAdjuntaDocAtencion").val('');
	      	$("#dvMuestraPreDocAdjunto").hide();
	      	$("#dvMuestraBtnAdjuntarDoc").show();
	        //$('#dvTablaBitacora').load('vistas/bitacora/modals/tablaBitacora.php?idDerivacion=' + idDerivacion);
			
	      }
	  });

	}
	
	function fnMuestraAdjuntoAtencion(){

	rutaAdjuntaDocAtencion = $("#hdRutaAdjuntaDocAtencion").val();
	//alert(rutaAdjuntaDocAtencion);
	window.open('vistas/bitacora/adjuntaDoc/'+rutaAdjuntaDocAtencion, '_blank');
	//window.location.href = "vistas/bitacora/adjuntaDoc/"+rutaAdjuntaDocAtencion, "_blank";

	}
</script>



