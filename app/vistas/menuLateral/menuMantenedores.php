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
         <div id="dvMenuGrdReferencia"></div>
        <?php } ?>

        </li>
      </ul>
    </li>
</ul>
           
<script>
  $('#dvMenuCreaPaciente').load('vistas/menuLateral/menuCreaPaciente.php');
  $('#dvMenuContactosHospital').load('vistas/menuLateral/menuContactosHospital.php');
  $('#dvMenuCreaProfesional').load('vistas/menuLateral/menuCreaProfesional.php');
  $('#dvMenuCreaPatologia').load('vistas/menuLateral/menuCreaPatologia.php');
  $('#dvMenuCreaEtapaPatologia').load('vistas/menuLateral/menuCreaEtapaPatologia.php');
  $('#dvMenuCanastasPatologias').load('vistas/menuLateral/menuCreaCanastaPatologia.php');
  $('#dvMenuMotivoFinCanasta').load('vistas/menuLateral/menuMotivoFinCanasta.php');
  $('#dvMenuCreaPrestadores').load('vistas/menuLateral/menuCreaPrestador.php');
  $('#dvMenuConvenio').load('vistas/menuLateral/menuCreaConvenio.php');
  $('#dvMenuPaquete').load('vistas/menuLateral/menuCreaPaquete.php');
  $('#dvMenuPrestacion').load('vistas/menuLateral/menuCreaPrestacion.php');
  $('#dvMenuProblemaSalud').load('vistas/menuLateral/menuCreaProblemaSalud.php');
  $('#dvMenuGrdReferencia').load('vistas/menuLateral/menuCreaGrdReferencia.php');
</script>