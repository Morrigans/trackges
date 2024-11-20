<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFoliosAnulados()">
      <i class="nav-icon fas fa-users-slash"></i>
      <p>
        Folios anulados
      </p>
    </a>
  </li>
</ul>

<script>
  function fnFoliosAnulados(){
    $('#contenido_principal').load('2.0/vistas/reportes/tablaDerivacionesAnuladas.php');
  }
</script>