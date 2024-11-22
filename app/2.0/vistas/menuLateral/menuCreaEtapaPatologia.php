<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnCreaEtapaPatologia()">
      <i class="nav-icon fas fa-list-ol"></i>
      <p>
        Crea Etapa
      </p>
    </a>
  </li>
</ul>

<script>
	function fnCreaEtapaPatologia(){
		$('#contenido_principal').load('vistas/mantenedores/etapaPatologias/frmCreaEtapaPatologia.php');
	}
</script>