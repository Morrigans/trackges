<?php 
require_once 'Connections/oirs.php';
require_once 'includes/functions.inc.php';
  // Solo se permite el ingreso con el inicio de sesion.
  session_start();
  // Si el usuario no se ha logueado se le regresa al inicio.
  if (!isset($_SESSION['loggedin'])) {
  header('Location: index.php');
  exit; }


  require_once('notificaciones/modals/modalMensajeRecibido.php');
  require_once('notificaciones/modals/modalMensajeRecibidoIcrs.php');
  require_once('notificaciones/modals/modalVerTodoMensajes.php');
  require_once('notificaciones/modals/modalVerTodoMensajesIcrs.php');
  require_once('vistas/bitacora/modals/modalBitacora.php');
  require_once('vistas/modulos/pacientesPp/bitacora/modals/modalBitacora.php');
  require_once('vistas/bitacora/modals/modalProgramarTarea.php');
  require_once('vistas/bitacora/modals/modalPlayAudios.php');
  require_once('vistas/alarmas/modals/modalAlarmasInicio.php');
  require_once('vistas/mantenedores/misDatos/modalMisDatos.php');
  require_once('vistas/mantenedores/pacientes/modalInfoPaciente.php');
  require_once('vistas/modulos/contactarPaciente/modalContactarPaciente.php');
  require_once('vistas/bitacora/modals/modalAudios.php');
  require_once('vistas/mantenedores/contactosHospital/modalContactosHospital.php');
  require_once('vistas/mantenedores/contactosHospital/modalVerContactos.php');
  require_once('vistas/mantenedores/patologias/modalEditarPatologia.php');



  $usuario = $_SESSION['dni'];
  $origenMenu = $_REQUEST['origen'];

  require_once 'head/headers.php';

  //*******************************************************************************
  date_default_timezone_set('America/Santiago');
  $hoy= date('Y-m-d');
 
$query_qrBuscaAlarmas = "SELECT * FROM $MM_oirs_DATABASE.alarmas WHERE ESTADO = 'activa' AND FECHA_ALARMA <= '$hoy' AND USUARIO_RECEPTOR = '$usuario'";
$qrBuscaAlarmas = $oirs->SelectLimit($query_qrBuscaAlarmas) or die($oirs->ErrorMsg());
$totalRows_qrBuscaAlarmas = $qrBuscaAlarmas->RecordCount();
  //*******************************************************************************

  $query_verProfesion = "SELECT * FROM $MM_oirs_DATABASE.login where USUARIO='$usuario'";
  $verProfesion = $oirs->SelectLimit($query_verProfesion) or die($oirs->ErrorMsg());
  $totalRows_verProfesion = $verProfesion->RecordCount();

  $profesion=$verProfesion->Fields('TIPO');
  $nombrePro=$verProfesion->Fields('NOMBRE');
  $idClinica=$verProfesion->Fields('ID_PRESTADOR');

  $nom = ucfirst($nombrePro);


  $query_qrProfesion= "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '$profesion'";
  $qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
  $totalRows_qrProfesion = $qrProfesion->RecordCount();

  $query_qrClinicaOrigen= "SELECT * FROM $MM_oirs_DATABASE.prestador WHERE ID_PRESTADOR = '$idClinica'";
  $qrClinicaOrigen = $oirs->SelectLimit($query_qrClinicaOrigen) or die($oirs->ErrorMsg());
  $totalRows_qrClinicaOrigen = $qrClinicaOrigen->RecordCount();

?>
<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TrackGes RedSalud</title>

  
</head>
<?php if ($qrProfesion->Fields('PROFESION') == 'Supervisor' or $qrProfesion->Fields('PROFESION') == 'Administrador') { ?>
  <body class="hold-transition sidebar-mini layout-fixed">
<?php }
if($qrProfesion->Fields('PROFESION') == 'Gestor'){ ?>
  <!-- <body class="hold-transition sidebar-mini sidebar-collapse"> -->
  <body class="hold-transition sidebar-mini sidebar-fixed">
<?php } ?>
  

