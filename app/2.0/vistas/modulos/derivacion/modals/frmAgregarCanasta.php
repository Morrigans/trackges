<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';

$idDerivacion = $_REQUEST['idDerivacion'];
$idEtapaPatologia = $_REQUEST['idEtapaPatologia'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$codRutPac = $qrDerivacion->Fields('COD_RUTPAC');

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$codTipoPatologia = $qrDerivacion->Fields('CODIGO_TIPO_PATOLOGIA');

$codEtapaPatologia = $_REQUEST['etapaPatologia'];

$query_select = "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_ETAPA_PATOLOGIA='$codEtapaPatologia' ORDER BY DESC_CANASTA_PATOLOGIA ASC";
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();	

$patologia = $qrDerivacion->Fields('CODIGO_PATOLOGIA');

$query_qrPatologia = "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE CODIGO_PATOLOGIA='$patologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$query_qrCanastaUltimaAsignada = "SELECT MAX(ID_CANASTA_PATOLOGIA) as ID_CANASTA_PATOLOGIA FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION = '$idDerivacion'";
$qrCanastaUltimaAsignada = $oirs->SelectLimit($query_qrCanastaUltimaAsignada) or die($oirs->ErrorMsg());
$totalRows_qrCanastaUltimaAsignada = $qrCanastaUltimaAsignada->RecordCount();	

$idUltimoIdCanastaDerivacion = $qrCanastaUltimaAsignada->Fields('ID_CANASTA_PATOLOGIA');

$query_qrPrestadorUltimoAsignado = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_CANASTA_PATOLOGIA = '$idUltimoIdCanastaDerivacion'";
$qrPrestadorUltimoAsignado = $oirs->SelectLimit($query_qrPrestadorUltimoAsignado) or die($oirs->ErrorMsg());
$totalRows_qrPrestadorUltimoAsignado = $qrPrestadorUltimoAsignado->RecordCount();

$rutUltimoPrestador = $qrPrestadorUltimoAsignado->Fields('RUT_PRESTADOR');

$query_qrNomPrestadorUltimoAsignado = "SELECT * FROM $MM_oirs_DATABASE.prestador WHERE RUT_PRESTADOR = '$rutUltimoPrestador'";
$qrNomPrestadorUltimoAsignado = $oirs->SelectLimit($query_qrNomPrestadorUltimoAsignado) or die($oirs->ErrorMsg());
$totalRows_qrNomPrestadorUltimoAsignado = $qrNomPrestadorUltimoAsignado->RecordCount();

$query_qrPrestadores = "SELECT * FROM $MM_oirs_DATABASE.prestador WHERE ID_PRESTADOR = '19'";
$qrPrestadores = $oirs->SelectLimit($query_qrPrestadores) or die($oirs->ErrorMsg());
$totalRows_qrPrestadores = $qrPrestadores->RecordCount();	

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
        		<div class="input-group mb-3 col-sm-3">
				    <div class="input-group-prepend">
				      <span class="input-group-text">Prestador</span>
				    </div>
				    <select name="slPrestadorCanasta" id="slPrestadorCanasta" class="form-control input-sm"> 
				        <?php while (!$qrPrestadores->EOF) { ?>
				          <option value="<?php echo $qrPrestadores->Fields('RUT_PRESTADOR') ?>"><?php echo utf8_encode($qrPrestadores->Fields('DESC_PRESTADOR')) ?></option>
				        <?php $qrPrestadores->MoveNext(); } ?>
				    </select>
				</div>

				<div class="input-group mb-3 col-sm-3">
				    <div class="input-group-prepend">
				      <span class="input-group-text">Canasta</span>
				    </div>
				    <select name="slCanastaPatologiaDerivacion" id="slCanastaPatologiaDerivacion" class="form-control input-sm" onchange="fnReglasGenerales(this.value,'<?php echo $idDerivacion ?>')">
				        <option value="">Seleccione...</option>
				        <?php while (!$select->EOF) {?>
				          <option value="<?php echo $select->Fields('ID_CANASTA_PATOLOGIA') ?>"><?php echo utf8_encode($select->Fields('DESC_CANASTA_PATOLOGIA')) ?></option>
				        <?php $select->MoveNext(); } ?>
				    </select>
				</div>

				<div class="input-group mb-3 col-sm-3">
				  <!-- <div class="input-group-prepend">
				    <span class="input-group-text">Fecha Derivación</span>
				  </div> -->
				  <input type='date' class="form-control input-sm" name="fechaCanasta" id="fechaCanasta" />
				</div>
			
		  		<div class="col-sm-3">	
				    <button type="button" class="btn btn-default" onclick="fnCancelarAgregarCanasta('<?php echo $idDerivacion ?>')">Cancelar</button>
				    <button type="button" class="btn btn-success" onclick="
				    fnAgregarCanasta(
				    '<?php echo $idDerivacion ?>',
				    '<?php echo $codEtapaPatologia ?>',
				    '<?php echo $idEtapaPatologia ?>',
				    '<?php echo $codTipoPatologia ?>',
				    '<?php echo $qrDerivacion->Fields('COD_RUTPAC'); ?>',
				    '<?php echo $qrDerivacion->Fields('ID_CONVENIO'); ?>',
				    '<?php echo $qrDerivacion->Fields('CODIGO_PATOLOGIA'); ?>',
				    '<?php echo $qrDerivacion->Fields('ENFERMERA'); ?>',
				    '<?php echo $qrDerivacion->Fields('FECHA_DERIVACION'); ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('NOMBRE')) ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('FEC_NACIMI')) ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('FONO')) ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('DIRECCION')) ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('REGION')) ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('PROVINCIA')) ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('COMUNA')) ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('MAIL')) ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('OCUPACION')) ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('PREVISION')) ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('PLAN_SALUD')) ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('SEGURO_COMPLEMENTARIO')) ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('COMPANIA_SEGURO')) ?>',
				    '<?php echo utf8_encode($qrPaciente->Fields('SEXO')) ?>'
				    )">Agregar Canasta</button>
	  			</div>  
  			</div>    
	  	</div>
	</body>
</html>

<script>
// pone fecha actual en input de fechaCanasta
// var fecha = new Date();
// document.getElementById("fechaCanasta").value = fecha.toJSON().slice(0,10);

function fnReglasGenerales(canasta,idDerivacion){
	//captura el texto que se selecciona en el select
	nomCanasta = $('select[name="slCanastaPatologiaDerivacion"] option:selected').text();

	cadena = 'canasta=' + canasta +
			 '&idDerivacion=' + idDerivacion; 
	$.ajax({
		type:"post",
		data:cadena,
		url:'vistas/modulos/derivacion/modals/reglas/reglasGenerales.php',
		success:function(resultado){
			 var r=resultado.split('|');
                error = r[0];
                nomCanastaDependencia = r[1];

			if (error == 'dependencia') {
    			Swal.fire(
				  'Canasta de dependencia sin finalizar!',
				  'La canasta "'+nomCanasta+'" depende de "'+nomCanastaDependencia+'" la cual no esta finalizada!, no puede asignar esta canasta',
				  'warning'
				)
				$('#slCanastaPatologiaDerivacion').val('');
    		}
    		else if (error == 'repite'){
    			Swal.fire(
				  'Ya esta Asignada!',
				  'La canasta "'+nomCanasta+'" ya esta asignada a la derivación, por regla no puede repetirse!',
				  'warning'
				)
    			$('#slCanastaPatologiaDerivacion').val('');
    		}
    		else if (error == 'simultanea'){
    			Swal.fire(
				  'Tiene Canastas sin finalizar!',
				  'Canasta "'+nomCanasta+'" por regla no puede correr de forma simultanea con otras canastas!',
				  'warning'
				)
    			$('#slCanastaPatologiaDerivacion').val('');
    		}else{
    			//caso exitoso, pasa las reglas
    		}
			
		}
	});	
}


function fnCancelarAgregarCanasta(idDerivacion){
	$('#dvfrmDetalleDerivacion').load('vistas/modulos/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
}

function fnAgregarCanasta(idDerivacion,codEtapaPatologia,idEtapaPatologia,codTipoPatologia,codRutPac,idConvenio,codPatologia,enfermera,fechaDerivacion,nombrePaciente,nacimientoPaciente,fonoPaciente,direccionPaciente,regionPaciente,provinciaPaciente,comunaPaciente,mailPaciente,ocupacionPaciente,previsionPaciente,planSaludPaciente,seguroComplementarioPaciente,companiaSeguroPaciente,sexo){
	slCanastaPatologiaDerivacion = $('#slCanastaPatologiaDerivacion').val();
	slPrestadorCanasta = $('#slPrestadorCanasta').val();
	fechaCanasta = $('#fechaCanasta').val();

	if (slCanastaPatologiaDerivacion == '' || slPrestadorCanasta == '') {
		Swal.fire({
		  position: 'top-end',
		  icon: 'warning',
		  title: 'Debe seleccionar todas las opciones',
		  showConfirmButton: false,
		  timer: 1500
		})
	}else{
	cadena = 'idDerivacion=' + idDerivacion +
			 '&slCanastaPatologiaDerivacion=' + slCanastaPatologiaDerivacion +
			 '&codEtapaPatologia=' + codEtapaPatologia +
			 '&idEtapaPatologia=' + idEtapaPatologia +
			 '&fechaCanasta=' + fechaCanasta +
			 '&slPrestadorCanasta=' + slPrestadorCanasta;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/derivacion/modals/agregarCanasta.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'Canasta agregada con exito',
					  showConfirmButton: false,
					  timer: 2000
					})
					$('#dvfrmDetalleDerivacion').load('vistas/modulos/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
				}
			}
		});
	}
}

</script>



