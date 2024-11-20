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

$usuario = $_SESSION['dni'];
$idUsuario = $_SESSION['idUsuario'];
$tipoUsuario = $_SESSION['tipoUsuario'];

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$codRutPac = $qrDerivacion->Fields('COD_RUTPAC');

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$codRutPac'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$query_qrProfesion= "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '4' OR ID = '6'"; 
$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
$totalRows_qrProfesion = $qrProfesion->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('FOLIO'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Asignar equipo de gestión:</h2></div>

						<div class="col-md-6" id="dvInfoVentanasOpcionesAsignarTeam"></div>

						<div class="col-md-6">
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Tipo profesional</span>
							    </div>
							    <select name="slProfesionTeamGestion" id="slProfesionTeamGestion" class="form-control input-sm" onchange="fnAsignaProfesional(this.value)">
							        <option value="">Seleccione...</option>
							        <?php while (!$qrProfesion->EOF) {?>
							          <option value="<?php echo $qrProfesion->Fields('ID') ?>"><?php echo utf8_encode($qrProfesion->Fields('PROFESION')) ?></option>
							        <?php $qrProfesion->MoveNext(); } ?>
							    </select>
							</div>

							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Profesional</span>
							    </div>
							    <select name="slProfesionalesTeamAtencion" id="slProfesionalesTeamAtencion" class="form-control input-sm">
							    </select>
							</div>

							<div class="col-sm-12">
							  <div class="form-group">
							      <span class="label" style="color: black"><br></span>
							      <button id="btnGuardaTeamGestion" type="button" class="btn btn-success btn-block" onclick="fnGuardaTeamProfesionales('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>')">Asignar profesional</button>
							  </div>
							</div>
							
							<div id="dvTablaTeamGestion"></div>
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal" onclick="fnActualizaBandeja('<?php echo $tipoUsuario ?>')">Cerrar</button>
			  	</div>     
	  		</div>
	  		
	  		<input type="hidden" id="hdIdDerivacionTeamGestion" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
	$('#btnGuardaTeamGestion').hide();
	idDerivacion=$('#hdIdDerivacionTeamGestion').val();
	$("#dvTablaTeamGestion").load('2.0/vistas/modulos/asignarTeamGestion/tablaTeamGestion.php?idDerivacion='+idDerivacion);
	$('#dvInfoVentanasOpcionesAsignarTeam').load('2.0/vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

  function fnAsignaProfesional(){
    tipoEspecialidad = $("#slProfesionTeamGestion").val();
    cadena = 'tipoEspecialidad=' + tipoEspecialidad;
    $.ajax({
      type: "POST",
      url: "2.0/vistas/modulos/asignarTeamGestion/buscaTipoProfesional.php",
      data: cadena,
      success:function(r){
        $("#slProfesionalesTeamAtencion").html(r);
        $('#btnGuardaTeamGestion').show();
      }
    }); 

}

function fnGuardaTeamProfesionales(idDerivacion, tipoUsuario){
  prof = $("#slProfesionalesTeamAtencion").val();
  nomProf = $('#slProfesionalesTeamAtencion option:selected').text();
  tipoEspecialidad = $("#slProfesionTeamGestion").val();

  cadena = 'prof=' + prof +
           '&idDerivacion=' + idDerivacion +
           '&nomProf=' + nomProf +
           '&tipoEspecialidad=' + tipoEspecialidad; 
  $.ajax({
    type: "POST",
    url: "2.0/vistas/modulos/asignarTeamGestion/asignarTeamGestion.php",
    data: cadena,
    success:function(r){


    	$("#dvTablaTeamGestion").load('2.0/vistas/modulos/asignarTeamGestion/tablaTeamGestion.php?idDerivacion='+idDerivacion);	
          Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Profesional asociado con exito',
            showConfirmButton: false,
            timer: 800
          })
      }
    }); 
}

function fnActualizaBandeja(tipoUsuario){
		if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
				setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1000);
			}
			if (tipoUsuario == 4) {// administrativa
				setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioAdministrativa/inicioSAdministrativa.php'); }, 1000);
			}
			// if (tipoUsuario == 5) {//medico
			// 	setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
			// }
			if (tipoUsuario == 6) {//tens
				setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioTens/inicioTens.php'); }, 1000);
			}
}

	
</script>



