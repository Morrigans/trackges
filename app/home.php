<?php 
require_once 'Connections/oirs.php';
require_once 'includes/functions.inc.php';
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
  header('Location: index.php');
  exit; 
}


$usuario = $_SESSION['dni'];

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
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel TrackGes</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <!-- Font Awesome (para íconos) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-Z6p74hQGp0vEZVZDYBw69jURze4GKg0yqApt6oY1UHH5l3eaTJbD2U3KwE5aR20b3LtF6EBFvMmlhhDN5Jix2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body {
      background-color: #f8f9fa; /* Fondo suave */
    }
    .card {
      border-radius: 15px;
      margin-bottom: 20px;
    }
    .card-title {
      background-color: #343a40;
      color: white;
      padding: 10px;
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
    }
    .btn {
      margin-bottom: 10px;
    }
    .navbar-brand {
      font-weight: bold;
    }
    .navbar-nav .nav-item .nav-link {
      color: #ffffff !important;
    }
    .navbar-dark {
      background-color: #343a40;
    }
  </style>
</head>
<body>

  <!-- Menú superior -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">TrackGes Redsalud</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="fas fa-user"></i> Usuario: <?php echo utf8_encode($nom) ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="login/exit.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- Modal en construcción -->
  <div class="modal fade" id="modalEnConstruccion" tabindex="-1" aria-labelledby="modalEnConstruccionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEnConstruccionLabel">En construcción</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Esta función está actualmente en construcción. Disculpe las molestias.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Contenido principal -->
  <div class="container mt-4">
    <div class="row justify-content-center">
       <div class="col-md-6 mb-4">
        <div class="card">
          <h5 class="card-title text-center"><i class="fas fa-briefcase"></i> Primer Prestador</h5>
          <div class="card-body">
            <div id="accordion1">
              <div class="card">
                <div class="card-header">
                  <h6 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                      <i class="fas fa-notes-medical"></i> Isapres
                    </button>
                  </h6>
                </div>

                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion1">
                  <div class="card-body">
                    En esta sección encontraras los casos derivados desde las Isapres.
                    <button id="btnIrIsapres" class="btn btn-dark btn-block mt-3" onclick="fnIrPrincipal()"><i class="fas fa-arrow-right"></i> Ir a Isapres</button>
                  </div>
                </div>
              </div>

              <div class="card mt-2">
                <div class="card-header">
                  <h6 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                      <i class="fas fa-notes-medical"></i> Instituto del Cáncer
                    </button>
                  </h6>
                </div>

                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion1">
                  <div class="card-body">
                    En esta sección encontraras los casos derivados desde Instituto del Cáncer.
                    <button id="btnIrCancer" class="btn btn-dark btn-block mt-3" onclick="fnIrPrincipal()"><i class="fas fa-arrow-right"></i> Ver casos Instituto del Cáncer</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 mb-4">
        <div class="card">
          <h5 class="card-title text-center"><i class="fas fa-briefcase"></i> Segundo Prestador</h5>
          <div class="card-body">
            <div id="accordion2">
              <div class="card">
                <div class="card-header">
                  <h6 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                      <i class="fas fa-history"></i> Licitación anterior
                    </button>
                  </h6>
                </div>

                <div id="collapseThree" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion2">
                  <div class="card-body">
                    En esta sección encontrarás toda la información de casos de la antigua licitación FONASA
                    <a href="principal.php" class="btn btn-dark btn-block mt-3"><i class="fas fa-arrow-right"></i> Ir a TrackGes licitación anterior</a>
                  </div>
                </div>
              </div>
              <?php if ($profesion == 7 or $profesion == 8){ ?>
                  <div class="card mt-2">
                    <div class="card-header">
                      <h6 class="mb-0">
                        <button  class="btn btn-link" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour" disabled>
                          <i class="fas fa-plus-circle"></i> Nueva licitación
                        </button>
                      </h6>
                    </div>

                    <div id="collapseFour" class="collapse show" aria-labelledby="headingThree" data-parent="#accordion2">
                      <div class="card-body">
                        En esta sección encontrarás toda la información de casos de la nueva licitación FONASA
                       <a href="javascript:void(0);" id="btnIrNuevaLicitacion" class="btn btn-dark btn-block mt-3 disabled" onclick="return false;" style="pointer-events: none;">
                        <i class="fas fa-arrow-right"></i> Ir a Trackges nueva licitación
                      </a>
                        <!-- data-toggle="modal" data-target="#modalEnConstruccion" -->
                      </div>
                    </div>
                  </div>
              <?php }else{ ?>
                  <div class="card mt-2">
                    <div class="card-header">
                      <h6 class="mb-0">
                        <button  class="btn btn-link" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                          <i class="fas fa-plus-circle"></i> Nueva licitación
                        </button>
                      </h6>
                    </div>

                    <div id="collapseFour" class="collapse show" aria-labelledby="headingThree" data-parent="#accordion2">
                      <div class="card-body">
                        En esta sección encontrarás toda la información de casos de la nueva licitación FONASA
                        <a href="#" id="btnIrNuevaLicitacion" class="btn btn-dark btn-block mt-3" onclick="fnIrPrincipal2()" ><i class="fas fa-arrow-right"></i> Ir a Trackges nueva licitación</a>
                        <!-- data-toggle="modal" data-target="#modalEnConstruccion" -->
                      </div>
                    </div>
                  </div>
             <?php } ?>
              


            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Font Awesome (para íconos) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" integrity="sha512-fON1XqD1kM3K2FF0Rpk6/y8eKx1ps9ZxkE8kHcAt/SIbYwpk3j8thxk5hzA+8U9DkNnJZDJyv++C/4NofP0Lxw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <!-- jQuery y Popper.js necesarios para Bootstrap -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js" integrity="sha384-o3S9i+xUw04ZdLsVhLxyu5EZu3YwgK3qc9sY7uBznJ2b6St7fF0izPzI5Y6Lx9h" crossorigin="anonymous"></script>
  <!-- Bootstrap JS -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

  <script>
    
    // Mostrar modal al hacer clic en el botón de nueva licitación
    // $('#btnIrNuevaLicitacion').click(function() {
    //   $('#modalEnConstruccion').modal('show');
    // });

    function fnIrPrincipal() {
      // Crear un formulario dinámico
      var form = document.createElement('form');
      form.method = 'post';
      form.action = 'principal.php';

      // Crear un input para enviar 'origen' como variable POST
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'origen';
      input.value = 'icrs';

      // Agregar el input al formulario
      form.appendChild(input);

      // Agregar el formulario al cuerpo del documento
      document.body.appendChild(form);

      // Enviar el formulario
      form.submit();
  }

    function fnIrPrincipal2() {
      // Crear un formulario dinámico
      var form = document.createElement('form');
      form.method = 'post';
      form.action = 'principal2.0.php';
      // Crear un input para enviar 'origen' como variable POST
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'origen';
      input.value = '';

      // Agregar el input al formulario
      form.appendChild(input);

      // Agregar el formulario al cuerpo del documento
      document.body.appendChild(form);
      // Enviar el formulario
      form.submit();
  }
  </script>

  <!-- Pie de página -->
  <footer class="bg-dark text-white text-center py-3">
    <p class="navbar-brand">TrackGes Redsalud</p>
    <p>Desarrollado por South Platform Systems. Todos los derechos reservados.</p>
  </footer>
</body>
</html>
