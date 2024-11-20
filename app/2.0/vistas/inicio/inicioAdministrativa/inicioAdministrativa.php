<?php
require_once '../../../../Connections/oirs.php';
require_once '../../../../includes/functions.inc.php';
 
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];
$idUsuario = $_SESSION['idUsuario'];

$query_verProfesion = "SELECT * FROM $MM_oirs_DATABASE.login where ID='$idUsuario'";
$verProfesion = $oirs->SelectLimit($query_verProfesion) or die($oirs->ErrorMsg());
$totalRows_verProfesion = $verProfesion->RecordCount();

$idClinica=$verProfesion->Fields('ID_PRESTADOR');

date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');

$fechaLimite = date("Y-m-d",strtotime($hoy."+ 10 days"));

$query_qrDerivacionesActivas = "
    SELECT 
  a.N_DERIVACION

  FROM 2_derivaciones a

  WHERE ESTADO_RN != 'Anulado'

";
$qrDerivacionesActivas = $oirs->SelectLimit($query_qrDerivacionesActivas) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionesActivas = $qrDerivacionesActivas->RecordCount();

$query_qrDerivacionesActivasMenosAltas = "
    SELECT 
  a.N_DERIVACION

  FROM 2_derivaciones a

  WHERE ESTADO_RN != 'Anulado' AND
  ESTADO_RN != 'Alta Paciente'

";
$qrDerivacionesActivasMenosAltas = $oirs->SelectLimit($query_qrDerivacionesActivasMenosAltas) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionesActivasMenosAltas = $qrDerivacionesActivasMenosAltas->RecordCount();

// ************************************************
$query_qrCensoDiario = "
    SELECT DISTINCT
    a.ID_DERIVACION,
    a.FOLIO,
    a.N_DERIVACION,
    a.FECHA_DERIVACION,
    a.COD_RUTPAC,
    a.INTERVENCION_SANITARIA,
    a.ESTADO_RN,
    REPLACE(a.COD_RUTPAC, '.', '') AS COD_RUTPAC_SIN_PUNTOS,
    b.NOMBRE AS NOMBRE_PACIENTE,
    a.FOLIO,
    a.N_DERIVACION,
    a.FECHA_DERIVACION,
    med.NOMBRE AS MEDICO,
    adm.NOMBRE AS ADMINISTRATIVA,
  c.NOMBRE AS NOMBRE_PROFESIONAL,
  2_api_censo.fecha_censo,
  2_api_censo.id_admision,
  2_api_censo.fecha_ingreso,
  2_api_censo.dias_ingresado,
  2_api_censo.codigo_prestacion,
  2_api_censo.diagnostico,
  2_api_censo.nombre_convenio,
  2_api_censo.ley_urgencia,
  2_api_censo.fecha_foto,
  2_api_censo.ESTADO AS ESTADO_CENSO
    
  FROM 2_api_censo 

  LEFT JOIN 2_derivaciones a
  ON 2_api_censo.ID_DERIVACION = a.ID_DERIVACION

  LEFT JOIN 2_derivaciones_hijos di 
  ON a.ID_DERIVACION = di.ID_DERIVACION

  LEFT JOIN pacientes b 
  ON a.COD_RUTPAC = b.COD_RUTPAC

  LEFT JOIN login g 
  ON a.TENS = g.USUARIO

  LEFT JOIN login c 
  ON a.ENFERMERA = c.USUARIO

  LEFT JOIN login med 
  ON a.MEDICO = med.USUARIO

  LEFT JOIN login adm 
  ON a.ADMINISTRATIVA = adm.USUARIO
  
  WHERE
  a.ESTADO_RN != 'anulado' AND
  2_api_censo.fecha_registro = '$hoy'
";
$qrCensoDiario = $oirs->SelectLimit($query_qrCensoDiario) or die($oirs->ErrorMsg());
$totalRows_qrCensoDiario = $qrCensoDiario->RecordCount();
?>

