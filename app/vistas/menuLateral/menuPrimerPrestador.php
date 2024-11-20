<?php 
  require_once '../../Connections/oirs.php';
  require_once '../../includes/functions.inc.php';
  // Solo se permite el ingreso con el inicio de sesion.
  session_start();
  // Si el usuario no se ha logueado se le regresa al inicio.
  if (!isset($_SESSION['loggedin'])) {
  header('Location: ../../index.php');
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
          Primer Prestador
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <?php if ($usuario == '99.999.999-9') { ?>
            <div id="dvMenuDerivacionPp"></div>
          <?php } ?>
          <div id="dvMenuPrimerPrestador"></div>
          <div id="dvMenuCasosCerrados"></div>
        </li>
      </ul>
    </li>
</ul>
 
            
<script>
  $('#dvMenuDerivacionPp').load('vistas/menuLateral/menuDerivacionPp.php');
  $('#dvMenuPrimerPrestador').load('vistas/menuLateral/menuPp.php');
  $('#dvMenuCasosCerrados').load('vistas/menuLateral/menuCasosCerradosPp.php'); 

</script>