<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

$usuario = $_SESSION['dni'];
$idBitacora = $_REQUEST['idBitacora'];
$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrLogin= "SELECT * FROM $MM_oirs_DATABASE.login WHERE (TIPO = '2' OR TIPO = '3' OR TIPO = '4' OR TIPO = '6')";
$qrLogin = $oirs->SelectLimit($query_qrLogin) or die($oirs->ErrorMsg());
$totalRows_qrLogin = $qrLogin->RecordCount();

$query_qrDerivacion= "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE ID_BITACORA = '$idBitacora'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$mensaje = $qrDerivacion->Fields('BITACORA');

$hoy = date('Y-m-d');
$minimo = date("Y-m-d",strtotime($hoy."+ 0 days")); 
?>
<style type="text/css">
	.anyClass {
	  height:720px;
	  overflow-y: scroll;
	}
</style>
<!DOCTYPE html>
<html>
	<body>
        	<div class="row">
        		<div class="input-group mb-3 col-sm-12">
        			<em>Mensaje a alertar:<br>
		            "<?php echo $mensaje; ?>"</em><br><br>
		        </div>
				<div class="input-group mb-3 col-sm-6">
				  <div class="input-group-prepend">
				    <span class="input-group-text">Alertar dia</span>
				  </div>
				  <input type='date' min="<?php echo $minimo ?>" class="form-control input-sm" name="fechaRecordatorio" id="fechaRecordatorio" onkeydown="return false"/>
				</div>

				<div class="input-group mb-3 col-sm-6">
				  <div class="input-group-prepend">
				    <span class="input-group-text">Destinatario</span>
				  </div>
				  <select id="usuarioReceptor" class="form-control input-sm" onchange="fnProgramarTarea('<?php echo $idBitacora ?>','<?php echo $usuario ?>','<?php echo $idDerivacion ?>')">
				  		<option value="">Seleccione...</option>
				  		<?php while (!$qrLogin->EOF) {?>
				  		  <option value="<?php echo $qrLogin->Fields('USUARIO') ?>"><?php echo $qrLogin->Fields('NOMBRE') ?></option>
				  		<?php $qrLogin->MoveNext(); } ?>
				  </select>
				</div>

				<div class="col-sm-12">
				  <div id="tablaDestinatariosAlarmas"></div>
				</div>

	       		<div class="modal-footer" align="right">	
<!-- 				    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
 -->				    <button type="button" class="btn btn-success btn-md"  data-dismiss="modal" onclick="fnCerrarProgramarTarea('<?php echo $idDerivacion ?>')">Finalizar y salir</button>
		  		</div>
	  		</div>
	  		<input type="hidden" id="idBitacora" value="<?php echo $idBitacora ?>">
	</body>
</html>
<script type="text/javascript">
	idBitacora=$('#idBitacora').val();
	$("#tablaDestinatariosAlarmas").load('vistas/bitacora/modals/tablaDestinatariosAlarmas.php?idBitacora='+idBitacora);

	function fnProgramarTarea(idBitacora,usuario,idDerivacion){
		fechaRecordatorio = $("#fechaRecordatorio").val();
		usuarioReceptor = $("#usuarioReceptor").val();

		if (fechaRecordatorio == '') {
			// Swal.fire({
			//   position: 'top-end',
			//   icon: 'warning',
			//   title: 'Ingresa los datos solicitados',
			//   showConfirmButton: false,
			//   timer: 2000
			// })
			$("#usuarioReceptor").val('');
		}else{

		cadena = 'fechaRecordatorio=' + fechaRecordatorio +
			 '&idBitacora=' + idBitacora +
			 '&usuarioReceptor=' + usuarioReceptor;

		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/bitacora/modals/programarTarea.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'Se programo correctamente',
					  showConfirmButton: false,
					  timer: 1500
					})
					$("#tablaDestinatariosAlarmas").load('vistas/bitacora/modals/tablaDestinatariosAlarmas.php?idBitacora='+idBitacora);
					//setTimeout(function (){ $('#dvTablaBitacora').load('vistas/bitacora/modals/tablaBitacora.php?idDerivacion='+idDerivacion); }, 1501);//retardo PARA EVITAR dropdown
				
	    	}
				
			}
		});
	}
	}

function fnCerrarProgramarTarea(idDerivacion){
	setTimeout(function (){ $('#dvTablaBitacora').load('vistas/bitacora/modals/tablaBitacora.php?idDerivacion='+idDerivacion); }, 1);//retardo PARA EVITAR dropdown
}
	
</script>





