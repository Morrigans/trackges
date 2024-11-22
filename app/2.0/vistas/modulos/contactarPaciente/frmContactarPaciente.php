<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

//require_once('audio/modalAudios.php');

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

$query_qrAsignarPrestador= "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO = '5'";
$qrAsignarPrestador = $oirs->SelectLimit($query_qrAsignarPrestador) or die($oirs->ErrorMsg());
$totalRows_qrAsignarPrestador = $qrAsignarPrestador->RecordCount();

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

//consulta que busca canasta inicial de la derivacion para pasarla a la API de prestador para insertarse en derivaciones_canastas de prestador
$query_qrDerivacionCanastaInicial= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION = '$idDerivacion' and INICIAL ='si'";
$qrDerivacionCanastaInicial = $oirs->SelectLimit($query_qrDerivacionCanastaInicial) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanastaInicial = $qrDerivacionCanastaInicial->RecordCount();

$query_qrProfesion= "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '4' OR ID = '6'"; 
$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
$totalRows_qrProfesion = $qrProfesion->RecordCount();

$query_qrBuscaAlarmas = "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE PROGRAMADO = 'si' AND FECHA_PROGRAMACION <= '$hoy' AND SESION = '$usuario' order by FECHA_PROGRAMACION desc";
$qrBuscaAlarmas = $oirs->SelectLimit($query_qrBuscaAlarmas) or die($oirs->ErrorMsg());
$totalRows_qrBuscaAlarmas = $qrBuscaAlarmas->RecordCount();


