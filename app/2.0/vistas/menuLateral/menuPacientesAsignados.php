<ul class="nav nav-pills nav-sidebar flex-column">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmPacientesAsignados()">
      <i class="nav-icon fas fa-hospital-user"></i>
      <p>
        Pacientes Asignados
      </p>
    </a>
  </li>
</ul>

<script>
  function fnFrmPacientesAsignados(){
    $('#contenido_principal').load('2.0/vistas/pacientesAsignados/tablaPacientesAsignados.php');
  }
</script>