<input type="hidden" name="usuarioSesion" id="usuarioSesion" value="<?php echo $usuario ?>">
<input type="hidden" name="tipoUsuario" id="tipoUsuario" value="<?php echo $qrProfesion->Fields('PROFESION') ?>">
<input type="hidden" name="alarmas" id="alarmas" value="<?php echo $totalRows_qrBuscaAlarmas ?>">
<input type="hidden" name="origenMenu" id="origenMenu" value="<?php echo $origenMenu ?>">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
    <!-- <i class="fas fa-parking fa-4x">Oncología</i><i class="fas fa-parking fa-4x">RedSalud</i> -->
  </div>

  <!-- navbar -->
  <div id="menuSuperior"></div>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
        <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex row">
        <div class="image">
          <i class="far fa-user-circle fa-2x"></i>
        </div>
        <div class="info">
          <a href="#" class="d-block" data-toggle="modal" data-target="#modalMisDatos" onclick="fnCargaFrmMisDatos('<?php echo $usuario ?>')"><?php echo utf8_encode($nom) ?><br><small class="form-group"><?php echo ucfirst(utf8_encode($qrProfesion->Fields('PROFESION')));?></small><br><small class="form-group"><?php echo ucfirst(utf8_encode($qrClinicaOrigen->Fields('DESC_PRESTADOR')));?></small></a>
          <a href="login/exit.php"><small class="badge badge-danger">Cerrar Sesión</small></a>
          <a href="#" data-toggle="modal" data-target="#modalMisDatos" onclick="fnCargaFrmMisDatos('<?php echo $usuario ?>')"><small class="badge badge-warning">Mis Datos</small></a>
        </div>
        <!-- <br>
        <button type="button" class="btn btn-lg btn-block btn-outline-warning" data-toggle="modal" data-target="#modalMisDatos" onclick="fnCargaFrmMisDatos('<?php echo $usuario ?>')">Mis Datos</button> -->
      </div>

      <!-- Sidebar Menu principal -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <!-- para agregar menus solo debes agregar los div que apunten al archivo que contiene el menu con su funcion en el load -->
            <div id="dvMenuSubeRn"></div>
            <div id="dvMenuPp"></div>
            <div id="dvMenuDerivacion"></div>
            <div id="dvMenuPacientesCerrados"></div>
            <div id="dvMenuPacientesCerradosGestora"></div>
            <div id="dvMenuBitacoraAdministrativa"></div>
            
          </li> 
          <!-- menu de mantenedores, en el div dvMenuMantenedores carga el acordeon que esta en archivo menuMantenedores -->
          <li class="nav-item">
            <div id="dvMenuMantenedores"></div>
          </li>
          <!-- menu de reportes, en el div dvMenuReportes carga el acordeon que esta en archivo menuReportes -->
          <li class="nav-item">
            <div id="dvMenuDash"></div>
            <div id="dvMenuDashboard"></div>
            <div id="dvMenuReportes"></div> 
            
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
 
 
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <div id="contenido_principal"></div>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

   
  </div>
  <!-- /.content-wrapper -->
  <!-- <footer class="main-footer">
    <strong>Copyright &copy; 2022 <a href="https://www.southplattform.com">South Plattform</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> Oficial
    </div>
  </footer> -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->



</body>
</html>

