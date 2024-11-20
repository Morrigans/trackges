<ul class="nav nav-pills nav-sidebar flex-column  mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmConvenio()">
      <i class="nav-icon fas fa-user-edit"></i>
      <p>
        Crear Convenio
      </p>
    </a>
  </li>
</ul>

<script>
	function fnFrmConvenio(){
		$('#contenido_principal').load('vistas/mantenedores/convenio/frmCreaConvenio.php');
	}
</script>