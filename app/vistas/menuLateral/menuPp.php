<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmPacientesPp()">
      <i class="nav-icon fas fa-file-alt"></i>
      <p>
        Derivaciones
      </p>
    </a>
  </li>
</ul>

<script>
	function fnFrmPacientesPp(){
		$('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php');
	}
</script>