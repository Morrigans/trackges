<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$codRutPac = $_REQUEST['rutPac'];

$query_qrRegion = "SELECT * FROM $MM_oirs_DATABASE.regiones";
$qrRegion = $oirs->SelectLimit($query_qrRegion) or die($oirs->ErrorMsg());
$totalRows_qrRegion = $qrRegion->RecordCount();

$query_qrPrevision = "SELECT * FROM $MM_oirs_DATABASE.prevision";
$qrPrevision = $oirs->SelectLimit($query_qrPrevision) or die($oirs->ErrorMsg());
$totalRows_qrPrevision = $qrPrevision->RecordCount();
?>
<form id="frmCreaPaciente">
	<div class="card card-info">
	  <div class="card-header">
	    <h3 class="card-title">Datos de paciente</h3>
	    <div class="card-tools">
	      <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
	        <i class="fas fa-minus"></i>
	      </button>
	      <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
	        <i class="fas fa-times"></i>
	      </button>
	    </div>
	  </div>
		  <div class="card-body">
			  	<div class="row">
				    <div class="input-group mb-3 col-sm-3">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Rut Paciente</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="rutPaciente" id="rutPaciente" value="<?php echo $codRutPac ?>" onchange="fnBuscaRutPacienteExistente()" />
				    </div>

				    <div class="input-group mb-3 col-sm-3">
				        <button type="button" class="btn btn-info" id="btnBuscaPacienteParaAgendar" onclick="fnBuscaRutPacienteExistente()"><i class="fas fa-search"></i> Iniciar Registro</button>
				    </div>

				    <div class="input-group mb-3 col-sm-5">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Nombre Paciente</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="nombrePaciente" id="nombrePaciente"/>
				    </div>
				    <div class="input-group mb-3 col-sm-4">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Sexo Paciente</span>
				      </div>
				        <select class="form-control input-sm" name="sexoPaciente" id="sexoPaciente">
				          <option value="">Seleccione...</option>
				          <option value="F">Femenino</option>	
				          <option value="M">Masculino</option>	
				      	</select>
				    </div>
				    
				    <div class="input-group mb-3 col-sm-3">
				    	<div class="input-group-prepend">
				    	  <span class="input-group-text">Fecha Nacimiento</span>
				    	</div>
				    	    <input type='text' class="form-control input-sm" name="nacimientoPaciente" id="nacimientoPaciente" data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-mask/>
				    </div>
				    


				    <div class="input-group mb-3 col-sm-4">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Telefono</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="fonoPaciente" id="fonoPaciente"/>
				    </div>
				    <div class="input-group mb-3 col-sm-4">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Correo Electrónico</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="mailPaciente" id="mailPaciente"/>
				    </div>
				    <div class="input-group mb-3 col-sm-4">
				        <div class="input-group-prepend">
				          <span class="input-group-text">Región</span>
				        </div>
				        <select class="form-control input-sm" name="regionPaciente" id="regionPaciente" onchange="fnBuscaDomicilioProvincia()">
				          <option value="">Seleccione...</option>
				          <?php while (!$qrRegion->EOF) {?>
				            <option value="<?php echo $qrRegion->Fields('region_id') ?>"><?php echo $qrRegion->Fields('region_ordinal') ?> <?php echo utf8_encode($qrRegion->Fields('region_nombre')) ?></option>
				          <?php $qrRegion->MoveNext(); } ?>
				      	</select>
				    </div>
				    <div class="input-group mb-3 col-sm-4">
				        <div class="input-group-prepend">
				          <span class="input-group-text">Provincia</span>
				        </div>
				        <select class="form-control input-sm" name="provinciaPaciente" id="provinciaPaciente" onchange="fnBuscaDomicilioComuna()">
				          
				        </select>
				    </div>
				    <div class="input-group mb-3 col-sm-4">
				        <div class="input-group-prepend">
				          <span class="input-group-text">Comuna Domicilio</span>
				        </div>
				        <select class="form-control input-sm" name="comunaPaciente" id="comunaPaciente" data-placeholder="Seleccione la comuna donde vive">
				          
				        </select>
				    </div>
				    <div class="input-group mb-3 col-sm-4">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Dirección</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="direccionPaciente" id="direccionPaciente"/>
				    </div>

				    <div class="input-group mb-3 col-sm-8">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Ocupación</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="ocupacionPaciente" id="ocupacionPaciente"/>
				    </div>

				    <div class="input-group mb-3 col-sm-4">
				        <div class="input-group-prepend">
				          <span class="input-group-text">Previsión</span>
				        </div>
				        <select class="form-control input-sm" name="previsionPaciente" id="previsionPaciente">
				          <option value="">Seleccione...</option>
				          <?php while (!$qrPrevision->EOF) {?>
				            <option value="<?php echo $qrPrevision->Fields('ID') ?>"><?php echo utf8_encode($qrPrevision->Fields('PREVISION')) ?></option>
				          <?php $qrPrevision->MoveNext(); } ?>
				      	</select>
				    </div>

				    <div class="input-group mb-3 col-sm-4">
				        <div class="input-group-prepend">
				          <span class="input-group-text">Plan de salud</span>
				        </div>
				        <input type='text' class="form-control input-sm" name="planSaludPaciente" id="planSaludPaciente"/>
				    </div>

				    <div class="input-group mb-3 col-sm-4">
				        <div class="input-group-prepend">
				          <span class="input-group-text">Seguro complementario</span>
				        </div>
				        <select class="form-control input-sm" name="seguroComplementarioPaciente" id="seguroComplementarioPaciente">
				          <option value="">Seleccione...</option>
				          <option value="No">No</option>
				          <option value="Si">Si</option>
				      	</select>
				    </div>

				    <div class="input-group mb-3 col-sm-4">
				        <div class="input-group-prepend">
				          <span class="input-group-text">Compañia de seguro</span>
				        </div>
				        <input type='text' class="form-control input-sm" name="companiaSeguroPaciente" id="companiaSeguroPaciente"/>
				    </div>
			    
				</div>
		   </div>
		   <div class="card-footer">
			    <button type="submit" class="btn btn-info">Guardar Datos Paciente</button>
			</div>
	</div>
