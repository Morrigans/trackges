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

date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');
$fechaLimite = date("Y-m-d",strtotime($hoy."+ 10 days"));

// $query_qrVencecidas = "SELECT * FROM $MM_oirs_DATABASE.derivaciones where FECHA_LIMITE<'$hoy' and FECHA_LIMITE!='0000-00-00'  AND ESTADO != 'cerrada' AND CODIGO_TIPO_PATOLOGIA = '1' AND ENFERMERA = '$usuario'";
// $qrVencecidas = $oirs->SelectLimit($query_qrVencecidas) or die($oirs->ErrorMsg());
// $totalRows_qrVencecidas = $qrVencecidas->RecordCount();

$query_qrVencecidas = "SELECT * 
FROM $MM_oirs_DATABASE.derivaciones, $MM_oirs_DATABASE.derivaciones_canastas
where
$MM_oirs_DATABASE.derivaciones.ID_DERIVACION =  $MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION and
$MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE<'$hoy' and 
$MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE!='0000-00-00' and 
$MM_oirs_DATABASE.derivaciones_canastas.DIAS_LIMITE!='0' and
$MM_oirs_DATABASE.derivaciones.ESTADO !='cerrada' AND 
$MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA = '1' and
$MM_oirs_DATABASE.derivaciones_canastas.ESTADO != 'finalizada' AND 
ENFERMERA = '$usuario'";
$qrVencecidas = $oirs->SelectLimit($query_qrVencecidas) or die($oirs->ErrorMsg());
$totalRows_qrVencecidas = $qrVencecidas->RecordCount();

// $query_qrPorVencer = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE FECHA_LIMITE>='$hoy' and FECHA_LIMITE!='0000-00-00' and FECHA_LIMITE<='$fechaLimite' AND ESTADO != 'cerrada' AND CODIGO_TIPO_PATOLOGIA = '1' AND ENFERMERA = '$usuario'";
// $qrPorVencer = $oirs->SelectLimit($query_qrPorVencer) or die($oirs->ErrorMsg());
// $totalRows_qrPorVencer = $qrPorVencer->RecordCount();

$query_qrPorVencer = "SELECT * 
FROM $MM_oirs_DATABASE.derivaciones, $MM_oirs_DATABASE.derivaciones_canastas
where
$MM_oirs_DATABASE.derivaciones.ID_DERIVACION =  $MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION and
$MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE<='$fechaLimite' and 
$MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE!='0000-00-00' and 
$MM_oirs_DATABASE.derivaciones_canastas.DIAS_LIMITE!='0' and
$MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE>='$hoy' and 
$MM_oirs_DATABASE.derivaciones.ESTADO !='cerrada' AND 
$MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA = '1' and
$MM_oirs_DATABASE.derivaciones_canastas.ESTADO != 'finalizada' AND 
ENFERMERA = '$usuario'";
$qrPorVencer = $oirs->SelectLimit($query_qrPorVencer) or die($oirs->ErrorMsg());
$totalRows_qrPorVencer = $qrPorVencer->RecordCount();

$query_qrCumplidas = "SELECT *
FROM $MM_oirs_DATABASE.derivaciones, $MM_oirs_DATABASE.derivaciones_canastas
where
$MM_oirs_DATABASE.derivaciones.ID_DERIVACION =  $MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION and
$MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA = '1' and
$MM_oirs_DATABASE.derivaciones_canastas.ESTADO = 'finalizada' and
$MM_oirs_DATABASE.derivaciones.ENFERMERA = '$usuario'";
$qrCumplidas = $oirs->SelectLimit($query_qrCumplidas) or die($oirs->ErrorMsg());
$totalRows_qrCumplidas = $qrCumplidas->RecordCount();

$query_qrDerivaciones = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ESTADO != 'cerrada' AND ENFERMERA = '$usuario'";
$qrDerivaciones = $oirs->SelectLimit($query_qrDerivaciones) or die($oirs->ErrorMsg());
$totalRows_qrDerivaciones = $qrDerivaciones->RecordCount();

$query_qrDerivadas = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ESTADO = 'pendiente' AND ENFERMERA = '$usuario'";
$qrDerivadas = $oirs->SelectLimit($query_qrDerivadas) or die($oirs->ErrorMsg());
$totalRows_qrDerivadas = $qrDerivadas->RecordCount();

$query_qrAceptadas = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ESTADO = 'aceptada' AND ENFERMERA = '$usuario'";
$qrAceptadas = $oirs->SelectLimit($query_qrAceptadas) or die($oirs->ErrorMsg());
$totalRows_qrAceptadas = $qrAceptadas->RecordCount();

$query_qrAsignadas = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ESTADO = 'prestador' AND ENFERMERA = '$usuario'";
$qrAsignadas = $oirs->SelectLimit($query_qrAsignadas) or die($oirs->ErrorMsg());
$totalRows_qrAsignadas = $qrAsignadas->RecordCount();

$query_qrCerradas = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ESTADO = 'cerrada' AND ENFERMERA = '$usuario'";
$qrCerradas = $oirs->SelectLimit($query_qrCerradas) or die($oirs->ErrorMsg());
$totalRows_qrCerradas = $qrCerradas->RecordCount();

