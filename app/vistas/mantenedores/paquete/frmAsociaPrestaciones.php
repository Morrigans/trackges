<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idPaquete=$_REQUEST['idPaquete']; 
//$idCanasta=$_REQUEST['idCanasta']; 

$query_qrPresta = "SELECT * FROM $MM_oirs_DATABASE.prestaciones";
$qrPresta = $oirs->SelectLimit($query_qrPresta) or die($oirs->ErrorMsg());
$totalRows_qrPresta = $qrPresta->RecordCount();

?> 
<form id="frmAsociaPaquetePrestaciones">
	<div class="card card-info">
	  <div class="card-header">
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
			  		<input type='hidden' class="form-control input-sm" name="hdIdPaquete" id="hdIdPaquete" value="<?php echo $idPaquete; ?>" />
				    <div class="input-group mb-3 col-sm-12">
				        <div class="input-group-prepend">
				          <span class="input-group-text">Prestación</span>
				        </div>
				        <select name="slPrestaciones" id="slPrestaciones" class="form-control input-sm select2bs4" onchange="fnGuardaPaquetePrestacion()">
				            <option value="">Seleccione...</option>
                  <?php while (!$qrPresta->EOF) {?>
                    <option value="<?php echo $qrPresta->Fields('ID_PRESTACION') ?>"><?php echo $qrPresta->Fields('PRESTACION') ?></option>
                  <?php $qrPresta->MoveNext(); } ?>
				        </select>
				        
				    </div>
				</div>
		   </div>
		   <!-- <div class="card-footer">
			    <button type="submit" class="btn btn-info">Guardar</button>
			</div> -->
	</div> 
</form>
<br>
   <div id="dvMuestraTblPaquetePrestaciones"></div>

<script type="text/javascript">
		//Initialize Select2 Elements
	  $('.select2bs4').select2({
	    theme: 'bootstrap4'
	  })
	  idPaquete = $('#hdIdPaquete').val();

		$('#dvMuestraTblPaquetePrestaciones').load('vistas/mantenedores/paquete/tblPaquetePrestaciones.php?idPaquete='+idPaquete);

		$(function () {
		  $.validator.setDefaults({
		    submitHandler: function () {
		      fnGuardaPaquetePrestacion();
		    }
		  });
		  $('#frmAsociaPaquetePrestaciones').validate({
		    rules: {
		      
		      slPrestaciones: {
		        required: true
		      },
		     
		    },
		    messages: {
			    slPrestaciones: {
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

		function fnGuardaPaquetePrestacion(){
			idPaquete = $('#hdIdPaquete').val();
			slPrestaciones = $('#slPrestaciones').val();

			cadena = 'idPaquete=' + idPaquete +
							 '&slPrestaciones=' + slPrestaciones;

			$.ajax({
				type:"post",
				data:cadena,
				url:'vistas/mantenedores/paquete/guardaPaquetePrestacion.php',
				success:function(r){
					if (r == 1) {

						Swal.fire({
						  position: 'top-end',
						  icon: 'success',
						  title: 'Paquete creado correctamente',
						  showConfirmButton: false,
						  timer: 1500
						})

					 $('#dvMuestraTblPaquetePrestaciones').load('vistas/mantenedores/paquete/tblPaquetePrestaciones.php?idPaquete='+idPaquete);
						$('#hdIdPaquete').val('');
						$('#slPrestaciones').val('');
				    
		    } else {
		    }
					
				}
			});
		}


</script>
