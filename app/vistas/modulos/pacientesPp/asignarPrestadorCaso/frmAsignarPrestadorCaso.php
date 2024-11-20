<?php
//Connection statement
require_once '../../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../..index.php');
exit; }

$usuario = $_SESSION['dni'];

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$codRutPac = $qrDerivacion->Fields('COD_RUTPAC');

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

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

$query_qrAsignarEnfermeria= "SELECT * FROM $MM_oirs_DATABASE.login order by NOMBRE asc";
$qrAsignarEnfermeria = $oirs->SelectLimit($query_qrAsignarEnfermeria) or die($oirs->ErrorMsg());
$totalRows_qrAsignarEnfermeria = $qrAsignarEnfermeria->RecordCount();

$query_qrAsignarPrestador= "SELECT * FROM $MM_oirs_DATABASE.prestador order by DESC_PRESTADOR asc";
$qrAsignarPrestador = $oirs->SelectLimit($query_qrAsignarPrestador) or die($oirs->ErrorMsg());
$totalRows_qrAsignarPrestador = $qrAsignarPrestador->RecordCount();

$codConvenio = $qrDerivacion->Fields('ID_CONVENIO');

$query_qrConvenio= "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$codConvenio'";
$qrConvenio = $oirs->SelectLimit($query_qrConvenio) or die($oirs->ErrorMsg());
$totalRows_qrConvenio = $qrConvenio->RecordCount();

$query_qrDerivacionCanasta= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
$qrDerivacionCanasta = $oirs->SelectLimit($query_qrDerivacionCanasta) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanasta = $qrDerivacionCanasta->RecordCount();

$query_qrDerivacionEtapa= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_etapas_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacionEtapa = $oirs->SelectLimit($query_qrDerivacionEtapa) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionEtapa = $qrDerivacionEtapa->RecordCount();

//consulta que busca canasta inicial de la derivacion para pasarla a la API de prestador para insertarse en derivaciones_canastas de prestador
$query_qrDerivacionCanastaInicial= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_DERIVACION = '$idDerivacion' and INICIAL ='si'";
$qrDerivacionCanastaInicial = $oirs->SelectLimit($query_qrDerivacionCanastaInicial) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanastaInicial = $qrDerivacionCanastaInicial->RecordCount();


?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Asignar Prestador:</h2></div>
	        		<div class="col-md-6" id="dvInfoVentanasOpcionesAsignaPrestador"></div>	
						<div class="col-md-6">
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Asignar a</span>
							    </div>
							    <select name="slAsignarPrestadorDerivacion" id="slAsignarPrestadorDerivacion" class="form-control input-sm" onchange="fnCambiaComentarioBitacora(this.value,'<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>','<?php echo utf8_encode($qrPaciente->Fields('NOMBRE')); ?>','<?php echo $qrPaciente->Fields('COD_RUTPAC'); ?>','<?php echo $canasta; ?>'), fnConsultaSiTieneModuloPrestador()">
							        <option value="">Seleccione...</option>
							        <?php while (!$qrAsignarPrestador->EOF) {?>
							          <option value="<?php echo $qrAsignarPrestador->Fields('RUT_PRESTADOR') ?>"><?php echo utf8_encode($qrAsignarPrestador->Fields('DESC_PRESTADOR')) ?></option>
							        <?php $qrAsignarPrestador->MoveNext(); } ?>
							    </select>
							</div>
							<span class="label label-default">Comentario bitácora<br></span>
		  				<textarea name="comentarioBitacoraAsignarPrestadorCaso" id="comentarioBitacoraAsignarPrestadorCaso" cols="11" rows="10" class="form-control input-sm"><?php if ($qrTipoPatologia->Fields('DESC_TIPO_PATOLOGIA') == 'GES') { ?>A la canasta [<?php echo utf8_encode($qrCanastaPatologia->Fields('DESC_CANASTA_PATOLOGIA')) ?>] de la derivación número <?php echo $qrDerivacion->Fields('N_DERIVACION'); ?> del paciente <?php echo utf8_encode($qrPaciente->Fields('NOMBRE')); ?> rut <?php echo $qrDerivacion->Fields('COD_RUTPAC'); ?> se le asigno el prestador: (seleccione el prestador).<?php }else{ ?>A la derivación número <?php echo $qrDerivacion->Fields('N_DERIVACION'); ?> del paciente <?php echo utf8_encode($qrPaciente->Fields('NOMBRE')); ?> rut <?php echo $qrDerivacion->Fields('COD_RUTPAC'); ?> se le asigno el prestador: (seleccione el prestador).
		  					<?php } ?>
		  					 </textarea>  
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-info" data-dismiss="modal" onclick="fnAsignarPrestadorCaso(
					    	'<?php echo $idDerivacion ?>',
					    	'<?php echo $totalRows_qrDerivacionCanasta ?>',
					    	'<?php echo $codTipoPatologia ?>',
					    	'<?php echo $qrDerivacion->Fields('COD_RUTPAC'); ?>',
					    	'<?php echo $qrDerivacion->Fields('ID_CONVENIO'); ?>',
					    	'<?php echo $qrDerivacion->Fields('CODIGO_PATOLOGIA'); ?>',
					    	'<?php echo $qrDerivacion->Fields('ENFERMERA'); ?>',
					    	'<?php echo $usuario; ?>',
					    	'<?php echo $qrDerivacion->Fields('FECHA_DERIVACION'); ?>',
					    	'<?php echo $qrDerivacion->Fields('CODIGO_CANASTA_PATOLOGIA'); ?>',
					    	'<?php echo $qrDerivacion->Fields('CODIGO_ETAPA_PATOLOGIA'); ?>',
					    	'<?php echo $qrDerivacionCanastaInicial->Fields('FECHA_CANASTA'); ?>',
					    	'<?php echo $qrDerivacionCanastaInicial->Fields('FECHA_LIMITE'); ?>',
					    	'<?php echo $qrDerivacionCanastaInicial->Fields('ID_CANASTA_PATOLOGIA'); ?>',
					    	'<?php echo $qrDerivacionCanastaInicial->Fields('ID_ETAPA_PATOLOGIA'); ?>',
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
					    	'<?php echo utf8_encode($qrPaciente->Fields('SEXO')) ?>')">Asignar Prestador</button> 
			  	</div>     
	  		</div>
	  		<!-- lleno este hidden desde la funcion fnConsultaSiTieneModuloPrestador, para capturarlo en la funcion  fnAsignarPrestadorCaso y evaluar si llama o no a la API que le manda la derivacion-->
	  		<input type="hidden" id="hdModuloPrestador">
	  		<input type="hidden" id="hdIdDerivacionAsignaPrestadorPp" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>

