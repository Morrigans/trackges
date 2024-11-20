<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$codRutPac = $_REQUEST['rutPac'];

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia order by DESC_TIPO_PATOLOGIA asc";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia order by DESC_PATOLOGIA asc";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$query_qrEtapaPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia order by DESC_ETAPA_PATOLOGIA asc";
$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia order by DESC_TIPO_PATOLOGIA asc";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();


?>
<form id="frmCreaCanastaPatologias">
	<div class="card card-info">
	  <div class="card-header">
	    <h3 class="card-title">Registro nueva canasta</h3>
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
				        <select name="slTipoPatologia" id="slTipoPatologia" class="form-control input-sm btn-block" onchange="fnFiltraPatologias()">
				            <option value="">Seleccione...</option>
				            <option value="1">GES</option>				             
				        </select>
				    </div>

				    <div class="input-group mb-3 col-sm-9">
				        <div class="input-group-prepend">
				          <span class="input-group-text">Patología</span>
				        </div>
				        <select name="slPatologia" id="slPatologia" class="form-control input-sm" onchange="fnFiltraEtapasPatologias()"></select>
				    </div>				    

				    <div class="input-group mb-3 col-sm-3">
				        <div class="input-group-prepend">
				          <span class="input-group-text">Etapa Patología</span>
				        </div>
				        <select name="slEtapaPatologia" id="slEtapaPatologia" class="form-control input-sm btn-block"></select>
				    </div>				    

				    <div class="input-group mb-3 col-sm-9">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Nombre Canasta</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="nombreCanastaPatologia" id="nombreCanastaPatologia"/>
				    </div>

				    <div class="input-group mb-3 col-sm-3">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Días límite</span>
				      </div>
				      <input type='text' class="form-control input-sm btn-block" name="diasLimiteCanasta" id="diasLimiteCanasta"/>
				    </div>

				    <div class="input-group mb-3 col-sm-9">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Observación</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="obsCanastaPatologia" id="obsCanastaPatologia"/>
				    </div>

				</div>
		   </div>
		   <div class="card-footer">
			    <button type="submit" class="btn btn-info">Guardar Canasta</button>
			</div>
	</div>
</form>
<br>
   <div id="dvTablaCanastas"></div>

<script type="text/javascript">

$('#dvTablaCanastas').load('vistas/mantenedores/canastasPatologias/tablaCanastas.php');

$(function () {
  $.validator.setDefaults({
    submitHandler: function () {
      fnGuardaCreaCanasta();
    }
  });
  $('#frmCreaCanastaPatologias').validate({
    rules: {
      slTipoPatologia: {
        required: true
      },
      slPatologia: {
        required: true
      },
      slEtapaPatologia: {
        required: true
      },
      nombreCanastaPatologia: {
        required: true
      },
      diasLimiteCanasta: {
        required: true
      },
      obsCanastaPatologia: {
        required: true
      },
    },
    messages: {
	    slTipoPatologia: {
	      required: "Dato Obligatorio"
	    },
	    slPatologia: {
	      required: "Dato Obligatorio"
	    },
	    slEtapaPatologia: {
	      required: "Dato Obligatorio"
	    },
	    nombreCanastaPatologia: {
	      required: "Dato Obligatorio"
	    },
	    diasLimiteCanasta: {
	      required: "Dato Obligatorio"
	    },
	    obsCanastaPatologia: {
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

function fnGuardaCreaCanasta(){
	slTipoPatologia = $('#slTipoPatologia').val();
	slPatologia = $('#slPatologia').val();
	slEtapaPatologia = $('#slEtapaPatologia').val();
	nombreCanastaPatologia = $('#nombreCanastaPatologia').val();
	diasLimiteCanasta = $('#diasLimiteCanasta').val(); 
	obsCanastaPatologia = $('#obsCanastaPatologia').val();

	cadena= 'slTipoPatologia=' + slTipoPatologia +
	 		'&slPatologia=' + slPatologia +
	 		'&slEtapaPatologia=' + slEtapaPatologia +
	 		'&nombreCanastaPatologia=' + nombreCanastaPatologia +
	 		'&diasLimiteCanasta=' + diasLimiteCanasta +
	 		'&obsCanastaPatologia=' + obsCanastaPatologia;

	$.ajax({
		type:"post",
		data:cadena,
		url:'vistas/mantenedores/canastasPatologias/guardaCreaCanasta.php',
		success:function(r){
			if (r == 1) {

				Swal.fire({
				  position: 'top-end',
				  icon: 'success',
				  title: 'Patología creada correctamente',
				  showConfirmButton: false,
				  timer: 1500
				})
				$('#dvTablaCanastas').load('vistas/mantenedores/canastasPatologias/tablaCanastas.php');
				$('#slTipoPatologia').val('');
		        $('#slPatologia').val('');
		        $('#slEtapaPatologia').val('');
		        $('#nombreCanastaPatologia').val('');
		        $('#diasLimiteCanasta').val('');
		        $('#obsCanastaPatologia').val('');
    } else {
    }
			
		}
	});
}

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
function fnFiltraEtapasPatologias(){
    $("#slPatologia option:selected").each(function () {
        patologia=$(this).val();
        tipoPatologia = $("#slTipoPatologia").val();
        if (tipoPatologia == '2') {
          $("#slEtapaPatologia").val('0');
        }else{
          $.post("vistas/mantenedores/canastasPatologias/filtraEtapasPatologias.php",
          { patologia: patologia },
            function(data){
                $("#slEtapaPatologia").html(data);
          });
        }
    });
}
</script>
