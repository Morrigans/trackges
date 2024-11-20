<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnCreaGrdReferencia()">
      <i class="nav-icon fas fa-user-md"></i>
      <p>
        Grd Referencia
      </p>
    </a>
  </li>
</ul>

<script>
	function fnCreaGrdReferencia(){
		$('#contenido_principal').load('vistas/mantenedores/grdReferencia/frmCreaGrdReferencia.php'); 
	}
</script>