<script>
  usuario = $('#usuarioSesion').val();
  tipoUsuario = $('#tipoUsuario').val(); 
  alarmas = $('#alarmas').val();
  origenMenu = $('#origenMenu').val();

  if (alarmas > 0) {
    $("#modalAlarmasInicio").modal("show");
    $('#dvFrmAlarmasInicio').load('vistas/alarmas/modals/frmAlarmasInicio.php');
  }

	function fnCargaFrmMisDatos(usuario){
    
    $('#dvCargaFrmMisDatos').load('vistas/mantenedores/misDatos/frmActualizaMisDatos.php?usuario=' + usuario);
	}

  $("#menuSuperior").load('notificaciones/navbar.php');// carga menu superior la primera vez, en menu superior manejo notificaciones

  setInterval(function(){
    $("#menuSuperior").load('notificaciones/navbar.php');// carga menu superior cada x segundos, en menu superior manejo notificaciones
  }, 600000);

  if (tipoUsuario == 'Administrador') {
     if (origenMenu == 'icrs') {
         $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php');
     }else{
        $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); //pagina que se carga al iniciar sesion o recargar la pagina
     }
    // $('#dvMenuSubeRn').load('vistas/menuLateral/menuSubeRn.php');//menu para subir diariamente reporte rn
    $('#dvMenuPp').load('vistas/menuLateral/menuPrimerPrestador.php');//menu  bandeja instituto  del  cancer
    // $('#dvMenuDerivacion').load('vistas/menuLateral/menuDerivacion.php');//menu para crear derivacion del paciente
    $('#dvMenuMantenedores').load('vistas/menuLateral/menuMantenedores.php'); // menu que contiene submenus con mantenedores
    //$('#dvMenuPacientesCerrados').load('vistas/menuLateral/menuPacientesCerrados.php');
    $('#dvMenuReportes').load('vistas/menuLateral/menuReportes.php'); // menu que contiene submenus con mantenedores
    $('#dvMenuDash').load('vistas/menuLateral/menuDash.php'); // menu que contiene submenus con mantenedores
    $('#dvMenuDashboard').load('vistas/menuLateral/menuDashboard.php'); // menu que contiene submenus con mantenedores
    
  
  }

  if (tipoUsuario == 'Supervisor') {

     if (origenMenu == 'icrs') {
         $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php');
     }else{
        $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); //pagina que se carga al iniciar sesion o recargar la pagina
     }
   
    // $('#dvMenuDerivacion').load('vistas/menuLateral/menuDerivacion.php');//menu para crear derivacion del paciente
    $('#dvMenuPp').load('vistas/menuLateral/menuPrimerPrestador.php');//menu  bandeja instituto  del  cancer
    $('#dvMenuMantenedores').load('vistas/menuLateral/menuMantenedores.php'); // menu que contiene submenus con mantenedores
    //$('#dvMenuPacientesCerrados').load('vistas/menuLateral/menuPacientesCerrados.php');
    $('#dvMenuReportes').load('vistas/menuLateral/menuReportes.php'); // menu que contiene submenus con mantenedores
    $('#dvMenuBitacoraAdministrativa').load('vistas/menuLateral/menuBitacoraAdministrativa.php');//menu para crear derivacion del paciente
    $('#dvMenuDash').load('vistas/menuLateral/menuDash.php'); // menu que contiene submenus con mantenedores
    $('#dvMenuDashboard').load('vistas/menuLateral/menuDashboard.php'); // menu que contiene submenus con mantenedores
  }

  if (tipoUsuario == 'Gestor') {
     if (origenMenu == 'icrs') {
         $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php');
     }else{
        $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); //pagina que se carga al iniciar sesion o recargar la pagina
     }
    $('#dvMenuPp').load('vistas/menuLateral/menuPrimerPrestador.php');//menu  bandeja instituto  del  cancer
    $('#dvMenuReportes').load('vistas/menuLateral/menuReportes.php'); // menu que contiene submenus con reportes
    $('#dvMenuDash').load('vistas/menuLateral/menuDash.php'); // menu que contiene submenus con mantenedores
    $('#dvMenuDashboard').load('vistas/menuLateral/menuDashboard.php'); // menu que contiene submenus con mantenedores
  }

  if (tipoUsuario == 'Administrativa') {
     if (origenMenu == 'icrs') {
         $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php');
     }else{
        $('#contenido_principal').load('vistas/inicio/inicioAdministrativa/inicioAdministrativa.php'); //pagina que se carga al iniciar sesion o recargar la pagina
     }
    $('#dvMenuPp').load('vistas/menuLateral/menuPrimerPrestador.php');//menu  bandeja instituto  del  cancer
     $('#dvMenuReportes').load('vistas/menuLateral/menuReportes.php'); // menu que contiene submenus con mantenedores
     $('#dvMenuDash').load('vistas/menuLateral/menuDash.php'); // menu que contiene submenus con mantenedores
  }
  if (tipoUsuario == 'Tens') {
     if (origenMenu == 'icrs') {
         $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php');
     }else{
        $('#contenido_principal').load('vistas/inicio/inicioTens/inicioTens.php'); //pagina que se carga al iniciar sesion o recargar la pagina
     }
    $('#dvMenuPp').load('vistas/menuLateral/menuPrimerPrestador.php');//menu  bandeja instituto  del  cancer
    $('#dvMenuDash').load('vistas/menuLateral/menuDash.php'); // menu que contiene submenus con mantenedores
    //$('#dvMenuReportes').load('vistas/menuLateral/menuReportes.php'); // menu que contiene submenus con reportes
  } 

  if (tipoUsuario == 'Administrativa Isapre') {
    $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php');
   $('#dvMenuPp').load('vistas/menuLateral/menuPrimerPrestador.php');//menu  bandeja instituto  del  cancer
  }

</script>


