<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnTablaCasosCerradosPp()">
      <i class="nav-icon fas fa-file-alt"></i>
      <p>
        Casos Cerrados
      </p>
    </a>
  </li>
</ul>

<script>
	function fnTablaCasosCerradosPp(){
		$('#contenido_principal').load('2.0/vistas/modulos/pacientesPp/reportes/tblCasosCerradosPp.php');
	}
</script>