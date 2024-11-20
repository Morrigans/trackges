<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idPatologia = $qrDerivacion->Fields('ID_PATOLOGIA');

$query_qrPatologia = "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE ID_PATOLOGIA='$idPatologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$codPatologia = $qrPatologia->Fields('CODIGO_PATOLOGIA');

$query_select = "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_PATOLOGIA='$codPatologia' ORDER BY DESC_ETAPA_PATOLOGIA ASC";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();	

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
				<div class="input-group mb-3 col-sm-7">
				    <div class="input-group-prepend">
				      <span class="input-group-text">Etapa</span>
				    </div>
				    <select name="slEtapaPatologiaDerivacion" id="slEtapaPatologiaDerivacion" class="form-control input-sm">
				        <option value="">Seleccione...</option>
				        <?php while (!$select->EOF) {?>
				          <option value="<?php echo $select->Fields('ID_ETAPA_PATOLOGIA') ?>"><?php echo utf8_encode($select->Fields('DESC_ETAPA_PATOLOGIA')) ?></option>
				        <?php $select->MoveNext(); } ?>
				    </select>
				</div>
				<div class="col-sm-5">	
				    <button type="button" class="btn btn-default" onclick="fnCancelarAgregarEtapa('<?php echo $idDerivacion ?>')">Cancelar</button>
				    <button type="button" class="btn btn-success" onclick="fnAgregarEtapa('<?php echo $idDerivacion ?>')">Agregar Etapa</button>
	  			</div>  
			</div>  
					   
	  	</div>
	</body>
</html>

<script>
function fnCancelarAgregarEtapa(idDerivacion){
	$('#dvfrmDetalleDerivacion').load('vistas/modulos/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
}

function fnAgregarEtapa(idDerivacion){
	slEtapaPatologiaDerivacion = $('#slEtapaPatologiaDerivacion').val();

	if (slEtapaPatologiaDerivacion == '') {
		Swal.fire({
		  position: 'top-end',
		  icon: 'warning',
		  title: 'Debe seleccionar una etapa',
		  showConfirmButton: false,
		  timer: 1500
		})
	}else{
		cadena = 'idDerivacion=' + idDerivacion +
				 '&slEtapaPatologiaDerivacion=' + slEtapaPatologiaDerivacion;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/derivacion/modals/agregarEtapa.php',
			success:function(r){
				if (r == 1) {
					swal("Todo bien!", "Etapa agregada con exito", "success");   
				$('#dvfrmDetalleDerivacion').load('vistas/modulos/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
	    	}
				
			}
		});
	}
}
 

	
</script>