</form>
<br>
   <div id="tablaPacientes"></div>

<script type="text/javascript">
	$('#nombrePaciente').prop( "disabled", true );
	$('#sexoPaciente').prop( "disabled", true );
	$('#nacimientoPaciente').prop( "disabled", true );
	$('#fonoPaciente').prop( "disabled", true );
	$('#direccionPaciente').prop( "disabled", true );
	$('#comunaPaciente').prop( "disabled", true );
	$('#provinciaPaciente').prop( "disabled", true );
	$('#regionPaciente').prop( "disabled", true );
	$('#mailPaciente').prop( "disabled", true );
	$('#previsionPaciente').prop( "disabled", true );
	$('#ocupacionPaciente').prop( "disabled", true );
	$('#planSaludPaciente').prop( "disabled", true );
	$('#seguroComplementarioPaciente').prop( "disabled", true );
	$('#companiaSeguroPaciente').prop( "disabled", true );
$('#nacimientoPaciente').inputmask('dd-mm-yyyy', { 'placeholder': 'dd-mm-yyyy' })

$('#tablaPacientes').load('vistas/mantenedores/pacientes/tablaPacientes.php');

$("#rutPaciente").rut({formatOn: 'keyup'}).on('rutInvalido', function(e) {
    swal("Oops!", "El rut " + $(this).val() + " es inválido", "warning");
    $("#rutPaciente").val('');
});

