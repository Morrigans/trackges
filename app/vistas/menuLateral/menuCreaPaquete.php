<ul class="nav nav-pills nav-sidebar flex-column  mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmCreaPaquete()">
      <i class="nav-icon fas fa-user-edit"></i>
      <p>
        Crear Paquete
      </p>
    </a>
  </li>
</ul>

<script>
	function fnFrmCreaPaquete(){
		$('#contenido_principal').load('vistas/mantenedores/paquete/frmCreaPaquete.php');
	}
</script>