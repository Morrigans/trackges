<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnCreaMotivoVencimiento()">
      <i class="nav-icon fas fa-user-md"></i>
      <p>
        Motivo Fin Canastas
      </p>
    </a>
  </li>
</ul>

<script>
	function fnCreaMotivoVencimiento(){
		$('#contenido_principal').load('vistas/mantenedores/motivoFinCanasta/frmMotivoFinCanasta.php'); 
	}
</script>