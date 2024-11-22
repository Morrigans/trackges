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

$idUsuario = $_SESSION['idUsuario'];
$tipoUsuario = $_SESSION['tipoUsuario'];

//***********calculo 10 dias sobre hoy, para compararlo con la fecha cita y advertir si ajendan a mas de 10 dias, por el tema del cumplimiento de un indicador local*********
date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');
$diezDiasMas = date("Y/m/d",strtotime($hoy."+ 10 days")); 
//***************************************************************************************************************************************************************************

$usuario = $_SESSION['dni'];

$idDerivacion = $_REQUEST['idDerivacion'];


$query_qrBuscaCita = "SELECT * FROM $MM_oirs_DATABASE.events_pp WHERE ID_DERIVACION = '$idDerivacion' AND ESTADO_CITA='CITA' ";
$qrBuscaCita = $oirs->SelectLimit($query_qrBuscaCita) or die($oirs->ErrorMsg());
$trBuscaCita = $qrBuscaCita->RecordCount();

$idEvents = $qrBuscaCita->Fields('id');


$query_qrBuscaMaxi = "SELECT id FROM $MM_oirs_DATABASE.events_pp WHERE ID_DERIVACION = '$idDerivacion' AND ESTADO_CITA ='ATENDIDO'";
$qrBuscaMaxi = $oirs->SelectLimit($query_qrBuscaMaxi) or die($oirs->ErrorMsg());
$trBuscaMaxi = $qrBuscaMaxi->RecordCount();

//$maximo= $qrBuscaMaxi->Fields('maxi');

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idPaciente = $qrDerivacion->Fields('ID_PACIENTE');
$idLogin = $qrDerivacion->Fields('RUT_PRESTADOR');
$idDerivacionPp = $qrDerivacion->Fields('ID_DERIVACION_PP');

$query_qrBuscaDoc= "SELECT * FROM $MM_oirs_DATABASE.login WHERE ID = '$idLogin'";
$qrBuscaDoc = $oirs->SelectLimit($query_qrBuscaDoc) or die($oirs->ErrorMsg());
$totalRows_qrBuscaDoc = $qrBuscaDoc->RecordCount();

