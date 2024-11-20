<?php 
  require_once '../../Connections/oirs.php';
  require_once '../../includes/functions.inc.php';
  // Solo se permite el ingreso con el inicio de sesion.
  session_start();
  // Si el usuario no se ha logueado se le regresa al inicio.
  if (!isset($_SESSION['loggedin'])) {
  header('Location: index.php');
  exit; }

  $usuario = $_SESSION['dni'];

  $query_verProfesion = "SELECT * FROM $MM_oirs_DATABASE.login where USUARIO='$usuario'";
  $verProfesion = $oirs->SelectLimit($query_verProfesion) or die($oirs->ErrorMsg());
  $totalRows_verProfesion = $verProfesion->RecordCount();

  $profesion=$verProfesion->Fields('TIPO');

?>
<ul class="nav nav-pills nav-sidebar flex-column">
    <li class="nav-item menu-close">
      <a href="#" class="nav-link">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>
          Reportes
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
         <div id="dvMenuReporteCasosCerrados"></div>
         <div id="dvMenuReporteCasosAnulados"></div>
         <div id="dvMenuReporteDiario"></div>
         <!-- <div id="dvMenuReporteCanastasVencidasFinalizadas"></div> -->
         <div id="dvMenuReporteBuscaDerivacionGestora"></div>
         <div id="dvMenuReporteEgresos"></div>
          <?php if($profesion==1){ ?>
          <div id="dvMenuReporteBitacora"></div>
         <?php } ?>
        </li>
      </ul>
    </li>
</ul>
 
            
<script>
  $('#dvMenuReporteCasosCerrados').load('vistas/menuLateral/menuReporteCasosCerrados.php');
  $('#dvMenuReporteCasosAnulados').load('vistas/menuLateral/menuReporteCasosAnulados.php');
  $('#dvMenuReporteDiario').load('vistas/menuLateral/menuReporteDiario.php');
  $('#dvMenuReporteBitacora').load('vistas/menuLateral/menuReporteBitacora.php');
  $('#dvMenuReporteCanastasVencidasFinalizadas').load('vistas/menuLateral/menuReporteCanastasVencidasFinalizadas.php');
  $('#dvMenuReporteBuscaDerivacionGestora').load('vistas/menuLateral/menuReporteBuscaDerivacionGestora.php');
  $('#dvMenuReporteDerivaciones').load('vistas/menuLateral/menuReporteDerivaciones.php');
  $('#dvMenuReporteEgresos').load('vistas/menuLateral/menuReporteEgresos.php');
</script>