<style>
  .color-palette {
    height: 35px;
    line-height: 35px;
    text-align: right;
    padding-right: .75rem;
  }

  .color-palette.disabled {
    text-align: center;
    padding-right: 0;
    display: block;
  }

  .color-palette-set {
    margin-bottom: 15px;
  }

  .color-palette span {
    display: none;
    font-size: 12px;
  }

  .color-palette:hover span {
    display: block;
  }

  .color-palette.disabled span {
    display: block;
    text-align: left;
    padding-left: .75rem;
  }

  .color-palette-box h4 {
    position: absolute;
    left: 1.25rem;
    margin-top: .75rem;
    color: rgba(255, 255, 255, 0.8);
    font-size: 12px;
    display: block;
    z-index: 7;
  }
</style>

<!DOCTYPE html>
<html>
    <body>
     <div class="card card-info">
        <div class="card-header">
          <h3 class="card-title">Derivaciones Nueva Licitacion</h3>
          <div class="card-tools">
            <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
            <div class="row">
                 
                  

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('')"><h3><?php echo $totalRows_qrDerivacionesActivas ?></h3> Todas <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('activas')"><h3><?php echo $totalRows_qrDerivacionesActivasMenosAltas ?></h3> Activas <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('sin_bitacora')"><h3>Sin gestión</h3> Sin gestión sobre 10 dias <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>
                  <div class="col-lg-3 col-6">
                      <div class="small-box bg-white">
                        <a class="small-box-footer" style="pointer-events: none;"><h3>.</h3> En construcción... <i class="fas fa-arrow-circle-right"></i></a>
                      </div>
                  </div>
                  <div class="col-lg-3 col-6">
                      <div class="small-box bg-info">
                        <!-- <a href="#" class="small-box-footer"><h4> En construcción </h4><br><i class="fas fa-arrow-circle-right"></i></a> -->
                        <a href="#" class="small-box-footer" onclick="fnFiltraTablaApiCenso('censo')"><h3><?php echo $totalRows_qrCensoDiario ?></h3>Censo diario  <i class="fas fa-arrow-circle-right"></i></a>
                      </div>
                  </div>

                 
                   

                <div class="col-md-12" id="dvTablaDerivaciones">

                    </div>
                    </div>
            </div>
      </div>
    </body>
</html>

