<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnCreaProblemaSalud()">
      <i class="nav-icon fas fa-user-md"></i>
      <p>
        Problemas de salud
      </p>
    </a>
  </li>
</ul>

<script>
	function fnCreaProblemaSalud(){
		$('#contenido_principal').load('2.0/vistas/mantenedores/problemaSalud/frmCreaProblemaSalud.php'); 
	}
</script>