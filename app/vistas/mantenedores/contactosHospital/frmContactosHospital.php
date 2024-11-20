<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idHospital = $_REQUEST['idHospital'];




?>
<form id="formContactosHospital">
	<div class="card card-info">
	  <div class="card-header">
	    <h3 class="card-title">Datos del contacto</h3>
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
				        <span class="input-group-text">Nombre</span>
				      </div>
				     <input type='text' class="form-control input-sm" name="nombreContactosHospital" id="nombreContactosHospital"/>
				    </div>
				    <div class="input-group mb-3 col-sm-6">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Email</span>
				      </div>
				       <input type='email' class="form-control input-sm" name="mailContactosHospital" id="mailContactosHospital"/>
				    </div>
				    
				    <div class="input-group mb-3 col-sm-6">
				    	<div class="input-group-prepend">
				    	  <span class="input-group-text">Telefono</span>
				    	</div>
				    	    <input type='text' class="form-control input-sm" name="telefonoContactosHospital" id="telefonoContactosHospital"/>
				    </div>
				    
				    <div class="input-group mb-3 col-sm-6">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Servicio/Unidad</span>
				      </div>
				       <input type='text' class="form-control input-sm" name="unidadContactosHospital" id="unidadContactosHospital"/>
				    </div>
				    <div class="input-group mb-3 col-sm-12">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Observación</span>
				      </div>
				       <textarea type='text' class="form-control input-sm" name="observacionContactosHospital" id="observacionContactosHospital"></textarea>
				    </div>
				 <input type="hidden" id="inpIdHospital" name="inpIdHospital" value="<?php echo $idHospital ?>">   

		   </div>
		   <div align="right">
			    <button type="submit" class="btn btn-info">Guardar Datos</button>
			</div>
	</div>
</div>
</form>
<br>
   <div id="tablaContactos"></div>



<script type="text/javascript">
		idHospital = $('#inpIdHospital').val();

	$('#tablaContactos').load('vistas/mantenedores/contactosHospital/tablaContactos.php?idHospital='+idHospital);

						$(function () {

						$.validator.setDefaults({
						  submitHandler: function () {
						    fnGuardaContactosHospital();
						  }
						});
						$('#formContactosHospital').validate({
						  rules: {
						    nombreContactosHospital: {
						      required: true
						    },
						    mailContactosHospital: {
						      required: true
						    },
						    telefonoContactosHospital: {
						      required: true
						    },
						  },



						  messages: {
						    nombreContactosHospital: {
						      required: "Dato Obligatorio"
						    },
						    mailContactosHospital: {
						      required: "Dato Obligatorio"
						    },
						     telefonoContactosHospital: {
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

						function fnGuardaContactosHospital(){



						nombre = $('#nombreContactosHospital').val();
						email = $('#mailContactosHospital').val();
						telefono = $('#telefonoContactosHospital').val();
						unidadServicio = $('#unidadContactosHospital').val();
						obsevacion = $('#observacionContactosHospital').val();
						idHospital = $('#inpIdHospital').val();


						cadena = 'nombre=' + nombre +
								 		 '&email=' + email +
								 		 '&telefono=' + telefono +
								 		 '&unidadServicio=' + unidadServicio +
								 		 '&idHospital=' + idHospital +
								 		 '&obsevacion=' + obsevacion;
						$.ajax({
							type:"post",
							data:cadena,
							url:'vistas/mantenedores/contactosHospital/guardaContactosHospital.php',
							success:function(r){

							 $('#nombreContactosHospital').val('');
						$('#mailContactosHospital').val('');
						$('#telefonoContactosHospital').val('');
						$('#unidadContactosHospital').val('');
						$('#observacionContactosHospital').val('');
						$('#inpIdHospital').val('');

					    
								swal("Genial!", "Contacto creado correctamente", "success");


								
						             	$('#tablaContactos').load('vistas/mantenedores/contactosHospital/tablaContactos.php?idHospital='+idHospital);

							}
						});
						}



</script>
