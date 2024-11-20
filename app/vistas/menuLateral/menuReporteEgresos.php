<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmReporteEgresos()">
      <i class='nav-icon fa fa-calendar'></i>
      <p>
        Egresos
      </p>
    </a>
  </li>
</ul>

<script>
  function fnFrmReporteEgresos(){
    $('#contenido_principal').load('vistas/reportes/egresos/frmReporteEgresos.php');
  }
</script>