<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnCreaPrestador()">
      <i class="nav-icon fas fa-user-plus"></i>
      <p>
        Crea Prestador
      </p>
    </a>
  </li>
</ul>

<script>
	function fnCreaPrestador(){
		$('#contenido_principal').load('vistas/mantenedores/prestadores/frmCreaPrestador.php');
	}
</script>