idDerivacion = $('#hdIdDerivacionAsignaPrestadorPp').val();	
$('#dvInfoVentanasOpcionesAsignaPrestador').load('vistas/modulos/infoVentanasOpcionesPp.php?idDerivacion='+idDerivacion);

function fnConsultaSiTieneModuloPrestador(){
	slAsignarPrestadorDerivacion = $('#slAsignarPrestadorDerivacion').val();
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

function fnAsignarPrestadorCaso(idDerivacion,canasta, codTipoPatologia,codRutPac,idConvenio,codPatologia,enfermera,usuario,fecha_derivacion,codCanastaPatologia,codEtapaPatologia,fechaCanastaInicial,fechaFinGarantia,idTablaCanastaPatologia,idTablaEtapaPatologia, nombrePaciente,nacimientoPaciente,fonoPaciente,direccionPaciente,regionPaciente,provinciaPaciente,comunaPaciente,mailPaciente,ocupacionPaciente,previsionPaciente,planSaludPaciente,seguroComplementarioPaciente,companiaSeguroPaciente,sexo){
	slAsignarPrestadorDerivacion = $('#slAsignarPrestadorDerivacion').val();
	comentarioBitacoraAsignarPrestadorCaso = $('#comentarioBitacoraAsignarPrestadorCaso').val(); 
	moduloPrestador = $('#hdModuloPrestador').val();

	if (slAsignarPrestadorDerivacion == '' || comentarioBitacoraAsignarPrestadorCaso == '') { 
		Swal.fire({
		  icon: 'error',
		  title: 'Oops...',
		  text: 'No se asigno el caso, complete los datos requeridos!',
		})
	}else{

	cadena = 'idDerivacion=' + idDerivacion +
			 '&slAsignarPrestadorDerivacion=' + slAsignarPrestadorDerivacion +
			 '&comentarioBitacoraAsignarPrestadorCaso=' + comentarioBitacoraAsignarPrestadorCaso +
			 '&canasta=' + canasta;

		
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/pacientesPp/asignarPrestadorCaso/asignarPrestadorCaso.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'La derivacion ha sido asignada al prestador con exito',
					  showConfirmButton: false,
					  timer: 1400
					})
					if (moduloPrestador == 'si' ) {
						//fnAsignarDerivacionAPrestadorPorApi(idDerivacion,codTipoPatologia,codRutPac,idConvenio,codPatologia,enfermera,usuario,fecha_derivacion,codCanastaPatologia,codEtapaPatologia,fechaCanastaInicial,fechaFinGarantia,idTablaCanastaPatologia,idTablaEtapaPatologia,nombrePaciente,nacimientoPaciente,fonoPaciente,direccionPaciente,regionPaciente,provinciaPaciente,comunaPaciente,mailPaciente,ocupacionPaciente,previsionPaciente,planSaludPaciente,seguroComplementarioPaciente,companiaSeguroPaciente,sexo);
					}
					
					setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1401);
	    	}
				
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
		// 		 '&slAsignarPrestadorDerivacion=' + slAsignarPrestadorDerivacion +
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

function fnCambiaComentarioBitacora(prestador,nderivacion,paciente,rutPaciente,canasta){
	nprestador = $('select[name="slAsignarPrestadorDerivacion"] option:selected').text();

	if (canasta == '') {
		$('#comentarioBitacoraAsignarPrestadorCaso').val('A la derivación número '+ nderivacion + ' del paciente '+paciente + ' rut '+rutPaciente + ' se le asigno el prestador: '+ nprestador  +'.');
	}else{
		$('#comentarioBitacoraAsignarPrestadorCaso').val('A la canasta  ['+ canasta +'] de la derivación número '+ nderivacion + ' del paciente '+paciente + ' rut '+rutPaciente + ' se le asigno el prestador: '+ nprestador  +'.');
	}
	
}
	
</script>



