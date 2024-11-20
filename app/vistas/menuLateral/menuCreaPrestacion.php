<ul class="nav nav-pills nav-sidebar flex-column  mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmCreaPrestacion()">
      <i class="nav-icon fas fa-user-edit"></i>
      <p>
        Crear Prestación
      </p>
    </a>
  </li>
</ul>

<script>
	function fnFrmCreaPrestacion(){
		$('#contenido_principal').load('vistas/mantenedores/prestacion/frmCreaPrestacion.php');
	}
</script>