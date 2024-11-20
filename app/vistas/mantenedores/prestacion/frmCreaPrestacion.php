<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

?> 
<form id="frmCreaPrestacion">
	<div class="card card-info">
	  <div class="card-header">
	    <h3 class="card-title">PrestacionesX</h3>
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
			  		    <span class="input-group-text">Código prestación</span>
			  		  </div>
			  		  <input type='text' class="form-control input-sm" name="inpCodPrestacion" id="inpCodPrestacion"/>
			  		</div>

				    <div class="input-group mb-3 col-sm-6">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Nombre prestación</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="inpPrestacion" id="inpPrestacion"/>
				    </div>

				    <div class="input-group mb-3 col-sm-3">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Tiempo límite (días)</span>
				      </div>
				      <input type='number' class="form-control input-sm" name="inpTiempoPrestacion" id="inpTiempoPrestacion"/>
				    </div>
				</div>
		   </div>
		   <div class="card-footer">
			    <button type="submit" class="btn btn-info">Guardar</button>
			</div>
	</div> 
</form>
<br>
   <div id="dvCargatblPrestacion"></div>

<script type="text/javascript">

$('#dvCargatblPrestacion').load('vistas/mantenedores/prestacion/tablaPrestacion.php');

$(function () {
  $.validator.setDefaults({
    submitHandler: function () {
      fnGuardaPrestacion();
    }
  });
  $('#frmCreaPrestacion').validate({
    rules: {
      
      inpCodPrestacion: {
        required: true
      },
			inpPrestacion: {
			  required: true
			},

      inpTiempoPrestacion: {
        required: true
      },
     
    },
    messages: {
    	inpCodPrestacion: {
    	  required: "Dato Obligatorio"
    	},
	    inpPrestacion: {
	      required: "Dato Obligatorio"
	    },
	    inpTiempoPrestacion: {
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

function fnGuardaPrestacion(){
	inpCodPrestacion = $('#inpCodPrestacion').val();
	inpPrestacion = $('#inpPrestacion').val();
	inpTiempoPrestacion = $('#inpTiempoPrestacion').val();

	cadena = 'inpCodPrestacion=' + inpCodPrestacion +
						'&inpPrestacion=' + inpPrestacion +
						'&inpTiempoPrestacion=' + inpTiempoPrestacion;
	 		
	$.ajax({
		type:"post",
		data:cadena,
		url:'vistas/mantenedores/prestacion/guardaPrestacion.php',
		success:function(r){
			if (r == 1) {

				Swal.fire({
				  position: 'top-end',
				  icon: 'success',
				  title: 'Prestacion creada correctamente',
				  showConfirmButton: false,
				  timer: 1500
				})
			 $('#dvCargatblPrestacion').load('vistas/mantenedores/prestacion/tablaPrestacion.php');
				$('#inpPrestacion').val('');
				$('#inpTiempoPrestacion').val('');
				$('#inpCodPrestacion').val('');
		    
    } else {
    }
			
		}
	});
}


</script>
