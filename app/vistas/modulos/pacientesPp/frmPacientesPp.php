<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');

$fechaLimite = date("Y-m-d",strtotime($hoy."+ 10 days"));

$query_qrPorVencer = "SELECT *
FROM $MM_oirs_DATABASE.derivaciones_pp AS d
INNER JOIN $MM_oirs_DATABASE.derivaciones_canastas_pp AS dc ON d.ID_DERIVACION = dc.ID_DERIVACION
WHERE dc.FECHA_LIMITE <= '$fechaLimite'
  AND dc.FECHA_LIMITE != '0000-00-00'
  AND dc.DIAS_LIMITE != '0'
  AND dc.FECHA_LIMITE >= '$hoy'
  AND d.ESTADO != 'cerrada'
  AND d.CODIGO_TIPO_PATOLOGIA = '1'
  AND dc.ESTADO != 'finalizada'
";
$qrPorVencer = $oirs->SelectLimit($query_qrPorVencer) or die($oirs->ErrorMsg());
$totalRows_qrPorVencer = $qrPorVencer->RecordCount();

$query_qrVencecidas = "SELECT *
FROM $MM_oirs_DATABASE.derivaciones_pp AS d
INNER JOIN $MM_oirs_DATABASE.derivaciones_canastas_pp AS dc ON d.ID_DERIVACION = dc.ID_DERIVACION
WHERE dc.FECHA_LIMITE < '$hoy'
  AND dc.FECHA_LIMITE != '0000-00-00'
  AND dc.DIAS_LIMITE != '0'
  AND d.ESTADO != 'cerrada'
  AND d.CODIGO_TIPO_PATOLOGIA = '1'
  AND dc.ESTADO != 'finalizada'
";
$qrVencecidas = $oirs->SelectLimit($query_qrVencecidas) or die($oirs->ErrorMsg());
$totalRows_qrVencecidas = $qrVencecidas->RecordCount();

$query_qrDerivacionesPp = "SELECT *
FROM $MM_oirs_DATABASE.derivaciones_pp 
WHERE
  derivaciones_pp.ESTADO != 'cerrada' AND 
  derivaciones_pp.ORIGEN = 'isapre'
";
$qrDerivacionesPp = $oirs->SelectLimit($query_qrDerivacionesPp) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionesPp = $qrDerivacionesPp->RecordCount();

$query_qrDerivacionesPpIcrs = "SELECT *
FROM $MM_oirs_DATABASE.derivaciones_pp 
WHERE
  derivaciones_pp.ESTADO != 'cerrada' AND 
  derivaciones_pp.ORIGEN is null
";
$qrDerivacionesPpIcrs = $oirs->SelectLimit($query_qrDerivacionesPpIcrs) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionesPpIcrs = $qrDerivacionesPpIcrs->RecordCount();

?>


<html>
    <div class="card-body">
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
               <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('isapre')"><h3><?php echo $totalRows_qrDerivacionesPp ?></h3> Derivaciones Isapres <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
               <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('icrs')"><h3><?php echo $totalRows_qrDerivacionesPpIcrs ?></h3> Derivaciones Inst Cáncer <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('<?php echo $fechaLimite ?>')"><h3><?php echo $totalRows_qrPorVencer ?></h3> Canastas por vencer <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>



          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('<?php echo $hoy ?>','vencidas')"><h3><?php echo $totalRows_qrVencecidas ?></h3> Canastas Vencidas <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

      
        </div>
    </div>
   	<div class="col-md-12" id="dvTablaPacientesPp"></div>
</html>

<script>
    $('#dvTablaPacientesPp').html('<img src="images/loading.gif"/>');
	$('#dvTablaPacientesPp').load('vistas/modulos/pacientesPp/tblPacientesPp.php');

function fnFiltraTablaDerivaciones(estado,vencidas){
    $('#dvTablaPacientesPp').html('<img src="images/loading.gif"/>');
    $('#dvTablaPacientesPp').load('vistas/modulos/pacientesPp/tblPacientesPp.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnfrmDetalleDerivacion(idDerivacion){
	$('#dvfrmDetalleDerivacion').load('vistas/modulos/pacientesPp/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
}

function fnAceptarCasoPp(idDerivacion){
    $('#dvAceptarCasoPp').load('vistas/modulos/pacientesPp/aceptarCaso/frmAceptarCasoPp.php?idDerivacion=' + idDerivacion);
}
function fnfrmReasignarCasoPp(idDerivacion){
    $('#dvfrmReasignarCasoPp').load('vistas/modulos/pacientesPp/reasignarCaso/frmReasignarCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmAsignarCasoPp(idDerivacion){
    $('#dvfrmAsignarCasoPp').load('vistas/modulos/pacientesPp/asignarCaso/frmAsignarCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmBitacoraPp(idDerivacion){
    $('#dvfrmBitacoraPp').load('vistas/modulos/pacientesPp/bitacora/modals/frmBitacora.php?idDerivacion=' + idDerivacion);
}
// function fnfrmBitacoraCerradasPp(idDerivacion){
//     $('#dvfrmBitacoraCerradasPp').load('vistas/modulos/pacientesPp/bitacora/modals/frmBitacoraCerradas.php?idDerivacion=' + idDerivacion);
// }


function fnfrmCerrarCasoPp(idDerivacion){
    $('#dvfrmCerrarCasoPp').load('vistas/modulos/pacientesPp/cerrarCaso/frmCerrarCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmAsignarMedicotratante(idDerivacion){
    $('#dvfrmAsignarMedicoTratante').load('vistas/modulos/pacientesPp/asignarMedicoCaso/frmAsignarMedicoCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmAsignarTeam(idDerivacion){
    $('#dvfrmAsignarTeamGestionPp').load('vistas/modulos/pacientesPp/asignarTeamGestion/frmAsignarTeamGestion.php?idDerivacion=' + idDerivacion);
}

function fnfrmAsignarPatologia(idDerivacion){
    $('#dvfrmAsignarPatologiaEtapaCanastaPp').load('vistas/modulos/pacientesPp/asignarPatologiaEtapaCanasta/frmAsignarPatologiaEtapaCanasta.php?idDerivacion=' + idDerivacion);
}

function fnfrmContactarPaciente(idDerivacion){
    $('#dvfrmContactarPacientesPp').load('vistas/modulos/pacientesPp/contactarPaciente/frmContactarPaciente.php?idDerivacion=' + idDerivacion);
}

function fnAsignarCitaPp(idDerivacion){
    $('#dvfrmAsignarCitaPp').load('vistas/modulos/pacientesPp/asignarCita/frmAsignarCita.php?idDerivacion=' + idDerivacion);
}

function fnAtenderPacientePp(idDerivacion){
    $('#dvfrmAtenderPacientePp').load('vistas/modulos/pacientesPp/atenderPaciente/frmAtenderPaciente.php?idDerivacion=' + idDerivacion);
}

</script>