$(function () {
  $.validator.setDefaults({
    submitHandler: function () {
      fnGuardaCreaPaciente();
    }
  });
  $('#frmCreaPaciente').validate({
    rules: {
      rutPaciente: {
        required: true
      },
      nombrePaciente: {
        required: true
      },
      sexoPaciente: {
        required: true
      },
      // nacimientoPaciente: {
      //   required: true
      // },
      mailPaciente: {
        email:true
      },
      regionPaciente: {
        required: true
      },
      provinciaPaciente: {
        required: true
      },
      comunaPaciente: {
        required: true
      },
      // direccionPaciente: {
      //   required: true
      // },
      // planSaludPaciente: {
      //   required: true
      // },
      // seguroComplementarioPaciente: {
      //   required: true
      // },
      // companiaSeguroPaciente: {
      //   required: true
      // },
    },
    messages: {
	    rutPaciente: {
	      required: "Dato Obligatorio"
	    },
	    nombrePaciente: {
	      required: "Dato Obligatorio"
	    },
	     sexoPaciente: {
	      required: "Dato Obligatorio"
	    },
	    // nacimientoPaciente: {
	    //   required: "Dato Obligatorio"
	    // },
	    mailPaciente: {
	      email: "Formato Correo Incorrecto"
	    },
	    regionPaciente: {
        required: "Dato Obligatorio"
      },
      provinciaPaciente: {
        required: "Dato Obligatorio"
      },
       comunaPaciente: {
        required: "Dato Obligatorio"
      },
	    //  direccionPaciente: {
	    //   required: "Dato Obligatorio"
	    // },
	    // planSaludPaciente: {
	    //   required: "Dato Obligatorio"
	    // },
	    // seguroComplementarioPaciente: {
	    //   required: "Dato Obligatorio"
	    // },
	    // companiaSeguroPaciente: {
	    //   required: "Dato Obligatorio"
	    // }
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.input-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });
});

function fnGuardaCreaPaciente(){
	rutPaciente = $('#rutPaciente').val();
	nombrePaciente = $('#nombrePaciente').val();
	sexoPaciente = $('#sexoPaciente').val();
	nacimientoPaciente = $('#nacimientoPaciente').val();
	fonoPaciente = $('#fonoPaciente').val();
	direccionPaciente = $('#direccionPaciente').val();
	comunaPaciente = $('#comunaPaciente').val();
	regionPaciente = $('#regionPaciente').val();
	provinciaPaciente = $('#provinciaPaciente').val();
	mailPaciente = $('#mailPaciente').val();
	ocupacionPaciente = $('#ocupacionPaciente').val();
	previsionPaciente = $('#previsionPaciente').val();
	planSaludPaciente = $('#planSaludPaciente').val();
	seguroComplementarioPaciente = $('#seguroComplementarioPaciente').val();
	companiaSeguroPaciente = $('#companiaSeguroPaciente').val();


	cadena = 'rutPaciente=' + rutPaciente +
			 		 '&nombrePaciente=' + nombrePaciente +
			 		 '&sexoPaciente=' + sexoPaciente +
			 		 '&nacimientoPaciente=' + nacimientoPaciente +
			 		 '&fonoPaciente=' + fonoPaciente +
			 		 '&direccionPaciente=' + direccionPaciente +
			 		 '&comunaPaciente=' + comunaPaciente +
			 		 '&regionPaciente=' + regionPaciente +
			 		 '&provinciaPaciente=' + provinciaPaciente +
			 		 '&mailPaciente=' + mailPaciente +
			 		 '&ocupacionPaciente=' + ocupacionPaciente +
			 		 '&previsionPaciente=' + previsionPaciente +
			 		 '&planSaludPaciente=' + planSaludPaciente +
			 		 '&seguroComplementarioPaciente=' + seguroComplementarioPaciente +
			 		 '&companiaSeguroPaciente=' + companiaSeguroPaciente;
	$.ajax({
		type:"post",
		data:cadena,
		url:'vistas/mantenedores/pacientes/guardaCreaPaciente.php',
		success:function(r){
			if (r == 1) {
				Swal.fire({
				  title: 'Paciente creado correctamente',
				  text: "¿Desea crear una derivación para el paciente?",
				  icon: 'success',
				  showCancelButton: true,
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#d33',
				  confirmButtonText: 'Si, Crear Derivación!'
				}).then((result) => {
				  if (result.isConfirmed) {
				    $('#contenido_principal').load('vistas/derivacion/frmDerivacion.php?rutPaciente=' + rutPaciente);
				    
				  }
				})
				$('#tablaPacientes').load('vistas/mantenedores/pacientes/tablaPacientes.php');
				$('#rutPaciente').val('');
        $('#nombrePaciente').val('');
        $('#sexoPaciente').val('');
        $('#nacimientoPaciente').val('');
        $('#fonoPaciente').val('');
        $('#direccionPaciente').val('');
        $('#comunaPaciente').val('');
        $('#provinciaPaciente').val('');
        $('#regionPaciente').val('');
        $('#mailPaciente').val('');
        $('#previsionPaciente').val('');
        $('#ocupacionPaciente').val('');
        $('#planSaludPaciente').val('');
        $('#seguroComplementarioPaciente').val('');
        $('#companiaSeguroPaciente').val('');
    } else {
    }
			
		}
	});
}

//select anidado para desplegar provincias dependiendo de la region que se seleccione DOMICILIO
function fnBuscaDomicilioProvincia(){
  $("#regionPaciente option:selected").each(function () {
    region=$("#regionPaciente").val();
    $.post("vistas/mantenedores/pacientes/provincias.php", { region: region }, function(data){
        $("#provinciaPaciente").html(data);
    });     
  });
}
function fnBuscaDomicilioComuna(){
  $("#provinciaPaciente option:selected").each(function () {
    provincia=$("#provinciaPaciente").val();
    $.post("vistas/mantenedores/pacientes/comunas.php", { provincia: provincia }, function(data){
        $("#comunaPaciente").html(data);
    });     
  });
}

function fnBuscaRutPacienteExistente(){
	rutPaciente = $('#rutPaciente').val();
	

	cadena = 'rutPaciente=' + rutPaciente;
	$.ajax({
		type:"post",
		data:cadena,
		url:'vistas/mantenedores/pacientes/buscaRutPacienteExistente.php',
		success:function(r){
			if (r == 1) {
				Swal.fire({
				  title: 'Paciente ya existe en sistema',
				  text: "¿Desea crear una derivación para el paciente?",
				  icon: 'success',
				  showCancelButton: true,
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#d33',
				  confirmButtonText: 'Si, Crear Derivación!'
				}).then((result) => {
				  if (result.isConfirmed) {
				    $('#contenido_principal').load('vistas/derivacion/frmDerivacion.php?rutPaciente=' + rutPaciente);
				    
				  }
				})
        $('#nombrePaciente').prop( "disabled", true );
        $('#sexoPaciente').prop( "disabled", true );
        $('#nacimientoPaciente').prop( "disabled", true );
        $('#fonoPaciente').prop( "disabled", true );
        $('#direccionPaciente').prop( "disabled", true );
        $('#comunaPaciente').prop( "disabled", true );
        $('#provinciaPaciente').prop( "disabled", true );
        $('#regionPaciente').prop( "disabled", true );
        $('#mailPaciente').prop( "disabled", true );
        $('#previsionPaciente').prop( "disabled", true );
        $('#ocupacionPaciente').prop( "disabled", true );
        $('#planSaludPaciente').prop( "disabled", true );
        $('#seguroComplementarioPaciente').prop( "disabled", true );
        $('#companiaSeguroPaciente').prop( "disabled", true );
    } else {
    		$('#nombrePaciente').prop( "disabled", false );
    		$('#sexoPaciente').prop( "disabled", false );
    		$('#nacimientoPaciente').prop( "disabled", false );
    		$('#fonoPaciente').prop( "disabled", false );
    		$('#direccionPaciente').prop( "disabled", false );
    		$('#comunaPaciente').prop( "disabled", false );
    		$('#provinciaPaciente').prop( "disabled", false );
    		$('#regionPaciente').prop( "disabled", false );
    		$('#mailPaciente').prop( "disabled", false );
    		$('#previsionPaciente').prop( "disabled", false );
    		$('#ocupacionPaciente').prop( "disabled", false );
    		$('#planSaludPaciente').prop( "disabled", false );
    		$('#seguroComplementarioPaciente').prop( "disabled", false );
    		$('#companiaSeguroPaciente').prop( "disabled", false );
    		
    		$("#nombrePaciente").focus();
    }
			
		}
	});
}
</script>
