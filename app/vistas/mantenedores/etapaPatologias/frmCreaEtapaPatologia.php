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

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia order by DESC_TIPO_PATOLOGIA asc";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();


?> 
<form id="frmCreaEtapaPatologia">
	<div class="card card-info">
	  <div class="card-header">
	    <h3 class="card-title">Etapas patologia</h3>
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
				          <span class="input-group-text">Tipo Patología</span>
				        </div>
				        <select name="slTipoPatologia" id="slTipoPatologia" class="form-control input-sm" onchange="fnFiltraPatologias()">
				            <option value="">Seleccione...</option>
				             <?php 
				             while (!$qrTipoPatologia->EOF) {?>
				               <option value="<?php echo $qrTipoPatologia->Fields('ID_TIPO_PATOLOGIA'); ?>"><?php echo $qrTipoPatologia->Fields('DESC_TIPO_PATOLOGIA'); ?></option>
				            <?php $qrTipoPatologia->MoveNext(); } ?> 
				        </select>
				    </div>


			  <div class="input-group mb-3 col-sm-5">
				        <div class="input-group-prepend">
				          <span class="input-group-text">Patología</span>
				        </div>
				        <select name="slPatologia" id="slPatologia" class="form-control input-sm">
				            <option value="">Seleccione...</option>
				            
				        </select>
				    </div>

				    <div class="input-group mb-3 col-sm-4">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Etapa</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="inpEtapaPatologia" id="inpEtapaPatologia"/>
				    </div>
				    			    
			    
				</div>
		   </div>
		   <div class="card-footer">
			    <button type="submit" class="btn btn-info">Guardar etapa</button>
			</div>
	</div> 
</form>
<br>
   <div id="tablaEtapaPatologias"></div>

<script type="text/javascript">

$('#tablaEtapaPatologias').load('vistas/mantenedores/etapaPatologias/tablaEtapaPatologias.php');

function fnFiltraPatologias(){
    $("#slTipoPatologia option:selected").each(function () {
        tipoPatologia=$(this).val();


          $.post("vistas/mantenedores/canastasPatologias/filtraPatologias.php",
          { tipoPatologia: tipoPatologia },
            function(data){
            $("#slPatologia").html(data);
           
          });
    }); 
}



 



$(function () {
  $.validator.setDefaults({
    submitHandler: function () {
      fnGuardaEtapaPatologia();
    }
  });
  $('#frmCreaEtapaPatologia').validate({
    rules: {
      
      inpEtapaPatologia: {
        required: true
      },
       slTipoPatologia: {
        required: true
      },
       slPatologia: {
        required: true
      },
    },
    messages: {
	    inpEtapaPatologia: {
	      required: "Dato Obligatorio"
	    },
	     slTipoPatologia: {
	      required: "Dato Obligatorio"
	    },
	     slPatologia: {
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

function fnGuardaEtapaPatologia(){
	inpEtapaPatologia = $('#inpEtapaPatologia').val();
	codigoPatologia = $('#slPatologia').val();
	slTipoPatologia = $('#slTipoPatologia').val();




	cadena = 'inpEtapaPatologia=' + inpEtapaPatologia +
	 		 '&codigoPatologia=' + codigoPatologia +
	 		 '&slTipoPatologia=' + slTipoPatologia;

	$.ajax({
		type:"post",
		data:cadena,
		url:'vistas/mantenedores/etapaPatologias/guardaEtapaPatologia.php',
		success:function(r){
			if (r == 1) {

				Swal.fire({
				  position: 'top-end',
				  icon: 'success',
				  title: 'Etapa Patología creada correctamente',
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
			$('#tablaEtapaPatologias').load('vistas/mantenedores/etapaPatologias/tablaEtapaPatologias.php');
				$('#inpEtapaPatologia').val('');
		        $('#slPatologia').val('');
		        $('#slTipoPatologia').val('');
    } else {
    }
			
		}
	});
}


</script>
