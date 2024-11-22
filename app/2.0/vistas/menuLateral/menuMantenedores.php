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
          Mantenedores
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
         <!-- <div id="dvMenuCreaProfesion"></div> -->
         <div id="dvMenuCreaProfesional"></div>
         <div id="dvMenuCreaPaciente"></div>
         

         <?php if($profesion==1){ ?>
         <div id="dvMenuCreaPatologia"></div>
         <div id="dvMenuCreaEtapaPatologia"></div>
         <div id="dvMenuCanastasPatologias"></div>
         <div id="dvMenuCreaPrestadores"></div>
         <div id="dvMenuCreaPrestadores"></div>
         <div id="dvMenuConvenio"></div>
         <div id="dvMenuMotivoFinCanasta"></div>
         <div id="dvMenuContactosHospital"></div>
         <div id="dvMenuPaquete"></div>
         <div id="dvMenuPrestacion"></div>
         <div id="dvMenuProblemaSalud"></div>
        <?php } ?>

        </li>
      </ul>
    </li>
</ul>
           
<script>
  $('#dvMenuCreaPaciente').load('2.0/vistas/menuLateral/menuCreaPaciente.php');
  $('#dvMenuContactosHospital').load('2.0/vistas/menuLateral/menuContactosHospital.php');
  $('#dvMenuCreaProfesional').load('2.0/vistas/menuLateral/menuCreaProfesional.php');
  $('#dvMenuCreaPatologia').load('2.0/vistas/menuLateral/menuCreaPatologia.php');
  $('#dvMenuCreaEtapaPatologia').load('2.0/vistas/menuLateral/menuCreaEtapaPatologia.php');
  $('#dvMenuCanastasPatologias').load('2.0/vistas/menuLateral/menuCreaCanastaPatologia.php');
  $('#dvMenuMotivoFinCanasta').load('2.0/vistas/menuLateral/menuMotivoFinCanasta.php');
  $('#dvMenuCreaPrestadores').load('2.0/vistas/menuLateral/menuCreaPrestador.php');
  $('#dvMenuConvenio').load('2.0/vistas/menuLateral/menuCreaConvenio.php');
  $('#dvMenuPaquete').load('2.0/vistas/menuLateral/menuCreaPaquete.php');
  $('#dvMenuPrestacion').load('2.0/vistas/menuLateral/menuCreaPrestacion.php');
  $('#dvMenuProblemaSalud').load('2.0/vistas/menuLateral/menuCreaProblemaSalud.php');
</script>