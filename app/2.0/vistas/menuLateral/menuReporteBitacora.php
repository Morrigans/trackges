<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmReporteBitacora()">
      <i class='nav-icon fa fa-calendar'></i>
      <p>
        Bitacora
      </p>
    </a>
  </li>
</ul>

<script>
  function fnFrmReporteBitacora(){
    $('#contenido_principal').load('2.0/vistas/reportes/bitacora/frmReporteBitacora.php');
  }
</script>