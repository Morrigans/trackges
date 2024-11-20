<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$idUsuario = $_SESSION['idUsuario'];
$tipoUsuario = $_SESSION['tipoUsuario'];

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idPaciente = $qrDerivacion->Fields('ID_PACIENTE');
$marca = $qrDerivacion->Fields('MARCA');

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$idPaciente'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$codRutPac = $qrPaciente->Fields('COD_RUTPAC');

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Para cierre</h2></div>
						<div class="col-md-6" id="dvInfoVentanasOpciones"></div>	
						<div class="col-md-6">
						<div class="input-group mb-3 col-sm-12">
						    <div class="input-group-prepend">
						      <span class="input-group-text">Agregar marca</span>
						    </div>
						    <select name="slAgregarMarcaDerivacion" id="slAgregarMarcaDerivacion" class="form-control input-sm">
						    	<?php if ($marca == '') { ?>
						    		<option value="para_cierre">Marcar para cierre</option>
						    	<?php }else{ ?>
							        <option value="0">Quitar marca de cierre</option>
						    	<?php } ?>
						    </select>
						</div>
		  				
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-success" data-dismiss="modal" onclick="fnAgregarMarcaParaCierre('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>')">Actualizar</button>
			  	</div>     
	  		</div>
		<input type="hidden" id="idDerivacion" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
idDerivacion = $('#idDerivacion').val();	
$('#dvInfoVentanasOpciones').load('2.0/vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

function fnAgregarMarcaParaCierre(idDerivacion, tipoUsuario){
	slAgregarMarcaDerivacion = $('#slAgregarMarcaDerivacion').val();


	cadena = 'idDerivacion=' + idDerivacion +
					 '&slAgregarMarcaDerivacion=' + slAgregarMarcaDerivacion;
		$.ajax({
			type:"post",
			data:cadena,
			url:'2.0/vistas/modulos/agregarMarca/agregarMarca.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'La marca ha sido agregada con exito',
					  showConfirmButton: false,
					  timer: 800
					})
					if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
						setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
					}
					if (tipoUsuario == 4) {// administrativa
						setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioAdministrativa/inicioSAdministrativa.php'); }, 1);
					}
					// if (tipoUsuario == 5) {//medico
					// 	setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
					// }
					if (tipoUsuario == 6) {//tens
						setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioTens/inicioTens.php'); }, 1);
					}
	    		}else{

	    			Swal.fire({
	    			  position: 'top-end',
	    			  icon: 'success',
	    			  title: 'La marca se ha quitado con exito',
	    			  showConfirmButton: false,
	    			  timer: 800
	    			})
	    			if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
	    				setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
	    			}
	    			if (tipoUsuario == 4) {// administrativa
	    				setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioAdministrativa/inicioSAdministrativa.php'); }, 1);
	    			}
	    			// if (tipoUsuario == 5) {//medico
	    			// 	setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
	    			// }
	    			if (tipoUsuario == 6) {//tens
	    				setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioTens/inicioTens.php'); }, 1);
	    			}

	    		}
				
			}
		});
	}
	
</script>



