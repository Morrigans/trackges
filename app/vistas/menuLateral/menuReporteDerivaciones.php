<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmDerivaciones()">
      <i class="nav-icon fas fa-users-slash"></i>
      <p>
        Derivaciones
      </p>
    </a>
  </li>
</ul>

<script>
  function fnFrmDerivaciones(){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/reportes/tablaDerivaciones.php');
  }
</script>