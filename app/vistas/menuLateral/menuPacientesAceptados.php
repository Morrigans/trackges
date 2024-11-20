<ul class="nav nav-pills nav-sidebar flex-column">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmPacientesAceptados()">
      <i class="nav-icon fas fa-user-check"></i>
      <p>
        Pacientes Aceptados
      </p>
    </a>
  </li>
</ul>

<script>
  function fnFrmPacientesAceptados(){
    $('#contenido_principal').load('vistas/pacientesAceptados/tablaPacientesAceptados.php');
  }
</script>