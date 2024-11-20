<?php
//Connection statement
require_once '../../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

$idDerivacion = $_REQUEST['idDerivacion'];
$idDerivacionCanasta = $_REQUEST['idDerivacionCanasta'];
$codEtapaPatologia = $_REQUEST['etapaPatologia'];
$idEtapaPatologia = $_REQUEST['idEtapaPatologia'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$codRutPac = $qrDerivacion->Fields('COD_RUTPAC');

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$query_qrPrestadores = "SELECT * FROM $MM_oirs_DATABASE.prestador";
$qrPrestadores = $oirs->SelectLimit($query_qrPrestadores) or die($oirs->ErrorMsg());
$totalRows_qrPrestadores = $qrPrestadores->RecordCount();

$query_qrDerivacionCanasta= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_CANASTA_PATOLOGIA = '$idDerivacionCanasta'";
$qrDerivacionCanasta = $oirs->SelectLimit($query_qrDerivacionCanasta) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanasta = $qrDerivacionCanasta->RecordCount();

$codTipoPatologia = $qrDerivacion->Fields('CODIGO_TIPO_PATOLOGIA');

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia WHERE ID_TIPO_PATOLOGIA = '$codTipoPatologia'";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

$codPatologia = $qrDerivacion->Fields('CODIGO_PATOLOGIA');

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE CODIGO_PATOLOGIA = '$codPatologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$codEtapaPatologia = $qrDerivacion->Fields('CODIGO_ETAPA_PATOLOGIA');

$query_qrEtapaPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA = '$codEtapaPatologia'";
$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

$codCanastaPatologia = $qrDerivacion->Fields('CODIGO_CANASTA_PATOLOGIA');

$query_qrCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanastaPatologia'";
$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
        		<div class="input-group mb-3 col-sm-7">
				    <div class="input-group-prepend">
				      <span class="input-group-text">Prestador</span>
				    </div>
				    <select name="slPrestadorCanasta" id="slPrestadorCanasta" class="form-control input-sm" onchange="fnConsultaSiTieneModuloPrestador2()"> 
				        <option value="">Seleccione...</option>
				        <?php while (!$qrPrestadores->EOF) { ?>
				          <option value="<?php echo $qrPrestadores->Fields('RUT_PRESTADOR') ?>"><?php echo utf8_encode($qrPrestadores->Fields('DESC_PRESTADOR')) ?></option>
				        <?php $qrPrestadores->MoveNext(); } ?>
				    </select>
				</div>

		  		<div class="col-sm-5">	
				    <button type="button" class="btn btn-default" onclick="fnCancelarAgregarPrestador('<?php echo $idDerivacion ?>')">Cancelar</button>

				    <button type="button" class="btn btn-success" onclick="fnAgregarPrestadorCanasta(
				    '<?php echo $idDerivacion ?>',
				    '<?php echo $idDerivacionCanasta ?>',
				    '<?php echo $codEtapaPatologia ?>',
				    '<?php echo $idEtapaPatologia ?>',
				    '<?php echo $codTipoPatologia ?>',
				    '<?php echo $qrDerivacion->Fields('COD_RUTPAC'); ?>',
				    '<?php echo $qrDerivacion->Fields('ID_CONVENIO'); ?>',
			    	'<?php echo $qrDerivacion->Fields('CODIGO_PATOLOGIA'); ?>',
			    	'<?php echo $qrDerivacion->Fields('ENFERMERA'); ?>',
			    	'<?php echo $usuario; ?>',
			    	'<?php echo $qrDerivacion->Fields('FECHA_DERIVACION'); ?>',
			    	'<?php echo $qrDerivacion->Fields('CODIGO_CANASTA_PATOLOGIA'); ?>',
			    	'<?php echo $qrDerivacion->Fields('CODIGO_ETAPA_PATOLOGIA'); ?>',
			    	'<?php echo $qrDerivacionCanasta->Fields('FECHA_CANASTA'); ?>',
			    	'<?php echo $qrDerivacionCanasta->Fields('FECHA_LIMITE'); ?>',
			    	'<?php echo $qrDerivacionCanasta->Fields('ID_CANASTA_PATOLOGIA'); ?>',
			    	'<?php echo $qrDerivacionCanasta->Fields('ID_ETAPA_PATOLOGIA'); ?>',
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
			    	'<?php echo utf8_encode($qrPaciente->Fields('SEXO')) ?>',
			    	'<?php echo utf8_encode($qrDerivacion->Fields('ID_PATOLOGIA')) ?>')">Cambiar Prestador</button>
	  			</div>  
  			</div>    
	  	</div>
	  	<!-- lleno este hidden desde la funcion fnConsultaSiTieneModuloPrestador, para capturarlo en la funcion  fnAsignarPrestadorCaso y evaluar si llama o no a la API que le manda la derivacion-->
	  		<input type="hidden" id="hdModuloPrestador">
	</body>