$nombreDr = $qrBuscaDoc->Fields('NOMBRE');

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">

        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Agendamiento de citas:</h2></div>

						<div class="col-md-6" id="dvInfoVentanasOpcionesAsignaCita"></div>	
						
						<div class="col-md-6">
							<div id="dvMuestraFrmAgendarCita">
									<div class="input-group mb-3 col-sm-12" id="dvMuestraMedicoTratante"><h6>Dr: <kbd><?php echo $nombreDr ?></kbd></h6></div>

									<div class="input-group mb-3 col-sm-12">
									    <div class="input-group-prepend">
									      <span class="input-group-text">Tipo atención</span>
									    </div>
									    <?php if($trBuscaMaxi==0){ ?>
									    	<input type="text" name="slTipoAtencionAgendamiento" id="slTipoAtencionAgendamiento" class="form-control input-sm" value="primeraConsulta" readonly>
									  	<?php } if($trBuscaMaxi==1){ ?>
									    	<input type="text" name="slTipoAtencionAgendamiento" id="slTipoAtencionAgendamiento" class="form-control input-sm" value="segundaConsulta" readonly>
									  	<?php } if($trBuscaMaxi>=2){ ?>
									  		<input type="text" name="slTipoAtencionAgendamiento" id="slTipoAtencionAgendamiento" class="form-control input-sm" value="otraConsulta" readonly>
									  	<?php } ?>
									    <!-- <select name="slTipoAtencionAgendamiento" id="slTipoAtencionAgendamiento" class="form-control input-sm">
									    	<option value="">Seleccione...</option>
									    	<option value="primeraConsulta">Primera Consulta</option>
									    	<option value="segundaConsulta">Segunda Consulta</option>
									    	<option value="otraConsulta">Otra Consulta</option>
									    </select> -->
									</div>

									<div class="input-group mb-3 col-sm-12">
									    <div class="input-group-prepend">
									      <span class="input-group-text">Fecha cita</span>
									    </div>
									    <input type="date" class="form-control input-sm" id="fechaAgendamientoEvents" onchange="fnFrenaDiezDias(this.value);">							    
									</div>

									<div class="input-group mb-3 col-sm-12">
									    <div class="input-group-prepend">
									      <span class="input-group-text">Hora cita</span>
									    </div>
									    <input type="time" class="form-control input-sm" id="horaAgendamientoEvents">							    
									</div>

									<div class="input-group mb-3 col-sm-12">
									    <div class="input-group-prepend">
									      <span class="input-group-text">Observación</span>
									    </div>
									    <!-- <input type="text" class="form-control input-sm" id="obsAgendamientoEvents"> -->
									    <textarea class="form-control" id="obsAgendamientoEvents" rows="2"></textarea>							    
									</div>

									<div id="dvTablaTeamGestion"></div>
							</div>
							<div id="dvMuestraCitaAgendada"  >
									<strong>Tipo cita:</strong> <span class="" id="dvMuestraTipoCita"></span></br>
									<strong>Médico:</strong> <span class="" id="dvMuestraMedTratante"></span></br>
									<strong>Fecha cita:</strong> <span class="" id="dvMuestraFechaCita"></span></br>
									<strong>Agendado por:</strong> <span class="" id="dvMuestraRutAgenda"></span></br></br>

									<button type="button" id="btnQuitaReagendaCita" class="btn btn-danger btn-block" onclick="fnQuitaReagendaCita('<?php echo $idDerivacion ?>')"><i class="fas fa-trash-alt"></i> Quitar y reagendar cita</button>

							</div>

							<div id="dvObsQuitar" class=" col-sm-12"  >
									Motivo eliminación: <textarea id="inpObsQuitar" name="inpObsQuitar"  placeholder="Indique el motivo de la eliminación, este comentario se vera reflejado en bitacora del paciente" class="form-control input-sm"></textarea>
								</br>
							

									<button type="button" id="btnObsQuitar" class="btn btn-danger" data-dismiss="modal" onclick="fnObsquitaCita('<?php echo $idEvents ?>','<?php echo $idDerivacion ?>','<?php echo $idDerivacionPp ?>')"><i class="fas fa-trash-alt"></i> Aceptar </button>
									<button type="button" id="btnObsQuitar" class="btn btn-default"  onclick="fnQuitaCancelar()"> Cancelar </button>

							</div>
						</div>

						

					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					    <div id="dvAccionesPreguardarCita">
					    	<button type="button" class="btn btn-success" onclick="fnGuardaAgendamientoCita('<?php echo $idDerivacion ?>','<?php echo $idDerivacionPp ?>','<?php echo $tipoUsuario ?>')">Guardar cita</button>
					    </div>
			  	</div>     
	  		</div>
	  		
	  		<input type="hidden" id="idDerivacionAsignaCita" value="<?php echo $idDerivacion ?>">
	  		<input type="hidden" id="hdTrBuscaCita" value="<?php echo $trBuscaCita ?>">
	  		<!-- guardo la fecha actual mas 10 dias para capturarla en la funcion fnFrenaDiezDias y compararla con la fecha de la cita -->
	  		<input type="hidden" id="hdDiezDiasMas" value="<?php echo $diezDiasMas ?>">
	  		<input type="hidden" id="hdIdEvents" value="">
	</body>
</html>

<script>
idDerivacion = $('#idDerivacionAsignaCita').val();	

