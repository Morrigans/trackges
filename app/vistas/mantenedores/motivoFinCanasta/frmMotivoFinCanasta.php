<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$codRutPac = $_REQUEST['rutPac'];


?>
<form id="frmCreaMotivoCanasta">
	<div class="card card-info">
	  <div class="card-header">
	    <h3 class="card-title">Crea Motivo finalizar canasta</h3>
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

				    <div class="input-group mb-3 col-sm-4" id="tipoMotivo">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Tipo Motivo</span>
				      </div>
				   <!--    <input type='text' class="form-control input-sm" name="descripcionPatologia" id="descripcionPatologia"/> -->
				       <select name="slTipoMotivo" id="slTipoMotivo" class="form-control input-sm">
				            <!-- <option value="">Seleccione...</option> -->
				            <option value="CANASTA_VENCIDA">Canastas Vencidas</option>
				            <!-- <option value="NO_VENCIDA">SIN VENCIDAS</option> -->
				             
				        </select>
				    </div>

				    <div class="input-group mb-3 col-sm-7">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Motivo</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="descripcionVence" id="descripcionVence"/>
				    </div>
 
			

				</div>
		   </div>
		   <div class="card-footer">
			    <button type="submit" class="btn btn-info">Guardar motivo</button>
			</div>
	</div>
</form>
<br>
   <div id="dvMotivoVence"></div>

<script type="text/javascript">
$('#tipoMotivo').hide();
$('#dvMotivoVence').load('vistas/mantenedores/motivoFinCanasta/tablaMotivoFinCanasta.php');

$(function () {
  $.validator.setDefaults({
    submitHandler: function () {
      fnGuardaCreaMotivo();
    }
  });
  $('#frmCreaMotivoCanasta').validate({
    rules: {
      slTipoMotivo: {
        required: true
      },
      descripcionVence: {
        required: true
      },
     
    },
    messages: {
	    slTipoMotivo: {
	      required: "Dato Obligatorio"
	    },
	    descripcionVence: {
	      required: "Dato Obligatorio"
	    },
	   
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

function fnGuardaCreaMotivo(){
	slTipoMotivo = $('#slTipoMotivo').val();
	descripcionVence = $('#descripcionVence').val();


	cadena = 'slTipoMotivo=' + slTipoMotivo +
	 			 		 '&descripcionVence=' + descripcionVence;

	$.ajax({
		type:"post",
		data:cadena,
		url:'vistas/mantenedores/motivoFinCanasta/guardaMotivoFinCanasta.php',
		success:function(r){
			if (r == 1) {

				Swal.fire({
				  position: 'top-end',
				  icon: 'success',
				  title: 'Motivo creado correctamente',
				  showConfirmButton: false,
				  timer: 1500
				})
				$('#dvMotivoVence').load('vistas/mantenedores/motivoFinCanasta/tablaMotivoFinCanasta.php');
				
		        $('#slTipoMotivo').val('');
		        $('#descripcionVence').val('');
    	}
		}
	});
}

</script>
