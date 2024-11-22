<ul class="nav nav-pills nav-sidebar flex-column">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmDerivacion()">
      <i class="nav-icon fas fa-user-edit"></i>
      <p>
        Crear Derivación
      </p>
    </a>
  </li>
</ul>

<script>
	function fnFrmDerivacion(){
		$('#contenido_principal').load('2.0/vistas/derivacion/frmDerivacion.php');
	}
</script>