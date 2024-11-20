<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnCreaPatologia()">
      <i class="nav-icon fas fa-book-medical"></i>
      <p>
        Crea Patología
      </p>
    </a>
  </li>
</ul>

<script>
	function fnCreaPatologia(){
		$('#contenido_principal').load('vistas/mantenedores/patologias/frmCreaPatologia.php');
	}
</script>