</html>

<script>
	function fnConsultaSiTieneModuloPrestador2(){
		slAsignarPrestadorDerivacion = $('#slPrestadorCanasta').val();

		cadena = 'slAsignarPrestadorDerivacion='+slAsignarPrestadorDerivacion;

		$.ajax({
				type:"post",
				data:cadena,
				url:'vistas/modulos/pacientesPp/asignarPrestadorCaso/consultaSiTieneModuloPrestador.php', 
				success:function(r){
					//lleno el hidden con la respuesta si tiene modulo de prestador o no
					$('#hdModuloPrestador').val(r);				
				}
			});
	}
// pone fecha actual en input de fechaCanasta
// var fecha = new Date();
// document.getElementById("fechaCanasta").value = fecha.toJSON().slice(0,10);

function fnCancelarAgregarPrestador(idDerivacion){
	$('#dvfrmDetalleDerivacion').load('vistas/modulos/pacientesPp/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
}

function fnAgregarPrestadorCanasta(idDerivacion,idDerivacionCanasta,codEtapaPatologia,idEtapaPatologia, codTipoPatologia,codRutPac,idConvenio,codPatologia,enfermera,usuario,fecha_derivacion,codCanastaPatologia,codEtapaPatologia,fechaCanastaInicial,fechaFinGarantia,idTablaCanastaPatologia,idTablaEtapaPatologia, nombrePaciente,nacimientoPaciente,fonoPaciente,direccionPaciente,regionPaciente,provinciaPaciente,comunaPaciente,mailPaciente,ocupacionPaciente,previsionPaciente,planSaludPaciente,seguroComplementarioPaciente,companiaSeguroPaciente,sexo,idPatologia){
	slPrestadorCanasta = $('#slPrestadorCanasta').val();
	moduloPrestador = $('#hdModuloPrestador').val();

	if (slPrestadorCanasta == '') {
		Swal.fire({
		  position: 'top-end',
		  icon: 'warning',
		  title: 'Debe seleccionar un prestador',
		  showConfirmButton: false,
		  timer: 1500
		})
	}else{
	cadena = 'idDerivacion=' + idDerivacion +
			 '&idDerivacionCanasta=' + idDerivacionCanasta +
			 '&codEtapaPatologia=' + codEtapaPatologia +
			 '&idEtapaPatologia=' + idEtapaPatologia +
			 '&slPrestadorCanasta=' + slPrestadorCanasta;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/pacientesPp/derivacion/modals/agregarPrestadorCanasta.php',
			success:function(prestador){
				
					// swal("Todo bien!", "Prestador agregado con exito", "success");
					if (moduloPrestador == 'si' ) {
						if (prestador==19) {
							
						//fnAsignarDerivacionPrimerPrestador(idDerivacion,codTipoPatologia,codRutPac,idConvenio,codPatologia,enfermera,usuario,fecha_derivacion,codCanastaPatologia,codEtapaPatologia,fechaCanastaInicial,fechaFinGarantia,idTablaCanastaPatologia,idTablaEtapaPatologia,nombrePaciente,nacimientoPaciente,fonoPaciente,direccionPaciente,regionPaciente,provinciaPaciente,comunaPaciente,mailPaciente,ocupacionPaciente,previsionPaciente,planSaludPaciente,seguroComplementarioPaciente,companiaSeguroPaciente,sexo,prestador,idPatologia);
						}if (prestador==48) {
							
						// fnAsignarDerivacionAPrestadorPorApi(idDerivacion,codTipoPatologia,codRutPac,idConvenio,codPatologia,enfermera,usuario,fecha_derivacion,codCanastaPatologia,codEtapaPatologia,fechaCanastaInicial,fechaFinGarantia,idTablaCanastaPatologia,idTablaEtapaPatologia,nombrePaciente,nacimientoPaciente,fonoPaciente,direccionPaciente,regionPaciente,provinciaPaciente,comunaPaciente,mailPaciente,ocupacionPaciente,previsionPaciente,planSaludPaciente,seguroComplementarioPaciente,companiaSeguroPaciente,sexo);
					}
				}

					$('#dvfrmDetalleDerivacion').load('vistas/modulos/pacientesPp/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
				}
			});

		// cadena = "idDerivacion=" + idDerivacion +
		// 		 "&codTipoPatologia=" + codTipoPatologia +
		// 		 "&codRutPac=" + codRutPac +
		// 		 "&idConvenio=" + idConvenio +
		// 		 "&codPatologia=" + codPatologia + 
		// 		 "&enfermera=" + enfermera +
		// 		 "&fecha_derivacion=" + fecha_derivacion +
		// 		 "&codCanastaPatologia=" + codCanastaPatologia +
		// 		 "&codEtapaPatologia=" + codEtapaPatologia +
		// 		 "&fechaCanastaInicial=" + fechaCanastaInicial +
		// 		 "&fechaFinGarantia=" + fechaFinGarantia +
		// 		 "&idTablaCanastaPatologia=" + idTablaCanastaPatologia +
		// 		 "&idTablaEtapaPatologia=" + idTablaEtapaPatologia +
		// 		 "&nombrePaciente=" + nombrePaciente +
		// 		 "&nacimientoPaciente=" + nacimientoPaciente +
		// 		 "&fonoPaciente=" + fonoPaciente +
		// 		 "&direccionPaciente=" + direccionPaciente +
		// 		 "&regionPaciente=" + regionPaciente +
		// 		 "&provinciaPaciente=" + provinciaPaciente +
		// 		 "&comunaPaciente=" + comunaPaciente +
		// 		 "&mailPaciente=" + mailPaciente +
		// 		 "&ocupacionPaciente=" + ocupacionPaciente +
		// 		 "&previsionPaciente=" + previsionPaciente +
		// 		 "&planSaludPaciente=" + planSaludPaciente +
		// 		 "&seguroComplementarioPaciente=" + seguroComplementarioPaciente +
		// 		 "&companiaSeguroPaciente=" + companiaSeguroPaciente +
		// 		 "&usuario=" + usuario +
		// 		 '&slAsignarPrestadorDerivacion=' + slPrestadorCanasta +
		// 		 "&sexo=" + sexo;

		// 	$.ajax({
		// 		type:"post",
		// 		data:cadena,
		// 		url:'vistas/modulos/mails/notificaDerivacionAtd.php',
		// 		success:function(r){

		// 		}
		// 	});
	}
}

