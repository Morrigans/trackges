<ul class="nav nav-pills nav-sidebar flex-column">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmMenuBitacoraAdministrativa()">
      <i class="nav-icon fas fa-book"></i>
      <p>
        Bitacora Administrativa
      </p>
    </a>
  </li>
</ul>

<script>
  function fnFrmMenuBitacoraAdministrativa(){
    $('#contenido_principal').load('2.0/vistas/bitacoraAdministrativa/modals/frmBitacoraAdministrativa.php');
  }
</script>