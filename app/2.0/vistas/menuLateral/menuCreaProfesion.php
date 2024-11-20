<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnCreaProfesion()">
      <i class="nav-icon fas fa-user-md"></i>
      <p>
        Crea Profesión
      </p>
    </a>
  </li>
</ul>

<script>
	function fnCreaProfesion(){
		$('#contenido_principal').load('vistas/mantenedores/profesion/frmCreaProfesion.php'); 
	}
</script>