$('#dvInfoVentanasOpcionesAsignaCita').load('vistas/modulos/infoVentanasOpcionesPp.php?idDerivacion='+idDerivacion);

	//alerto si la fecha de la primera cita que intento agendar supera los 10 dias
	function fnFrenaDiezDias(fechaCita){
		hdDiezDiasMas=$('#hdDiezDiasMas').val();
		tipoAtencion=$('#slTipoAtencionAgendamiento').val();

		var date1=new Date(fechaCita +' 00:00:00');
		var date2=new Date(hdDiezDiasMas +' 00:00:00'); 

		if (date1 > date2 && tipoAtencion == 'primeraConsulta') {
			Swal.fire({
	            position: 'top-end',
	            icon: 'error',
	            title: 'Atención, La fecha seleccionada supera los diez dias para la primera atención del paciente',
	            showConfirmButton: false,
	            timer: 5000
	        })
		}else{
			//poner el resto en caso que no solo sea una alerta, sino un freno definitivo
		}
	}


	$('#dvMuestraCitaAgendada').hide();
	idDerivacion=$('#idDerivacionAsignaCita').val();
	trBuscaCita=$('#hdTrBuscaCita').val();
	if(trBuscaCita>0){

		cadena = 'idDerivacion=' + idDerivacion;

		$.ajax({
      type: "POST",
      url: "vistas/modulos/pacientesPp/asignarCita/buscaCitaAgendada.php",
      data: cadena,
      dataType:"json",
      success:function(r){
      	$('#dvMuestraCitaAgendada').show();

        $("#dvMuestraMedTratante").html(r.nomPro);	
        $("#dvMuestraTipoCita").html(r.tipoAt);	
        $("#dvMuestraFechaCita").html(r.fechaCita);	
        $("#dvMuestraRutAgenda").html(r.nomAgen);	
        $("#hdIdEvents").val(r.idEvents);	
      }
    });
		$('#dvAccionesPreguardarCita').hide();
		$('#dvMuestraFrmAgendarCita').html("<h4>El paciente ya cuenta con una citación agendada</h4>");

	}
	//$("#dvTablaTeamGestion").load('vistas/modulos/asignarTeamGestion/tablaTeamGestion.php?idDerivacion='+idDerivacion);

function fnAsignaProfesional(){
    tipoEspecialidad = $("#slProfesionTeamGestion").val();
    cadena = 'tipoEspecialidad=' + tipoEspecialidad;
    $.ajax({
      type: "POST",
      url: "vistas/modulos/pacientesPp/asignarTeamGestion/buscaTipoProfesional.php",
      data: cadena,
      success:function(r){
        $("#slProfesionalesTeamAtencion").html(r);
        $('#btnGuardaTeamGestion').show();
      }
    }); 
}

