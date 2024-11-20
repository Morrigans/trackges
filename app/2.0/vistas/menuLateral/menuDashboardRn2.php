<?php 
  require_once '../../../Connections/oirs.php';
  require_once '../../../includes/functions.inc.php';
  // Solo se permite el ingreso con el inicio de sesion.
  session_start();
  // Si el usuario no se ha logueado se le regresa al inicio.
  if (!isset($_SESSION['loggedin'])) {
  header('Location: ../../../index.php');
  exit; }

  $usuario = $_SESSION['dni'];

  $query_verProfesion = "SELECT * FROM $MM_oirs_DATABASE.login where USUARIO='$usuario'";
  $verProfesion = $oirs->SelectLimit($query_verProfesion) or die($oirs->ErrorMsg());
  $totalRows_verProfesion = $verProfesion->RecordCount();

  $profesion=$verProfesion->Fields('TIPO');
  $nombrePro=$verProfesion->Fields('NOMBRE');

?>

<ul class="nav nav-pills nav-sidebar flex-column">
    <li class="nav-item menu-close">
      <a href="#" class="nav-link">
        <i class="nav-icon fas fa-wrench"></i>
        <p>
          Dashboard
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">


         <?php if($profesion==3){ ?>
         <!-- <div id="dvMenuDashboardFinanciero"></div> -->
         <!-- <div id="dvMenuDashboardComercial"></div> -->
         <!-- <div id="dvMenuDashboardOperacional"></div> -->
         
        <?php } ?>

        <?php if($profesion==1 or $profesion==2 or $profesion==7 or $profesion==3){ ?>
         <!-- <div id="dvMenuDashboardFinancieroRn2"></div> -->
         <div id="dvMenuDashboardComercialRn2"></div>
         <!-- <div id="dvMenuDashboardOperacionalRn2"></div> -->
         
        <?php } ?>

        </li>
      </ul>
    </li>
</ul>
           
<script>
  $('#dvMenuDashboardFinancieroRn2').load('2.0/vistas/menuLateral/menuDashboardFinancieroRn2.php');
  $('#dvMenuDashboardComercialRn2').load('2.0/vistas/menuLateral/menuDashboardComercialRn2.php');
  $('#dvMenuDashboardOperacionalRn2').load('2.0/vistas/menuLateral/menuDashboardOperacionalRn2.php');

</script>