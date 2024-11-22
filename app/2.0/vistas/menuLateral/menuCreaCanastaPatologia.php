<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnCreaCanastaPatologia()">
      <i class="nav-icon fas fa-cart-plus"></i>
      <p>
        Crea Canasta
      </p>
    </a>
  </li>
</ul>

<script>
	function fnCreaCanastaPatologia(){
		$('#contenido_principal').load('vistas/mantenedores/canastasPatologias/frmCreaCanasta.php');
	}
</script>