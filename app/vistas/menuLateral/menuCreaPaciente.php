<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnCreaPaciente()">
      <i class="nav-icon fas fa-procedures"></i>
      <p>
        Crea Paciente
      </p>
    </a>
  </li>
</ul>

<script>
	function fnCreaPaciente(){
		$('#contenido_principal').load('vistas/mantenedores/pacientes/frmCreaPaciente.php');
	}
</script>