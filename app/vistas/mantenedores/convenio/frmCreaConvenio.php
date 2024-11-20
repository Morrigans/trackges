<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

?> 
<form id="frmCreaConvenio">
	<div class="card card-info">
	  <div class="card-header">
	    <h3 class="card-title">Convenio</h3>
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
				    <div class="input-group mb-3 col-sm-4">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Nombre convenio</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="inpConvenio" id="inpConvenio"/>
				    </div>
				</div>
		   </div>
		   <div class="card-footer">
			    <button type="submit" class="btn btn-info">Guardar</button>
			</div>
	</div> 
</form>
<br>
   <div id="tablaConvenio"></div>

<script type="text/javascript">

$('#tablaConvenio').load('vistas/mantenedores/convenio/tablaConvenio.php');




 



$(function () {
  $.validator.setDefaults({
    submitHandler: function () {
      fnGuardaConvenio();
    }
  });
  $('#frmCreaConvenio').validate({
    rules: {
      
      inpConvenio: {
        required: true
      },
     
    },
    messages: {
	    inpConvenio: {
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

function fnGuardaConvenio(){
	inpConvenio = $('#inpConvenio').val();





	cadena = 'inpConvenio=' + inpConvenio;
	 		

	$.ajax({
		type:"post",
		data:cadena,
		url:'vistas/mantenedores/convenio/guardaConvenio.php',
		success:function(r){
			if (r == 1) {

				Swal.fire({
				  position: 'top-end',
				  icon: 'success',
				  title: 'Convenio creado correctamente',
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
			 $('#tablaConvenio').load('vistas/mantenedores/convenio/tablaConvenio.php');
				$('#inpConvenio').val('');
		    
    } else {
    }
			
		}
	});
}


</script>