?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Contactar paciente:</h2></div>

						<div class="col-md-6" id="dvInfoVentanasOpcionesContactarPaciente"></div>

						<div class="col-md-6">
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Tipo contacto</span>
							    </div>
							    <select name="slTipoContacto" id="slTipoContacto" class="form-control input-sm" onchange="fnNumeroContactos(this.value,'<?php echo $idDerivacion ?>')">
							        <option value="">Seleccione...</option>
							        <option value="Primera consulta">Primera consulta</option>
							        <option value="Segunda consulta">Segunda consulta</option>
							        <option value="Otro contacto">Otro contacto</option>
							    </select>
							</div>

							<span><div class="col-md-12" id="dvNumeroContactos"></div></span>

							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Medio contacto</span>
							    </div>
							    <select name="slMedioContacto" id="slMedioContacto" class="form-control input-sm" onchange="fnMuestraGrabarLlamada(this.value)"> 
							    		<option value="">Seleccione...</option>
							    		<option value="Telefono">Telefono</option>
							    		<option value="Mail">Mail</option>
							    </select>
							</div>

							<!-- grabar audio**************************************************************************************************************************** -->
							<div class="col-sm-6" id="grabarLlamada">
							  <div class="form-group">
							      <a href="#" data-toggle="modal" class="btn btn-info btn-block" data-target="#modalAudios" onclick="$('#origen').val('contactoPaciente');$('#idDerivacionAudio').val(<?php echo $idDerivacion ?>)"><i class="fas fa-microphone"></i> Grabar llamada Telefonica</a>
							  </div>
							</div>

							<div class="col-sm-6">
							  <div class="form-group">
							      <a href="#" style="display: none" data-toggle="modal" id="escucharLlamada" class="btn btn-success btn-block" onclick="$('#dvFrmPlayAudios').load('vistas/modulos/contactarPaciente/frmPlayAudios.php?linkAudio='+$('#rutaAudio').val()); $('#escucharLlamada').hide();$('#pausarLlamada').show()"><i class="fas fa-play"></i> Escuchar llamada Telefonica</a>
							      <a href="#" style="display: none" data-toggle="modal" id="pausarLlamada" class="btn btn-warning btn-block" onclick="document.getElementById('audioContacto').pause();$('#pausarLlamada').hide();$('#escucharLlamada').show()"><i class="fas fa-stop"></i> Detener</a>
							      <button class="btn btn-danger btn-block" id="quitarRutaAudio" onclick="preguntarSiNoEliminaAudioContatoPaciente()"><span class=" fas fa-trash-alt"></span> Quitar llamada telefonica</button>
							  </div>
							</div>

							 

							<!-- se inserta ruta del audio grabado desde guardarAudios que esta en carpeta bitacora/modals para ser guardada en la insercion del contacto -->
							<input type="hidden" id="rutaAudio">	 
							<!-- ************************************************************************************************************************************************* -->

							<!-- Inicio adjuntar archivo al contactar paciente ****************************************************************************************************-->
							<div class="col-sm-6" id="dvMuestraBtnAdjuntarArchivoContactarPaciente">
							  <div class="form-group">
							      <a href="#" class="btn btn-success btn-block" onclick="$('#dvCargaAdjuntarContactarPaciente').load('vistas/bitacora/adjuntaDoc/adjuntaDocumentoContactarPaciente.php?idDerivacion='+<?php echo $idDerivacion?>), $('#dvCargaAdjuntarContactarPaciente').show();"><i class="fas fa-paperclip"></i> Adjuntar Archivo</a>
							  </div>
							</div>

							<input type="hidden" name="hdRutaAdjuntaDocContactarPaciente" id="hdRutaAdjuntaDocContactarPaciente" class="form-control input-sm">

							<div id="dvMuestraPreDocAdjuntoContactarPaciente"  class="col-sm-6">
								<a target="_blank" class="btn btn-success btn-block" href="#" onclick='fnMuestraAdjuntoContactarPaciente()' ><i class="far fa-file-pdf"></i> Ver archivo adjunto</a>
								<button class="btn btn-danger btn-block" onclick="preguntarSiNoEliminaAdjuntoContactarPaciente()"><span class=" fas fa-trash-alt"></span> Quitar archivo adjunto</button>
							</div>

							<div id="dvCargaAdjuntarContactarPaciente"></div>
							<!-- Fin adjuntar archivo al contactar paciente *******************************************************************************************************-->
							<br>
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Nota contacto</span>
							    </div>
							    <textarea id="txaNotaContacto" name="txaNotaContacto" class="form-control input-sm"></textarea>
							</div>
							
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button> 
					    <button id="btnGuardaContacto" type="button" class="btn btn-success" onclick="fnGuardaContacto('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>')">Guardar contacto con paciente</button>
			  	</div>     
	  		</div>
	  		<input type="hidden" id="idDerivacion" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>

	idDerivacion=$('#idDerivacion').val();
	$('#dvInfoVentanasOpcionesContactarPaciente').load('vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

$("#grabarLlamada").hide();// oculto el boton de llamada hasta que se seleccione medio de contacto telefono
$("#quitarRutaAudio").hide();
$("#dvMuestraPreDocAdjuntoContactarPaciente").hide(); //Se oculta la Previsualizacion de adjuntar archivo contactar paciente

//muestra boton de grabar llamada al seleccionar opcion medio de contacto telefono.
function fnMuestraGrabarLlamada(tipoContacto){
	if (tipoContacto == 'Telefono') {
		$("#grabarLlamada").show();
	}else{
		$("#grabarLlamada").hide();
		$("#escucharLlamada").hide();
    	$("#pausarLlamada").hide();
    	$("#quitarRutaAudio").hide();
	}
}
 
//guarda el contacto con el paciente, incluyendo la ruta del audio grabado
function fnGuardaContacto(idDerivacion,tipoUsuario){
  slTipoContacto = $("#slTipoContacto").val();
  slMedioContacto = $('#slMedioContacto').val();
  txaNotaContacto = $("#txaNotaContacto").val();
  rutaAudio = $("#rutaAudio").val();
  rutaAdjuntaDocContactarPaciente = $("#hdRutaAdjuntaDocContactarPaciente").val();

	//valida que este ingresado el medio de contacto
  if (slMedioContacto == '') {
  	Swal.fire({
  	  position: 'top-end',
  	  icon: 'error',
  	  title: 'Debe completar la información',
  	  showConfirmButton: false,
  	  timer: 2000
  	})
  }else{
  	 cadena = 'slTipoContacto=' + slTipoContacto +
           '&idDerivacion=' + idDerivacion +
           '&slMedioContacto=' + slMedioContacto +
           '&txaNotaContacto=' + txaNotaContacto +
           '&rutaAdjuntaDocContactarPaciente=' + rutaAdjuntaDocContactarPaciente +
           '&rutaAudio=' + rutaAudio; 
  $.ajax({
    type: "POST",
    url: "vistas/modulos/contactarPaciente/contactarPaciente.php",
    data: cadena,
    success:function(r){
    		fnNumeroContactos(slTipoContacto, idDerivacion);//recargo la tabla de contactos
    		$('#slMedioContacto').val('');
    		$("#txaNotaContacto").val('');
    		$("#rutaAudio").val('');
    		$("#hdRutaAdjuntaDocContactarPaciente").val('');
    		$("#escucharLlamada").hide();
    		$("#pausarLlamada").hide();
    		$("#grabarLlamada").hide();
    		$("#quitarRutaAudio").hide();
    		$("#hdRutaAdjuntaDocContactarPaciente").val('');
      	$("#dvMuestraPreDocAdjuntoContactarPaciente").hide();
      	$("#dvMuestraBtnAdjuntarArchivoContactarPaciente").show();
          Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Contacto guardado con exito',
            showConfirmButton: false,
            timer: 800 
          })
      }
    }); 
  }
}

