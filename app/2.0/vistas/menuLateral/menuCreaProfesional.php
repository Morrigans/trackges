<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnCreaProfesional()">
      <i class="nav-icon fas fa-user-md"></i>
      <p>
        Crea Usuario
      </p>
    </a>
  </li>
</ul>

<script>
	function fnCreaProfesional(){
		$('#contenido_principal').load('vistas/mantenedores/profesionales/frmCreaProfesional.php'); 
	}
</script>