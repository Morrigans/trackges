<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';

$idDerivacion = $_REQUEST['idDerivacion'];
$idEtapaPatologia = $_REQUEST['idEtapaPatologia'];
$canasta = $_REQUEST['idDerivacionCanasta'];
$prestador = $_REQUEST['prestador'];
date_default_timezone_set('America/Santiago');
$fechaHoy= date('Y-m-d');
$codEtapaPatologia = $_REQUEST['etapaPatologia'];


$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$query_qrCanastaDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_CANASTA_PATOLOGIA = '$canasta'";
$qrCanastaDerivacion = $oirs->SelectLimit($query_qrCanastaDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrCanastaDerivacion = $qrCanastaDerivacion->RecordCount();	

$fechaLimite=$qrCanastaDerivacion->Fields('FECHA_LIMITE');
$idMotivo=$qrCanastaDerivacion->Fields('MOTIVO_FIN_CANASTA');

$query_select = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_ETAPA_PATOLOGIA='$codEtapaPatologia' ORDER BY DESC_CANASTA_PATOLOGIA ASC";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();	

$patologia = $qrDerivacion->Fields('CODIGO_PATOLOGIA');

$query_qrPatologia = "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE CODIGO_PATOLOGIA='$patologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$query_qrPrestadores = "SELECT * FROM $MM_oirs_DATABASE.prestador_asignado WHERE ID_DERIVACION='$idDerivacion'";
$qrPrestadores = $oirs->SelectLimit($query_qrPrestadores) or die($oirs->ErrorMsg());
$totalRows_qrPrestadores = $qrPrestadores->RecordCount();	

$query_qrMuestraMotivos= "SELECT * FROM $MM_oirs_DATABASE.motivos_fin_canastas";
$qrMuestraMotivos = $oirs->SelectLimit($query_qrMuestraMotivos) or die($oirs->ErrorMsg());
$totalRows_qrMuestraMotivos = $qrMuestraMotivos->RecordCount();

$query_qrMuestraMotivo= "SELECT * FROM $MM_oirs_DATABASE.motivos_fin_canastas WHERE ID_MOTIVO = '$idMotivo'";
$qrMuestraMotivo = $oirs->SelectLimit($query_qrMuestraMotivo) or die($oirs->ErrorMsg());
$totalRows_qrMuestraMotivo = $qrMuestraMotivo->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
				<div class="input-group mb-3 col-sm-3">
				  <div class="input-group-prepend">
				    <span class="input-group-text">Fecha Fin Canasta</span>
				  </div>
				  <input type='date' class="form-control input-sm" name="fechaFinCanasta" id="fechaFinCanasta" onchange="fnFinCanastaVenc()" />
				</div>

        		<div class="input-group mb-3 col-sm-3">
				    <div class="input-group-prepend">
				      <span class="input-group-text">Comentario</span>
				    </div>
				    <input type="text" id="comentarioCambioEstadoCanasta" class="form-control input-sm" value="<?php echo $qrCanastaDerivacion->Fields('OBSERVACION') ?>">
				</div>
	  			<div id="dvSlMotivo" class="input-group mb-3 col-sm-3">
			        <div class="input-group-prepend">
			          <span class="input-group-text">Motivo Vencimiento</span>
			        </div>
			        <select name="slMotivoFinCanasta" id="slMotivoFinCanasta" class="form-control input-sm">
			        	<?php
			        		if($qrCanastaDerivacion->Fields('MOTIVO_FIN_CANASTA') != 0){?>
			        			<option value="<?php echo $idMotivo; ?>"><?php echo $qrMuestraMotivo->Fields('DESC_MOTIVO'); ?></option>
			        		<?php }else{?>
								<option value="">Seleccione...</option>
			        		<?php }
			        	?>
			            
			             <?php 
			             while (!$qrMuestraMotivos->EOF) {?>
			               <option value="<?php echo $qrMuestraMotivos->Fields('ID_MOTIVO'); ?>"><?php echo $qrMuestraMotivos->Fields('DESC_MOTIVO'); ?></option>
			            <?php $qrMuestraMotivos->MoveNext(); } ?> 
			        </select>
		    	</div> 
		    	<div class="col-sm-1">	
				    <button type="button" class="btn btn-default" onclick="fnCancelarAgregarCanasta('<?php echo $idDerivacion ?>')">Cancelar</button>
	  			</div> 
	  			<div class="col-sm-2">					   
				    <button type="button" class="btn btn-danger" onclick="fnFinalizarCanasta('<?php echo $idDerivacion ?>','<?php echo $idEtapaPatologia ?>','<?php echo $codEtapaPatologia ?>','<?php echo $canasta ?>','<?php echo $prestador ?>')">Finalizar Canasta</button>
	  			</div> 
  			</div>    
	  	</div>
	  	
	  	<input type="hidden" id="hdFechaLimite" value="<?php echo $fechaLimite  ?>" id name="">
	</body>
</html>

<script>
$("#dvSlMotivo").hide();

function fnFinCanastaVenc(){	
	fechaLimite=$('#hdFechaLimite').val();
	fechaFinCanasta = $('#fechaFinCanasta').val();

	if (fechaFinCanasta > fechaLimite && fechaLimite != '0000-00-00') { //si fechaLimite no tiene no pide el motivo
		$("#dvSlMotivo").show(); 
	}else{
		$("#dvSlMotivo").hide();
	}
}




// pone fecha actual en input de fechaCanasta
// var fecha = new Date();
// document.getElementById("fechaCanasta").value = fecha.toJSON().slice(0,10);

function fnCancelarAgregarCanasta(idDerivacion){
	$('#dvfrmDetalleDerivacion').load('vistas/modulos/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
}

function fnFinalizarCanasta(idDerivacion,idEtapaPatologia,codEtapaPatologia,canasta,prestador){
	slCanastaPatologiaDerivacion = $('#slCanastaPatologiaDerivacion').val();
	comentarioCambioEstadoCanasta = $('#comentarioCambioEstadoCanasta').val();
	slPrestadorCanasta = $('#slPrestadorCanasta').val();
	slMotivoFinCanasta = $('#slMotivoFinCanasta').val();
	fechaFinCanasta = $('#fechaFinCanasta').val();

	if (fechaFinCanasta == '' || comentarioCambioEstadoCanasta == '') {
		Swal.fire({
		  position: 'top-end',
		  icon: 'warning',
		  title: 'Debe completar todos los campos',
		  showConfirmButton: false,
		  timer: 1500
		})
	}else{
	cadena = 'idDerivacion=' + idDerivacion +
			 '&canasta=' + canasta +
			 '&codEtapaPatologia=' + codEtapaPatologia +
			 '&idEtapaPatologia=' + idEtapaPatologia +
			 '&fechaFinCanasta=' + fechaFinCanasta +
			 '&prestador=' + prestador +
			 '&comentarioCambioEstadoCanasta=' + comentarioCambioEstadoCanasta +
			 '&slMotivoFinCanasta=' + slMotivoFinCanasta;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/derivacion/modals/finalizarCanasta.php',
			success:function(r){
				if (r == 1) {
					swal("Todo bien!", "Canasta finalizada con exito", "success");
				$('#dvfrmDetalleDerivacion').load('vistas/modulos/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
	    	}
				
			}
		});
	}
}
 

	
</script>



