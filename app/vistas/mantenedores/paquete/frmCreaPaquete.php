<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_qrCanasta = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia where DECRETO = 'LEP1922'";
$qrCanasta = $oirs->SelectLimit($query_qrCanasta) or die($oirs->ErrorMsg());
$totalRows_qrCanasta = $qrCanasta->RecordCount();

?> 
<form id="frmCreaPaquete">
	<div class="card card-info">
	  <div class="card-header">
	    <h3 class="card-title">Paquete</h3>
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
				        <span class="input-group-text">Nombre paquete</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="inpPaquete" id="inpPaquete"/>
				    </div>
				    <div class="input-group mb-3 col-sm-5">
				        <div class="input-group-prepend">
				          <span class="input-group-text">Canasta</span>
				        </div>
				        <select name="slCanasta" id="slCanasta" class="form-control input-sm select2bs4">
				            <option value="">Seleccione...</option>
                  <?php while (!$qrCanasta->EOF) {?>
                    <option value="<?php echo $qrCanasta->Fields('ID_CANASTA_PATOLOGIA') ?>"><?php echo utf8_encode($qrCanasta->Fields('DESC_CANASTA_PATOLOGIA')) ?></option>
                  <?php $qrCanasta->MoveNext(); } ?>
				        </select>
				        
				    </div>
				</div>
		   </div>
		   <div class="card-footer">
			    <button type="submit" class="btn btn-info">Guardar</button>
			</div>
	</div> 
</form>
<br>
   <div id="tablaPaquete"></div>

<script type="text/javascript">
	//Initialize Select2 Elements
  $('.select2bs4').select2({
    theme: 'bootstrap4'
  })

$('#tablaPaquete').load('vistas/mantenedores/paquete/tablaPaquete.php');

$(function () {
  $.validator.setDefaults({
    submitHandler: function () {
      fnGuardaPaquete();
    }
  });
  $('#frmCreaPaquete').validate({
    rules: {
      
      inpPaquete: {
        required: true
      },
      slCanasta: {
        required: true
      },
     
    },
    messages: {
	    inpPaquete: {
	      required: "Dato Obligatorio"
	    },
	    slCanasta: {
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

function fnGuardaPaquete(){
	inpPaquete = $('#inpPaquete').val();
	idCanasta = $('#slCanasta').val();

	cadena = 'inpPaquete=' + inpPaquete +
					 '&idCanasta=' + idCanasta;

	$.ajax({
		type:"post",
		data:cadena,
		url:'vistas/mantenedores/paquete/guardaPaquete.php',
		success:function(r){
			if (r == 1) {

				Swal.fire({
				  position: 'top-end',
				  icon: 'success',
				  title: 'Paquete creado correctamente',
				  showConfirmButton: false,
				  timer: 1500
				})

			 $('#tablaPaquete').load('vistas/mantenedores/paquete/tablaPaquete.php');
				$('#inpPaquete').val('');
		    
    } else {
    }
			
		}
	});
}


</script>
