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
	    <h3 class="card-title">sssssss</h3>
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

				    <div class="input-group mb-3 col-sm-8">
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

//$('#dvTablaPatologias').load('vistas/mantenedores/patologias/tablaPatologias.php');

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

	cadena = 'descripcionPatologia=' + descripcionPatologia +
	 		 '&codigoPatologia=' + codigoPatologia +
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
				$('#dvTablaPatologias').load('vistas/mantenedores/patologias/tablaPatologias.php');
				$('#descripcionPatologia').val('');
		        $('#codigoPatologia').val('');
		        $('#slTipoPatologia').val('');
    } else {
    }
			
		}
	});
}
</script>
