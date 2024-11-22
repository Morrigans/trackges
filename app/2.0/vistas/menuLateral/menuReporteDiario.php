<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmCasosDiarios()">
      <i class='nav-icon fa fa-calendar'></i>
      <p>
        Reg. actividad diaria
      </p>
    </a>
  </li>
</ul>

<script>
  function fnFrmCasosDiarios(){
    $('#contenido_principal').load('2.0/vistas/reportes/reporteDiario/frmReporteDiario.php');
  }
</script>