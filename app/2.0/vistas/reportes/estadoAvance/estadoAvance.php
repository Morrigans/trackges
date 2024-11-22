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

$tipo=$verProfesion->Fields('TIPO');

date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');

$fechaLimite = date("Y-m-d",strtotime($hoy."+ 10 days"));

$query_qrDerivaciones = "
    SELECT 
  a.N_DERIVACION

  FROM 2_derivaciones a

";
$qrDerivaciones = $oirs->SelectLimit($query_qrDerivaciones) or die($oirs->ErrorMsg());
$totalRows_qrDerivaciones = $qrDerivaciones->RecordCount();

if ($tipo == '6') {
  $query_qrMisCasos = "
    SELECT 
  a.N_DERIVACION

  FROM 2_derivaciones a

  WHERE 
  a.TENS = '$usuario'

";
$qrMisCasos = $oirs->SelectLimit($query_qrMisCasos) or die($oirs->ErrorMsg());
$totalRows_qrMisCasos = $qrMisCasos->RecordCount();
}
if ($tipo == '3') {
  $query_qrMisCasos = "
    SELECT 
  a.N_DERIVACION

  FROM 2_derivaciones a

  WHERE 
  a.ENFERMERA = '$usuario'

";
$qrMisCasos = $oirs->SelectLimit($query_qrMisCasos) or die($oirs->ErrorMsg());
$totalRows_qrMisCasos = $qrMisCasos->RecordCount();
}
if ($tipo == '4') {
  $query_qrMisCasos = "
    SELECT 
  a.N_DERIVACION

  FROM 2_derivaciones a

  WHERE 
  a.ADMINISTRATIVA = '$usuario'

";
$qrMisCasos = $oirs->SelectLimit($query_qrMisCasos) or die($oirs->ErrorMsg());
$totalRows_qrMisCasos = $qrMisCasos->RecordCount();
}



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
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('')"><h3><?php echo $totalRows_qrDerivaciones ?></h3> Todas <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>
                  <?php if ($tipo == 3 or $tipo == 4 or $tipo == 6) { ?>
                    <div class="col-lg-3 col-6">
                      <div class="small-box bg-success">
                        <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivacionesMisCasos('')"><h3><?php echo $totalRows_qrMisCasos ?></h3> Mis Casos <i class="fas fa-arrow-circle-right"></i></a>
                      </div>
                  </div>
                  <?php } ?>
                  


                <div class="col-md-12" id="dvTablaEstadoAvance">

                    </div>
                    </div>
            </div>
      </div>
    </body>
</html>

<script>
$('#dvTablaEstadoAvance').html('<img src="images/loading.gif"/>');
$('#dvTablaEstadoAvance').load('2.0/vistas/reportes/estadoAvance/tablaEstadoAvance.php');

function fnFiltraTablaDerivaciones(estado,vencidas){
    $('#dvTablaEstadoAvance').html('<img src="images/loading.gif"/>');
    $('#dvTablaEstadoAvance').load('2.0/vistas/reportes/estadoAvance/tablaEstadoAvance.php?estado=' + estado+'&vencidas='+vencidas);

}

function fnFiltraTablaDerivacionesMisCasos(){
    $('#dvTablaEstadoAvance').html('<img src="images/loading.gif"/>');
    $('#dvTablaEstadoAvance').load('2.0/vistas/reportes/estadoAvance/tablaEstadoAvanceMisCasos.php');

}

function fnfrmEstadoAvance(idDerivacion){
    $('#dvfrmEstadoAvance').load('2.0/vistas/modulos/estadoAvance/frmEstadoAvance.php?idDerivacion=' + idDerivacion);
}

function fnfrmEstadoAvanceMisCasos(idDerivacion){
    misCasos='mis_casos';
    $('#dvfrmEstadoAvance').load('2.0/vistas/modulos/estadoAvance/frmEstadoAvance.php?idDerivacion=' + idDerivacion+'&misCasos='+misCasos);
}




</script>