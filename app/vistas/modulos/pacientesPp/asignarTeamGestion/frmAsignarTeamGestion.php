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

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idPaciente = $qrDerivacion->Fields('ID_PACIENTE');

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$idPaciente'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$codRutPac = $qrPaciente->Fields('COD_RUTPAC');

$codTipoPatologia = $qrDerivacion->Fields('CODIGO_TIPO_PATOLOGIA');

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia WHERE ID_TIPO_PATOLOGIA = '$codTipoPatologia'";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

$codPatologia = $qrDerivacion->Fields('ID_PATOLOGIA');

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia_pp WHERE ID_PATOLOGIA = '$codPatologia'";
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

$query_qrAsignarPrestador= "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO = '5'";
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

$query_qrProfesion= "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '4' OR ID = '6'"; 
$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
$totalRows_qrProfesion = $qrProfesion->RecordCount();


?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Asignar equipo de gestión:</h2></div>

						<div class="col-md-6" id="dvInfoVentanasOpcionesAsignarTeamPp"></div>

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
							      <button id="btnGuardaTeamGestion" type="button" class="btn btn-success btn-block" onclick="fnGuardaTeamProfesionalesPp('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>')">Asignar profesional</button>
							  </div>
							</div>
							
							<div id="dvTablaTeamGestion"></div>
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			  	</div>     
	  		</div>
	  		
	  		<input type="hidden" id="hdIdDerivacionTeamGestion" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
	$('#btnGuardaTeamGestion').hide();
	idDerivacion=$('#hdIdDerivacionTeamGestion').val();
	$("#dvTablaTeamGestion").load('vistas/modulos/pacientesPp/asignarTeamGestion/tablaTeamGestion.php?idDerivacion='+idDerivacion);
	$('#dvInfoVentanasOpcionesAsignarTeamPp').load('vistas/modulos/infoVentanasOpcionesPp.php?idDerivacion='+idDerivacion);

  function fnAsignaProfesional(){
    tipoEspecialidad = $("#slProfesionTeamGestion").val();
    cadena = 'tipoEspecialidad=' + tipoEspecialidad;
    $.ajax({
      type: "POST",
      url: "vistas/modulos/asignarTeamGestion/buscaTipoProfesional.php",
      data: cadena,
      success:function(r){
        $("#slProfesionalesTeamAtencion").html(r);
        $('#btnGuardaTeamGestion').show();
      }
    }); 

}

function fnGuardaTeamProfesionalesPp(idDerivacion, tipoUsuario){
  prof = $("#slProfesionalesTeamAtencion").val();
  nomProf = $('#slProfesionalesTeamAtencion option:selected').text();
  tipoEspecialidad = $("#slProfesionTeamGestion").val();

  cadena = 'prof=' + prof +
           '&idDerivacion=' + idDerivacion +
           '&nomProf=' + nomProf +
           '&tipoEspecialidad=' + tipoEspecialidad; 
  $.ajax({
    type: "POST",
    url: "vistas/modulos/pacientesPp/asignarTeamGestion/asignarTeamGestion.php",
    data: cadena,
    success:function(r){


    		$("#dvTablaTeamGestion").load('vistas/modulos/pacientesPp/asignarTeamGestion/tablaTeamGestion.php?idDerivacion='+idDerivacion);
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

	
</script>



