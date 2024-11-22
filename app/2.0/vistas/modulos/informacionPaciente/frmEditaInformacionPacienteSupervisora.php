<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idPaciente = $qrDerivacion->Fields('ID_PACIENTE');

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$idPaciente'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$codRutPac = $qrPaciente->Fields('COD_RUTPAC');

$query_qrRegion = "SELECT * FROM $MM_oirs_DATABASE.regiones";
$qrRegion = $oirs->SelectLimit($query_qrRegion) or die($oirs->ErrorMsg());
$totalRows_qrRegion = $qrRegion->RecordCount();

$query_qrPrevision = "SELECT * FROM $MM_oirs_DATABASE.prevision";
$qrPrevision = $oirs->SelectLimit($query_qrPrevision) or die($oirs->ErrorMsg());
$totalRows_qrPrevision = $qrPrevision->RecordCount();

$idRegion = $qrPaciente->Fields('REGION');

$query_qrRegionPac= "SELECT * FROM $MM_oirs_DATABASE.regiones WHERE region_id = '$idRegion'";
$qrRegionPac = $oirs->SelectLimit($query_qrRegionPac) or die($oirs->ErrorMsg());
$totalRows_qrRegionPac = $qrRegionPac->RecordCount();

$idProvincia = $qrPaciente->Fields('PROVINCIA');

$query_qrProvinciaPac= "SELECT * FROM $MM_oirs_DATABASE.provincias WHERE provincia_id = '$idProvincia'";
$qrProvinciaPac = $oirs->SelectLimit($query_qrProvinciaPac) or die($oirs->ErrorMsg());
$totalRows_qrProvinciaPac = $qrProvinciaPac->RecordCount();

$query_qrProvincia= "SELECT * FROM $MM_oirs_DATABASE.provincias WHERE region_id = '$idRegion'";
$qrProvincia = $oirs->SelectLimit($query_qrProvincia) or die($oirs->ErrorMsg());
$totalRows_qrProvincia = $qrProvincia->RecordCount();

$idComuna = $qrPaciente->Fields('COMUNA');

$query_qrComunaPac= "SELECT * FROM $MM_oirs_DATABASE.comunas WHERE comuna_id = '$idComuna'";
$qrComunaPac = $oirs->SelectLimit($query_qrComunaPac) or die($oirs->ErrorMsg());
$totalRows_qrComunaPac = $qrComunaPac->RecordCount();

$query_qrComuna= "SELECT * FROM $MM_oirs_DATABASE.comunas WHERE provincia_id = '$idProvincia'";
$qrComuna = $oirs->SelectLimit($query_qrComuna) or die($oirs->ErrorMsg());
$totalRows_qrComuna = $qrComuna->RecordCount();

$idPrevision = $qrPaciente->Fields('PREVISION');

