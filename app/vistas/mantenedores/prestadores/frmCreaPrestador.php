<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$codRutPac = $_REQUEST['rutPac'];

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.prestador order by ID_PRESTADOR asc";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();
?>
<form id="frmCreaPrestador">
	<div class="card card-info">
	  <div class="card-header">
	    <h3 class="card-title">Registra nuevo prestador</h3>
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

				   <!--  <div class="input-group mb-3 col-sm-3">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Rut Prestador</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="rutNuevoPrestador" id="rutNuevoPrestador" onchange="validaRutPrestador()"/>
				    </div> -->

				    <div class="input-group mb-3 col-sm-12">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Nombre Prestador</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="nombrePrestador" id="nombrePrestador"  />
				    </div>
				    <!-- <div class="input-group mb-3 col-sm-6">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Comuna</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="comunaPrestador" id="comunaPrestador"  />
				    </div>

				    <div class="input-group mb-3 col-sm-6">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Dirección</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="direccionPrestador" id="direccionPrestador"  />
				    </div>
				    <div class="input-group mb-3 col-sm-4">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Región</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="regionPrestador" id="regionPrestador"  />
				    </div>
				    <div class="input-group mb-3 col-sm-2">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Rut Facturación</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="rutFacturacionPrestador" id="rutFacturacionPrestador"  />
				    </div> -->

				</div>
		   </div>
		   <div class="card-footer">
			    <button type="submit" class="btn btn-info">Guardar Prestador</button>
			</div>
	</div>
</form>
<br>
   <div id="dvTablaPrestador"></div>

<script type="text/javascript">

$('#dvTablaPrestador').load('vistas/mantenedores/prestadores/tablaPrestadores.php');

$("#rutNuevoPrestador").rut({formatOn: 'keyup'}).on('rutInvalido', function(e) {
    swal("Oops!", "El rut " + $(this).val() + " es inválido", "warning");
    $("#rutNuevoPrestador").val('');
});

$(function () {
  $.validator.setDefaults({
    submitHandler: function () {
      fnGuardaCreaPrestador();
    }
  });
  $('#frmCreaPrestador').validate({
    rules: {
      nombrePrestador: {
        required: true
      },
    },
    messages: {
	    nombrePrestador: {
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

function fnGuardaCreaPrestador(){
	//rutNuevoPrestador = $('#rutNuevoPrestador').val();
	nombrePrestador = $('#nombrePrestador').val();

	cadena = 'nombrePrestador=' + nombrePrestador;

	$.ajax({
		type:"post",
		data:cadena,
		url:'vistas/mantenedores/prestadores/guardaCreaPrestador.php',
		success:function(r){
			if (r == 1) {

				Swal.fire({
				  position: 'top-end',
				  icon: 'success',
				  title: 'Prestador creado correctamente',
				  showConfirmButton: false,
				  timer: 1500
				})

				$('#dvTablaPrestador').load('vistas/mantenedores/prestadores/tablaPrestadores.php');
		        //$('#rutNuevoPrestador').val('');
		        $('#nombrePrestador').val('');
    } else {
    }
			
		}
	});
}



// function validaRutPrestador(){ 
// 	rutNuevoPrestador=$('#rutNuevoPrestador').val();

// 	cadena = 'rutNuevoPrestador=' + rutNuevoPrestador;


// 	$.ajax({
// 		type:"post",
// 		data:cadena,
// 		url:'vistas/mantenedores/prestadores/validaRutPrestador.php',
// 		success:function(r){
// 			if (r == 1) {

// 			 swal("Oops!", "¡Rut del prestador existe!", "warning"); 

// 				// $('#dvTablaPrestador').load('vistas/mantenedores/prestadores/tablaPrestadores.php');
// 		        $('#rutNuevoPrestador').val('');
		    
//     } else {
//     }
			 
// 		}
// 	});
// }

</script>
