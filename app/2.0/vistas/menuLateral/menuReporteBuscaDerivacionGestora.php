<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnBuscaDerivacionGestora()">
      <i class="nav-icon fas fa-calendar"></i>
      <p>
       Derivación por paciente
        </p>
    </a>
  </li>
</ul>

<script>
  function fnBuscaDerivacionGestora(){
    $('#contenido_principal').load('2.0/vistas/reportes/buscaDerivacionGestora/frmBuscaDerivacionGestora.php');
  }
</script>