$query_qrPrevi = "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$idPrevision'";
$qrPrevi = $oirs->SelectLimit($query_qrPrevi) or die($oirs->ErrorMsg());
$totalRows_qrPrevi = $qrPrevi->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
<form id="frmEditaPaciente">
    	<div class="card-body">
        <div class="row">
	        <div class="table-responsive">
		  			<table class="table">
		  				<tr>
		  					<th style="width:25%">Rut:</th>
		  					<td><?php echo $codRutPac; ?></td>
		  					<input type="hidden" name="" id="hdInpRut" class="form-control " value="<?php echo $codRutPac; ?>" >
		  				</tr>
		  				<tr>
		  					<th>Nombre:</th>
		  					<td><input type="" name="" id="inpNombre" class="form-control " value="<?php echo utf8_encode($qrPaciente->Fields('NOMBRE')); ?>" ></td>
		  				</tr>
		  				<tr>
		  					<th>Fecha Nac:</th>
		  					<td><input type='text' class="form-control input-sm" name="inpNacimientoPaciente" id="inpNacimientoPaciente" data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-mask  value="<?php echo date("d-m-Y",strtotime($qrPaciente->Fields('FEC_NACIMI'))) ?>"/></td>
		  				</tr>
		  				<tr>
		  					<th style="">Telefono:</th>
		  					<td>
		  						<div class="input-group mb-3">
		  							<input type="" id="inpTelefono" class="form-control " value="<?php echo utf8_encode($qrPaciente->Fields('FONO')); ?>" >
		  						</div>
		  					</td>
		  				</tr>
		  				<tr>
		  					<th style="">Correo:</th>
		  					<td>
		  						<div class="input-group mb-3">
							      <input type="email" id="inpCorreo" name="inpCorreo" class="form-control " value="<?php echo $qrPaciente->Fields('MAIL'); ?>" >
							    </div>
		  					</td>
		  				</tr>
		  				<tr>
		  					<th>Región:</th>
		  					<td>
  					 	   <select class="form-control input-sm" name="slRegionPaciente" id="slRegionPaciente"  onchange="fnBuscaDomicilioProvincia()">
	          				<option value="<?php echo utf8_encode($qrRegionPac->Fields('region_id')); ?>"><?php echo utf8_encode($qrRegionPac->Fields('region_nombre')); ?></option>
	          				<?php while (!$qrRegion->EOF) {?>
	           				<option value="<?php echo utf8_encode($qrRegion->Fields('region_id')); ?>"><?php echo $qrRegionPac->Fields('region_ordinal') ?> <?php echo utf8_encode($qrRegion->Fields('region_nombre')) ?></option>
				          <?php $qrRegion->MoveNext(); } ?>
				      	</select>
		  					</td>
		  				</tr>
		  				<tr>
		  					<th>Provincia:</th>
		  					<td>
	  						  <select class="form-control input-sm" name="slProvinciaPaciente" id="slProvinciaPaciente" onchange="fnBuscaDomicilioComuna()">
	  						 		<option value="<?php echo utf8_encode($qrProvinciaPac->Fields('provincia_id')); ?>"><?php echo utf8_encode($qrProvinciaPac->Fields('provincia_nombre')); ?></option>
	  						 		<?php while (!$qrProvincia->EOF) {?>
			           		<option value="<?php echo utf8_encode($qrProvincia->Fields('provincia_id')); ?>"><?php echo utf8_encode($qrProvincia->Fields('provincia_nombre')) ?></option>
				          	<?php $qrProvincia->MoveNext(); } ?>
		  						</select>
		  					</td>
		  				</tr>
		  				<tr>
		  					<th>Comuna:</th>
		  					<td>
		  						<select class="form-control input-sm" name="slComunaPaciente" id="slComunaPaciente">
				          	<option value="<?php echo utf8_encode($qrComunaPac->Fields('comuna_id')); ?>"><?php echo utf8_encode($qrComunaPac->Fields('comuna_nombre')); ?></option>
				          	<?php while (!$qrComuna->EOF) {?>
				           				 <option value="<?php echo utf8_encode($qrComuna->Fields('comuna_id')); ?>"><?php echo utf8_encode($qrComuna->Fields('comuna_nombre')) ?></option>
				          	<?php $qrComuna->MoveNext(); } ?>
				        	</select>
		  					</td>
		  				</tr>
		  				<tr>
		  					<th>Dirección:</th>
		  					<td><input type=""  id="inpDireccion" class="form-control" value="<?php echo utf8_encode($qrPaciente->Fields('DIRECCION')); ?>" ></td>
		  				</tr>
		  				<tr>
		  					<th>Previsión:</th>
		  					<td><?php echo utf8_encode($qrPrevi->Fields('PREVISION')); ?>
		  						<input type="hidden"  id="slPrevisonPaciente" name="hdPrevisonPaciente" class="form-control" value="<?php echo utf8_encode($qrPrevi->Fields('ID')); ?>" >
							    
		  					</td>
		  				</tr>
		  				<!-- <tr>
		  					<th>Plan salud:</th>
		  					<td><?php echo utf8_encode($qrPaciente->Fields('PLAN_SALUD')); ?></td>
		  				</tr>
		  				<tr>
		  					<th>Cía Seguro:</th>
		  					<td><?php echo utf8_encode($qrPaciente->Fields('COMPAÑIA_SEGURO')); ?></td>
		  				</tr> -->
		  			</table>
		  		</div>		
			</div>     
	  </div>
	  	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	  	<button type="submit" class="btn btn-success">Guardar Cambios</button>
</form>
	</body>
</html>




<script type="text/javascript">
$('#inpNacimientoPaciente').inputmask('dd-mm-yyyy', { 'placeholder': 'dd-mm-yyyy' })

$(function () {
  $.validator.setDefaults({
    submitHandler: function () {
      aceptarInfoPaciente();
    }
  });
  $('#frmEditaPaciente').validate({
    rules: {
   
      inpCorreo: {
        email:true
      }
    },
    messages: {
	
	    inpCorreo: {
	      email: "Formato Correo Incorrecto"
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
	
function aceptarInfoPaciente(){
  rutPac = $('#hdInpRut').val();
	nombrePac = $('#inpNombre').val();
	nacPac = $('#inpNacimientoPaciente').val();
	telefonoPac = $('#inpTelefono').val();
	correoPac = $('#inpCorreo').val();
	direccionPac = $('#inpDireccion').val();
	region = $('#slRegionPaciente').val();
	provincia = $('#slProvinciaPaciente').val();
	comuna = $('#slComunaPaciente').val();
	prevision = $('#hdPrevisonPaciente').val();

	cadena = 'nombrePac=' + nombrePac +
			 '&rutPac=' + rutPac +
			 '&nacPac=' + nacPac +
			 '&telefonoPac=' + telefonoPac +
			 '&correoPac=' + correoPac +
			 '&direccionPac=' + direccionPac+
			 '&region=' + region +
			 '&provincia=' + provincia +
			 '&comuna=' + comuna+
			 '&prevision=' + prevision;
	$.ajax({
		type:"post",
		data:cadena,
		url:'vistas/modulos/informacionPaciente/modificaInfoPaciente.php',
		success:function(r){
		if (r == 1) {
							$('#modalEditaInformacionPacienteSupervisora').modal('hide');
						Swal.fire({
    					  position: 'top-end',
    					  icon: 'success',
    					  title: 'Modificado con exito',
    					  showConfirmButton: false,
    					  timer: 2000
    				})

    				setTimeout(function (){ $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivaciones.php'); }, 2001);
    				
    	}
		}
	});
	
}

function fnBuscaDomicilioProvincia(){
  $("#slRegionPaciente option:selected").each(function () {
    region=$("#slRegionPaciente").val();
    $.post("vistas/modulos/informacionPaciente/provincias.php", { region: region }, function(data){
        $("#slProvinciaPaciente").html(data);
    });     
  });
}

function fnBuscaDomicilioComuna(){
  $("#slProvinciaPaciente option:selected").each(function () {
    provincia=$("#slProvinciaPaciente").val();
    $.post("vistas/modulos/informacionPaciente/comunas.php", { provincia: provincia }, function(data){
        $("#slComunaPaciente").html(data);
    });     
  });
}

</script>