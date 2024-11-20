<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmCasosCerrados()">
      <i class="nav-icon fas fa-users-slash"></i>
      <p>
        Casos Cerrados
      </p>
    </a>
  </li>
</ul>

<script>
  function fnFrmCasosCerrados(){
    $('#contenido_principal').load('2.0/vistas/reportes/tablaDerivacionesCerradas.php');
  }
</script>