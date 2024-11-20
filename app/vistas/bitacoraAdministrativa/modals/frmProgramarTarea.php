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

$query_qrLogin= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$usuario'";
$qrLogin = $oirs->SelectLimit($query_qrLogin) or die($oirs->ErrorMsg());
$totalRows_qrLogin = $qrLogin->RecordCount();

$hoy = date('Y-m-d');
$minimo = date("Y-m-d",strtotime($hoy."+ 1 days")); 
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
				  <div class="input-group-prepend">
				    <span class="input-group-text">Alertar dia</span>
				  </div>
				  <input type='date' min="<?php echo $minimo ?>" class="form-control input-sm" name="fechaRecordatorio" id="fechaRecordatorio" onkeydown="return false"/>
				</div>
	       		<div class="modal-footer" align="right">	
				    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
				    <button class="btn btn-success btn-md" data-dismiss="modal" onclick="fnProgramarTarea('<?php echo $idBitacora ?>','<?php echo $usuario ?>')">Programar</button>
		  		</div>
	  		</div>
	</body>
</html>
<script type="text/javascript">
	function fnProgramarTarea(idBitacora,usuario){
		fechaRecordatorio = $("#fechaRecordatorio").val();

		cadena = 'fechaRecordatorio=' + fechaRecordatorio +
			 '&idBitacora=' + idBitacora;

		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/bitacoraAdministrativa/modals/programarTarea.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'Se programo correctamente',
					  showConfirmButton: false,
					  timer: 1500
					})
					setTimeout(function (){ $('#dvTablaBitacoraAdministrativa').load('vistas/bitacoraAdministrativa/modals/tablaBitacora.php?usuario='+usuario); }, 1501);//retardo PARA EVITAR dropdown
				
	    	}
				
			}
		});
	}

	
</script>