<script>
$('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
$('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioAdministrativa/tablaDerivaciones.php');

function fnFiltraTablaOncologicosIcrs(estado){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioSupervisora/tablaDerivacionesOncoICrs.php?estado=' + estado);
}

function fnFiltraTablaDerivaciones(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioAdministrativa/tablaDerivaciones.php?estado=' + estado+'&vencidas='+vencidas);

}
function fnFiltraTablaDerivacionesPrestadorAsignado(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioSupervisora/tablaDerivacionesPrestadorAsignado.php?estado=' + estado+'&vencidas='+vencidas);

}
function fnFiltraTablaDerivacionesAceptadas(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioSupervisora/tablaDerivacionesAceptadas.php?estado=' + estado+'&vencidas='+vencidas);

}

function fnFiltraTablaApiCenso(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioAdministrativa/tablaDerivacionesApiCenso.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaApiPabellon(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioSupervisora/tablaDerivacionesApiPabellon.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaApiPrgPabellones(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioSupervisora/tablaDerivacionesApiPrgPabellones.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaApiInterconsultas(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioSupervisora/tablaDerivacionesApiInterconsultas.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaApiUrgencia(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioSupervisora/tablaDerivacionesApiUrgencia.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaParaCierre(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioSupervisora/tablaDerivacionesParaCierre.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaRetrazado(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioSupervisora/tablaDerivacionesRetrazado.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaEnPlazo(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioSupervisora/tablaDerivacionesEnPlazo.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnfrmAceptarCaso(idDerivacion){
    $('#dvfrmAceptarCaso').load('2.0/vistas/modulos/aceptarCaso/frmAceptarCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmCerrarCaso(idDerivacion){
    $('#dvfrmCerrarCaso').load('2.0/vistas/modulos/cerrarCaso/frmCerrarCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmReasignarCaso(idDerivacion){
    $('#dvfrmReasignarCaso').load('2.0/vistas/modulos/reasignarCaso/frmReasignarCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmAsignarMedicoCaso(idDerivacion){
    $('#dvfrmAsignarMedicoCaso').load('2.0/vistas/modulos/asignarMedicoCaso/frmAsignarMedicoCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmBitacora(idDerivacion){
    $('#dvfrmBitacora').load('2.0/vistas/bitacora/modals/frmBitacora.php?idDerivacion=' + idDerivacion);
}

function fnfrmDetalleDerivacion(idDerivacion){
    $('#dvfrmDetalleDerivacion').load('2.0/vistas/modulos/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
}

function fnFiltraTablaDerivacionesCerradas(){
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioSupervisora/tablaDerivacionesCerradas.php');
}

function fnFiltraTablaDerivacionesCumplidas(){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('2.0/vistas/inicio/inicioSupervisora/tablaDerivacionesCumplidas.php');
}

function fnFrmEditaInformacionPacienteSupervisora(idDerivacion){
    $('#dvFrmEditaInformacionPacienteSupervisora').load('2.0/vistas/modulos/informacionPaciente/frmEditaInformacionPacienteSupervisora.php?idDerivacion=' + idDerivacion);
}

function fnfrmAsignarTeamGestion(idDerivacion){
    $('#dvfrmAsignarTeamGestion').load('2.0/vistas/modulos/asignarTeamGestion/frmAsignarTeamGestion.php?idDerivacion=' + idDerivacion);
}

function fnfrmContactarPaciente(idDerivacion){
    $('#dvfrmContactarPaciente').load('2.0/vistas/modulos/contactarPaciente/frmContactarPaciente.php?idDerivacion=' + idDerivacion);
}

function fnfrmAsignarCita(idDerivacion){
    $('#dvfrmAsignarCita').load('2.0/vistas/modulos/asignarCita/frmAsignarCita.php?idDerivacion=' + idDerivacion);
}

function fnfrmAtenderPaciente(idDerivacion){
    $('#dvfrmAtenderPaciente').load('2.0/vistas/modulos/atenderPaciente/frmAtenderPaciente.php?idDerivacion=' + idDerivacion);
}

function fnfrmAsignarPatologiaEtapaCanasta(idDerivacion){
    $('#dvfrmAsignarPatologiaEtapaCanasta').load('2.0/vistas/modulos/asignarPatologiaEtapaCanasta/frmAsignarPatologiaEtapaCanasta.php?idDerivacion=' + idDerivacion); 
}

function fnfrmAsignarCaso(idDerivacion){
    $('#dvfrmAsignarCaso').load('2.0/vistas/modulos/asignarCaso/frmAsignarCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmAgregarMarca(idDerivacion){
    $('#dvfrmAgregarMarca').load('2.0/vistas/modulos/agregarMarca/frmAgregarMarca.php?idDerivacion=' + idDerivacion);
}

function fnfrmValidarRechazar(idDerivacion){
    $('#dvfrmValidarRechazar').load('2.0/vistas/modulos/validarRechazarParaGesCenso/frmValidarRechazar.php?idDerivacion=' + idDerivacion);
}

function fnfrmValidarRechazarPab(idDerivacion){
    $('#dvfrmValidarRechazarPab').load('2.0/vistas/modulos/validarRechazarParaGesPab/frmValidarRechazarPab.php?idDerivacion=' + idDerivacion);
}

function fnfrmValidarRechazarUrg(idDerivacion, idUrg){
    $('#dvfrmValidarRechazarUrg').load('2.0/vistas/modulos/validarRechazarParaGesUrg/frmValidarRechazarUrg.php?idDerivacion=' + idDerivacion + '&idUrg=' + idUrg);
}

function fnfrmMotivoVencidaNoFinalizada(idDerivacion){
    $('#dvfrmMotivoVencidaNoFinalizada').load('2.0/vistas/modulos/motivoVencidaNoFinalizada/frmMotivoVencidaNoFinalizada.php?idDerivacion=' + idDerivacion);

}
function fnfrmMotRetPabPrg(idPrgPab){
    $('#dvfrmMotRetPabPrg').load('2.0/vistas/modulos/motRetPabPrg/frmMotRetPabPrg.php?idPrgPab=' + idPrgPab);  
}



</script>