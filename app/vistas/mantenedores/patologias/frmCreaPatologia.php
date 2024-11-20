<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$codRutPac = $_REQUEST['rutPac'];

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia order by DESC_TIPO_PATOLOGIA asc";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();
?>
<form id="frmCreaPatologia">
	<div class="card card-info">
	  <div class="card-header">
	    <h3 class="card-title">Crea patologías</h3>
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

				    <div class="input-group mb-3 col-sm-6">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Patología</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="descripcionPatologia" id="descripcionPatologia"/>
				    </div>

				    <div class="input-group mb-3 col-sm-2">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Código</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="codigoPatologia" id="codigoPatologia"/>
				    </div>

				    <div class="input-group mb-3 col-sm-2">
				        <div class="input-group-prepend">
				          <span class="input-group-text">Tipo Patología</span>
				        </div>
				        <select name="slTipoPatologia" id="slTipoPatologia" class="form-control input-sm">
				            <option value="">Seleccione...</option>
				             <?php 
				             while (!$qrTipoPatologia->EOF) {?>
				               <option value="<?php echo $qrTipoPatologia->Fields('ID_TIPO_PATOLOGIA'); ?>"><?php echo utf8_encode($qrTipoPatologia->Fields('DESC_TIPO_PATOLOGIA')); ?></option>
				            <?php $qrTipoPatologia->MoveNext(); } ?> 
				        </select>
				    </div>
				    <div class="input-group mb-3 col-sm-2">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Días de vigencía</span>
				      </div>
				      <input type='number' class="form-control input-sm" name="diasDeVigencia" id="diasDeVigencia">
				    </div>

				</div>
		   </div>
		   <div class="card-footer">
			    <button type="submit" class="btn btn-info">Guardar Patología</button>
			</div>
	</div>
</form>
<br>
   <div id="dvTablaPatologias"></div>

<script type="text/javascript">

$('#dvTablaPatologias').load('vistas/mantenedores/patologias/tablaPatologias.php');

$(function () {
  $.validator.setDefaults({
    submitHandler: function () {
      fnGuardaCreaPatologia(); 
    }
  });
  $('#frmCreaPatologia').validate({
    rules: {
      descripcionPatologia: {
        required: true
      },
      codigoPatologia: {
        required: true
      },
      slTipoPatologia: {
        required: true
      },
       diasDeVigencia: {
        required: true
      },
    },
    messages: {
	    descripcionPatologia: {
	      required: "Dato Obligatorio"
	    },
	    codigoPatologia: {
	      required: "Dato Obligatorio"
	    },
	    slTipoPatologia: {
	      required: "Dato Obligatorio"
	    },

	    diasDeVigencia: {
	      required: "Dato Obligatorio"
	    }
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

function fnGuardaCreaPatologia(){
	descripcionPatologia = $('#descripcionPatologia').val();
	codigoPatologia = $('#codigoPatologia').val();
	slTipoPatologia = $('#slTipoPatologia').val();
	diasDeVigencia = $('#diasDeVigencia').val();

	cadena = 'descripcionPatologia=' + descripcionPatologia +
	 		 '&codigoPatologia=' + codigoPatologia +
	 		 '&diasDeVigencia=' + diasDeVigencia +
	 		 '&slTipoPatologia=' + slTipoPatologia;

	$.ajax({
		type:"post",
		data:cadena,
		url:'vistas/mantenedores/patologias/guardaCreaPatologia.php',
		success:function(r){
			if (r == 1) {

				Swal.fire({
				  position: 'top-end',
				  icon: 'success',
				  title: 'Patología creada correctamente',
				  showConfirmButton: false,
				  timer: 1500
				})

				// Swal.fire({
				//   title: 'Patología creada correctamente',
				//   text: "¿Desea crear una derivación para el paciente?",
				//   icon: 'success',
				//   showCancelButton: true,
				//   confirmButtonColor: '#3085d6',
				//   cancelButtonColor: '#d33',
				//   confirmButtonText: 'Si, Crear Derivación!'
				// }).then((result) => {
				//   if (result.isConfirmed) {
				//     $('#contenido_principal').load('vistas/derivacion/frmDerivacion.php?rutPaciente=' + rutPaciente);
				    
				//   }
				// })
				$('#dvTablaPatologias').load('vistas/mantenedores/patologias/tablaPatologias.php');
				$('#descripcionPatologia').val('');
		        $('#codigoPatologia').val('');
		        $('#slTipoPatologia').val('');
    } else {
    }
			
		}
	});
}

// function fnBuscaRutPacienteExistente(){
// 	rutPaciente = $('#rutPaciente').val();
	

// 	cadena = 'rutPaciente=' + rutPaciente;
// 	$.ajax({
// 		type:"post",
// 		data:cadena,
// 		url:'vistas/mantenedores/pacientes/buscaRutPacienteExistente.php',
// 		success:function(r){
// 			if (r == 1) {
// 				Swal.fire({
// 				  title: 'Paciente ya existe en sistema',
// 				  text: "¿Desea crear una derivación para el paciente?",
// 				  icon: 'success',
// 				  showCancelButton: true,
// 				  confirmButtonColor: '#3085d6',
// 				  cancelButtonColor: '#d33',
// 				  confirmButtonText: 'Si, Crear Derivación!'
// 				}).then((result) => {
// 				  if (result.isConfirmed) {
// 				    $('#contenido_principal').load('vistas/derivacion/frmDerivacion.php?rutPaciente=' + rutPaciente);
				    
// 				  }
// 				})
//         $('#nombrePaciente').prop( "disabled", true );
//         $('#nacimientoPaciente').prop( "disabled", true );
//         $('#fonoPaciente').prop( "disabled", true );
//         $('#direccionPaciente').prop( "disabled", true );
//         $('#comunaPaciente').prop( "disabled", true );
//         $('#provinciaPaciente').prop( "disabled", true );
//         $('#regionPaciente').prop( "disabled", true );
//         $('#mailPaciente').prop( "disabled", true );
//         $('#previsionPaciente').prop( "disabled", true );
//         $('#ocupacionPaciente').prop( "disabled", true );
//         $('#planSaludPaciente').prop( "disabled", true );
//         $('#seguroComplementarioPaciente').prop( "disabled", true );
//         $('#companiaSeguroPaciente').prop( "disabled", true );
//     } else {
//     		$('#nombrePaciente').prop( "disabled", false );
//     		$('#nacimientoPaciente').prop( "disabled", false );
//     		$('#fonoPaciente').prop( "disabled", false );
//     		$('#direccionPaciente').prop( "disabled", false );
//     		$('#comunaPaciente').prop( "disabled", false );
//     		$('#provinciaPaciente').prop( "disabled", false );
//     		$('#regionPaciente').prop( "disabled", false );
//     		$('#mailPaciente').prop( "disabled", false );
//     		$('#previsionPaciente').prop( "disabled", false );
//     		$('#ocupacionPaciente').prop( "disabled", false );
//     		$('#planSaludPaciente').prop( "disabled", false );
//     		$('#seguroComplementarioPaciente').prop( "disabled", false );
//     		$('#companiaSeguroPaciente').prop( "disabled", false );
    		
//     		$("#nombrePaciente").focus();
//     }
			
// 		}
// 	});
// }
</script>