function fnGuardaAgendamientoCita(idDerivacion,idDerivacionPp, tipoUsuario){
  var fechaAgendamiento = $("#fechaAgendamientoEvents").val();
  var horaAgendamiento = $("#horaAgendamientoEvents").val();
  var obsAgendamientoEvents = $("#obsAgendamientoEvents").val();
  var tipoAtencion = $("#slTipoAtencionAgendamiento").val();

  if(fechaAgendamiento =='' || tipoAtencion ==''){

		Swal.fire({
            position: 'top-end',
            icon: 'error',
            title: 'Debe completar fecha y tipo atención',
            showConfirmButton: false,
            timer: 2500
        })

  }else{

  		cadena = 'fechaAgendamiento=' + fechaAgendamiento +
           		'&idDerivacion=' + idDerivacion +
           		'&idDerivacionPp=' + idDerivacionPp +
           		'&obsAgendamientoEvents=' + obsAgendamientoEvents +
           		'&tipoAtencion=' + tipoAtencion +
           		'&horaAgendamiento=' + horaAgendamiento;

		  $.ajax({
		    type: "POST",
		    url: "vistas/modulos/pacientesPp/asignarCita/asignarCita.php",
		    data: cadena,
		    success:function(r){

		    			$('#modalAsignarCitaPp').modal('hide');
		          Swal.fire({
		            position: 'top-end',
		            icon: 'success',
		            title: 'Agendado correctamente',
		            showConfirmButton: false,
		            timer: 800
		          })
		       setTimeout(function (){ $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php'); }, 1000);


		          
		      }
		    }); 
  }
}


$('#dvObsQuitar').hide();

function fnQuitaReagendaCita(){
  $('#btnQuitaReagendaCita').hide();
  $('#dvObsQuitar').show();
}



function fnQuitaCancelar(){
  $('#btnQuitaReagendaCita').show();
  $('#dvObsQuitar').hide();
  $('#inpObsQuitar').val('');
}

function fnObsquitaCita(idEvents,idDerivacion,idDerivacionPp){
obsQuitarCita=$('#inpObsQuitar').val();
if (obsQuitarCita=='') {
	 Swal.fire({
		            position: 'top-end',
		            icon: 'error',
		            title: 'Favor ingrese motivo',
		            showConfirmButton: false,
		            timer: 2000
		          })

}else{
	   cadena = 'idEvents=' + idEvents +
            	'&obsQuitarCita=' + obsQuitarCita +
            	'&idDerivacionPp=' + idDerivacionPp +
            	'&idDerivacion=' + idDerivacion;
            
    $.ajax({
        type:"post",
        data:cadena,
        url:'vistas/modulos/pacientesPp/asignarCita/quitaCitaAgendada.php',
        success:function(r){
            if (r == 1) {
                 Swal.fire({
		            position: 'top-end',
		            icon: 'success',
		            title: 'Eliminado correctamente',
		            showConfirmButton: false,
		            timer: 1400
		          })
                 $('#inpObsQuitar').val('');
                $('#dvfrmAsignarCita').load('vistas/modulos/pacientesPp/asignarCita/frmAsignarCita.php?idDerivacion=' + idDerivacion);
               
                 if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
						setTimeout(function (){ $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php'); }, 1);
					}
					if (tipoUsuario == 4) {// administrativa
						setTimeout(function (){ $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php'); }, 1);
					}
					// if (tipoUsuario == 5) {//medico
					// 	setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
					// }
					if (tipoUsuario == 6) {//tens
						setTimeout(function (){ $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php'); }, 1);
					}
              
            } else {
            }
            
        }
    });



}
}

</script> 



<!-- swal({
  title: "!Eliminar Citacion!",
  text: "Motivo de la Eliminacion:",
  type: "input",
  showCancelButton: true,
  closeOnConfirm: false,
  // inputPlaceholder: "Write something"
}, function (inputValue) {
  if (inputValue === false) return false;
  if (inputValue === "") {
    swal.showInputError("!Debe ingresar un Motivo!");
    return false
}

  
	cadena = 'id='+id +
			'&inputValue='+inputValue;
$.ajax({

	 url: 'modals/ajaxModalEdicionRecepcionista/eliminaAtencionModalRecepcionista.php',
	 type: "POST",
	 data: cadena,
	 dataType: 'json',
	 success: function(arr) {
			if(arr !=''){
				var fechaNueva=arr.fechaNueva;
	    		var horaNueva=arr.horaNueva;
				$('#calendar').fullCalendar('removeEvents',id);
				swal("Genial!", "Se ha eliminado la citación", "success");
				
			}
		}
	});
  


Swal.fire({
  title: "!Eliminar !",
  text: "Motivo de la Eliminacion:",
  input: "text",


showCancelButton: true ,
confirmButtonColor: 'green',

  inputPlaceholder: "",
}, function (inputValue) {
  if (inputValue === false) return false;
  if (inputValue === "") {
    swal.showInputError("!Debe ingresar un Motivo!");
    return false
}

  
	cadena = 'id='+id +
			'&inputValue='+inputValue;
$.ajax({

	 url: 'modals/ajaxModalEdicionRecepcionista/eliminaAtencionModalRecepcionista.php',
	 type: "POST",
	 data: cadena,
	 dataType: 'json',
	 success: function(arr) {
			if(arr !=''){
				var fechaNueva=arr.fechaNueva;
	    		var horaNueva=arr.horaNueva;
				$('#calendar').fullCalendar('removeEvents',id);
				swal("Genial!", "Se ha eliminado la citación", "success");
				
			}
		}
	});
  
});}); -->