<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnVencidasFinalizadas()">
      <i class="nav-icon fas fa-calendar"></i>
      <p>
       Canastas vencidas/finalizadas
      </p>
    </a>
  </li>
</ul>

<script>
  function fnVencidasFinalizadas(){
    $('#contenido_principal').load('vistas/reportes/tablaCanastasVencidasFinalizadas.php');
  }
</script>