function fnAsignarDerivacionAPrestadorPorApi(idDerivacionRedGes,codTipoPatologia,codRutPac,idConvenio,codPatologia,enfermera,usuario,fecha_derivacion,codCanastaPatologia,codEtapaPatologia,fechaCanastaInicial,fechaFinGarantia,idTablaCanastaPatologia,idTablaEtapaPatologia,nombrePaciente,nacimientoPaciente,fonoPaciente,direccionPaciente,regionPaciente,provinciaPaciente,comunaPaciente,mailPaciente,ocupacionPaciente,previsionPaciente,planSaludPaciente,seguroComplementarioPaciente,companiaSeguroPaciente,sexo){

	cadena = "idDerivacionRedGes=" + idDerivacionRedGes +
			 "&codTipoPatologia=" + codTipoPatologia +
			 "&codRutPac=" + codRutPac +
			 "&idConvenio=" + idConvenio +
			 "&codPatologia=" + codPatologia + 
			 "&enfermera=" + enfermera +
			 "&fecha_derivacion=" + fecha_derivacion +
			 "&codCanastaPatologia=" + codCanastaPatologia +
			 "&codEtapaPatologia=" + codEtapaPatologia +
			 "&fechaCanastaInicial=" + fechaCanastaInicial +
			 "&fechaFinGarantia=" + fechaFinGarantia +
			 "&idTablaCanastaPatologia=" + idTablaCanastaPatologia +
			 "&idTablaEtapaPatologia=" + idTablaEtapaPatologia +
			 "&nombrePaciente=" + nombrePaciente +
			 "&nacimientoPaciente=" + nacimientoPaciente +
			 "&fonoPaciente=" + fonoPaciente +
			 "&direccionPaciente=" + direccionPaciente +
			 "&regionPaciente=" + regionPaciente +
			 "&provinciaPaciente=" + provinciaPaciente +
			 "&comunaPaciente=" + comunaPaciente +
			 "&mailPaciente=" + mailPaciente +
			 "&ocupacionPaciente=" + ocupacionPaciente +
			 "&previsionPaciente=" + previsionPaciente +
			 "&planSaludPaciente=" + planSaludPaciente +
			 "&seguroComplementarioPaciente=" + seguroComplementarioPaciente +
			 "&companiaSeguroPaciente=" + companiaSeguroPaciente +
			 "&usuario=" + usuario +
			 "&sexo=" + sexo;

	$.ajax({
	    type: "POST",
	    url: "https://redges.cl/domicilio/api/insertaDerivacion.php", 
	    data: cadena,
	    dataType:'json',
	    success: function(r) {
	      if (r == 1) {
	        swal("Todo bien!", "Derivación enviada a prestador por sistema", "success");
	      }else{
	        alert('no ingreso dato para insertar (msj enviado desde historialClinico)');
	      }
	      
	    }
	});
}
 


 function fnAsignarDerivacionPrimerPrestador(idDerivacionPp,codTipoPatologia,codRutPac,idConvenio,codPatologia,enfermera,usuario,fecha_derivacion,codCanastaPatologia,codEtapaPatologia,fechaCanastaInicial,fechaFinGarantia,idTablaCanastaPatologia,idTablaEtapaPatologia,nombrePaciente,nacimientoPaciente,fonoPaciente,direccionPaciente,regionPaciente,provinciaPaciente,comunaPaciente,mailPaciente,ocupacionPaciente,previsionPaciente,planSaludPaciente,seguroComplementarioPaciente,companiaSeguroPaciente,sexo,prestador,idPatologia){

	cadena = "idDerivacionPp=" + idDerivacionPp +
			 "&codTipoPatologia=" + codTipoPatologia +
			 "&codRutPac=" + codRutPac +
			 "&idConvenio=" + idConvenio +
			 "&codPatologia=" + codPatologia + 
			 "&enfermera=" + enfermera +
			 "&fecha_derivacion=" + fecha_derivacion +
			 "&codCanastaPatologia=" + codCanastaPatologia +
			 "&codEtapaPatologia=" + codEtapaPatologia +
			 "&fechaCanastaInicial=" + fechaCanastaInicial +
			 "&fechaFinGarantia=" + fechaFinGarantia +
			 "&idTablaCanastaPatologia=" + idTablaCanastaPatologia +
			 "&idTablaEtapaPatologia=" + idTablaEtapaPatologia +
			 "&nombrePaciente=" + nombrePaciente +
			 "&nacimientoPaciente=" + nacimientoPaciente +
			 "&fonoPaciente=" + fonoPaciente +
			 "&direccionPaciente=" + direccionPaciente +
			 "&regionPaciente=" + regionPaciente +
			 "&provinciaPaciente=" + provinciaPaciente +
			 "&comunaPaciente=" + comunaPaciente +
			 "&mailPaciente=" + mailPaciente +
			 "&ocupacionPaciente=" + ocupacionPaciente +
			 "&previsionPaciente=" + previsionPaciente +
			 "&planSaludPaciente=" + planSaludPaciente +
			 "&seguroComplementarioPaciente=" + seguroComplementarioPaciente +
			 "&companiaSeguroPaciente=" + companiaSeguroPaciente +
			 "&usuario=" + usuario +
			 "&sexo=" + sexo+
			 "&prestador=" + prestador +
			 "&idPatologia=" + idPatologia; 


	$.ajax({
	    type: "POST",
	    url: "vistas/modulos/derivacionesCRSS/phpInsertaDerivacionCrss.php", 
	    data: cadena,
	  
	    success: function(r) {
	      if (r == 1) {
	        swal("Todo bien!", "Derivación enviada a prestador por sistemassss", "success");

	          $('#dvfrmDetalleDerivacion').load('vistas/modulos/pacientesPp/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacionPp);
	      }else{
	        alert('no ingreso dato para insertar (msj enviado desde historialClinico)');
	      }
	      
	    }


	});
}

	
</script>



