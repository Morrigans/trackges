<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmDerivacionPp()">
      <i class="nav-icon fas fa-user-edit"></i>
      <p>
        Crear Derivación
      </p>
    </a>
  </li>
</ul>

<script>
	function fnFrmDerivacionPp(){
		$('#contenido_principal').load('vistas/modulos/pacientesPp/crearDerivacion/frmDerivacion.php');
	}
</script>