<ul class="nav nav-pills nav-sidebar flex-column">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmPacientesDerivados()">
      <i class="nav-icon fas fa-user-clock"></i>
      <p>
        Pacientes Derivados
      </p>
    </a>
  </li>
</ul>

<script>
  function fnFrmPacientesDerivados(){
    $('#contenido_principal').load('2.0/vistas/pacientesDerivados/tablaPacientesDerivados.php');
  }
</script>