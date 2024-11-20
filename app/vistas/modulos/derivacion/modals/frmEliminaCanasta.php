<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';

$idDerivacion = $_REQUEST['idDerivacion'];
$idEtapaPatologia = $_REQUEST['idEtapaPatologia'];
$canasta = $_REQUEST['canasta'];
$prestador = $_REQUEST['prestador'];
$idBitacora = $_REQUEST['idBitacora'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$codEtapaPatologia = $_REQUEST['etapaPatologia'];

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

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">

        		<div class="input-group mb-3 col-sm-9">
				    <div class="input-group-prepend">
				      <span class="input-group-text">Motivo eliminación</span>
				    </div>
				    <input type="text" id="motivoEliminaCanasta" class="form-control input-sm">
				</div>
			
		  		<div class="col-sm-3">	
				    <button type="button" class="btn btn-default" onclick="fnCancelarAgregarCanasta('<?php echo $idDerivacion ?>')">Cancelar</button>
				    <button type="button" class="btn btn-danger" onclick="fnEliminarCanasta('<?php echo $idDerivacion ?>','<?php echo $idEtapaPatologia ?>','<?php echo $codEtapaPatologia ?>','<?php echo $canasta ?>','<?php echo $prestador ?>','<?php echo $idBitacora ?>')">Eliminar Canasta</button>
	  			</div>  
  			</div>    
	  	</div>
	</body>
</html>

<script>
// pone fecha actual en input de fechaCanasta
// var fecha = new Date();
// document.getElementById("fechaCanasta").value = fecha.toJSON().slice(0,10);

function fnCancelarAgregarCanasta(idDerivacion){
	$('#dvfrmDetalleDerivacion').load('vistas/modulos/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
}

function fnEliminarCanasta(idDerivacion,idEtapaPatologia,codEtapaPatologia,canasta,prestador,idBitacora){

	motivoEliminaCanasta = $('#motivoEliminaCanasta').val(); 

	if (motivoEliminaCanasta == '') {
		Swal.fire({
		  position: 'top-end',
		  icon: 'warning',
		  title: 'Debe ingresar un motivo!!!',
		  showConfirmButton: false,
		  timer: 1500
		})
	}else{
	cadena = 'idDerivacion=' + idDerivacion +
			 '&canasta=' + canasta +
			 '&codEtapaPatologia=' + codEtapaPatologia +
			 '&idEtapaPatologia=' + idEtapaPatologia +
			 '&prestador=' + prestador +
			 '&idBitacora=' + idBitacora +
			 '&motivoEliminaCanasta=' + motivoEliminaCanasta;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/derivacion/modals/eliminarCanasta.php',
			success:function(r){
				if (r == 1) {
					swal("Todo bien!", "Canasta eliminada con exito", "success");
				$('#dvfrmDetalleDerivacion').load('vistas/modulos/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
	    	}
				
			}
		});
	}
}
 

	
</script>