?>

<!DOCTYPE html>
<html>
    <body>
      <div class="card card-info">
        <div class="card-header">
          <h3 class="card-title">Derivaciones</h3>
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

                 <div class="col-lg-2 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <h3><?php echo $totalRows_qrPorVencer?></h3>
                        <p>Canastas por vencer</p>
                      </div>
                      <div align="center" class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                      </div>
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('<?php echo $fechaLimite ?>')">Filtrar información <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-lg-2 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <h3><?php echo $totalRows_qrVencecidas?></h3>
                        <p>Canastas vencidas</p>
                      </div>
                      <div align="center" class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                      </div>
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('<?php echo $hoy ?>','vencidas')">Filtrar información <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-lg-2 col-6">
                    <div class="small-box bg-primary">
                      <div class="inner">
                        <h3><?php echo $totalRows_qrDerivadas ?></h3>
                        <p>Pendientes</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-user-clock"></i>
                      </div>
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('pendiente')">Filtrar información <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div> 
                  

                  <div class="col-lg-2 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <h3><?php echo $totalRows_qrAceptadas ?></h3>
                        <p>Aceptadas</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-user-check"></i>
                      </div>
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('aceptada')">Filtrar información <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-lg-2 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <h3><?php echo $totalRows_qrCumplidas ?></h3>
                        <p>Canastas cumplidas</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                      </div>
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivacionesCumplidasGestora()">Filtrar información <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-lg-2 col-6">
                    <div class="small-box bg-white">
                      <div class="inner">
                        <h3><?php echo $totalRows_qrDerivaciones ?></h3>
                        <p>Activas</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-bars"></i>
                      </div>
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('')">Mostrar todo <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-lg-2 col-6">
                    <div class="small-box bg-white">
                      <div class="inner">
                        <h3><?php echo $totalRows_qrDerivaciones ?></h3>
                        <p>Oncologicos ICRS</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-bars"></i>
                      </div>
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaOncologicosIcrs('')">Filtrar información <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>


                <div class="col-md-12" id="dvTablaDerivacionesGestora">  

                    </div>
                    </div>
            </div>
      </div>
    </body>
</html>

<script>
$('#dvTablaDerivacionesGestora').html('<img src="images/loading.gif"/>');
$('#dvTablaDerivacionesGestora').load('vistas/inicio/inicioGestora/tablaDerivaciones.php');

function fnFiltraTablaOncologicosIcrs(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/modulos/oncologicsIcrs/tblPacientesOncoParaICRS.php');
}

function fnFiltraTablaDerivaciones(estado,vencidas){
    $('#dvTablaDerivacionesGestora').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivacionesGestora').load('vistas/inicio/inicioGestora/tablaDerivaciones.php?estado=' + estado + '&vencidas=' + vencidas);
}

function fnfrmAceptarCasoGestora(idDerivacion){
    $('#dvfrmAceptarCasoGestora').load('vistas/inicio/inicioGestora/modals/aceptarCaso/frmAceptarCasoGestora.php?idDerivacion=' + idDerivacion);
}

function fnfrmCerrarCasoGestora(idDerivacion){
    $('#dvfrmCerrarCasoGestora').load('vistas/inicio/inicioGestora/modals/cerrarCaso/frmCerrarCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmReasignarCasoGestora(idDerivacion){
    $('#dvfrmReasignarCasoGestora').load('vistas/inicio/inicioGestora/modals/reasignarCaso/frmReasignarCaso.php?idDerivacion=' + idDerivacion);
}

// function fnfrmAsignarPrestadorCasoGestora(idDerivacion){
//     $('#dvfrmAsignarPrestadorCasoGestora').load('vistas/inicio/inicioGestora/modals/asignarPrestadorCaso/frmAsignarPrestadorCaso.php?idDerivacion=' + idDerivacion);
// }

function fnfrmBitacora(idDerivacion){
    $('#dvfrmBitacora').load('vistas/bitacora/modals/frmBitacora.php?idDerivacion=' + idDerivacion);
}

function fnfrmDetalleDerivacion(idDerivacion){
    $('#dvfrmDetalleDerivacionGestora').load('vistas/inicio/inicioGestora/modals/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
}

function fnFiltraTablaDerivacionesCerradas1(){
  $('#dvTablaDerivacionesGestora').load('vistas/inicio/inicioGestora/tablaDerivacionesCerradas.php');
}

function fnFiltraTablaDerivacionesCumplidasGestora(){
  $('#dvTablaDerivacionesGestora').html('<img src="images/loading.gif"/>');
  $('#dvTablaDerivacionesGestora').load('vistas/inicio/inicioGestora/tablaDerivacionesCumplidas.php');
}

function fnFrmEditaInformacionPacienteGestora(idDerivacion){
    $('#dvFrmEditaInformacionPacienteGestora').load('vistas/inicio/inicioGestora/modals/informacionPaciente/frmEditaInformacionPacienteGestora.php?idDerivacion=' + idDerivacion);
}

    
</script>