//muestra la tabla de intentos de contacto realizados
function fnNumeroContactos(tipoContacto, idDerivacion){

  cadena = 'tipoContacto=' + tipoContacto +
           '&idDerivacion=' + idDerivacion; 
  $.ajax({
    type: "POST",
    url: "vistas/modulos/contactarPaciente/numeroContactos.php",
    data: cadena,
    success:function(r){
          $("#dvNumeroContactos").html(r);
      }
    }); 
}

function preguntarSiNoEliminaAdjuntoContactarPaciente() {

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
		  	fnQuitarEliminaAdjuntoContactarPaciente()
		    Swal.fire(
		      'Eliminado!',
		      'Tu archivo fue eliminado.',
		      'success'
		    )
		  }
		})
}

function preguntarSiNoEliminaAudioContatoPaciente() {

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
		  	fnQuitarEliminaAudioContactarPaciente()
		    Swal.fire(
		      'Eliminado!',
		      'Tu archivo fue eliminado.',
		      'success'
		    )
		  }
		})
}
	
function fnQuitarEliminaAdjuntoContactarPaciente(){
	rutaAdjuntaDocContactarPaciente = $("#hdRutaAdjuntaDocContactarPaciente").val();
	cadena = 'rutaAdjuntaDocAtencion=' + rutaAdjuntaDocContactarPaciente; //mantengo el nombre "rutaAdjuntaDocAtencion" para reutilizar archivo de eliminacion de ruta
  $.ajax({
      type: "POST",
      url: "vistas/bitacora/adjuntaDoc/docs/eliminaAdjuntoAtencion.php",
      data: cadena,
      success: function(r) {
      	$("#hdRutaAdjuntaDocContactarPaciente").val('');
      	$("#dvMuestraPreDocAdjuntoContactarPaciente").hide();
      	$("#dvMuestraBtnAdjuntarArchivoContactarPaciente").show();
      }
  });

}

function fnQuitarEliminaAudioContactarPaciente(){
	rutaAudio = $("#rutaAudio").val();
	cadena = 'rutaAudio=' + rutaAudio;
  $.ajax({
      type: "POST",
      url: "vistas/bitacora/modals/audios/eliminaAudioContactoPaciente.php",
      data: cadena,
      success: function(r) {
      	$("#rutaAudio").val('');
      	$("#grabarLlamada").show();
				$("#escucharLlamada").hide();
    		$("#pausarLlamada").hide();
    		$("#quitarRutaAudio").hide();
      }
  });

}

function fnMuestraAdjuntoContactarPaciente(){
		rutaAdjuntaDocContactarPaciente = $("#hdRutaAdjuntaDocContactarPaciente").val();
		window.open('vistas/bitacora/adjuntaDoc/'+rutaAdjuntaDocContactarPaciente, '_blank');
}
</script>



