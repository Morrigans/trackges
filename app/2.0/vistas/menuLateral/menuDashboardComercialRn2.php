<ul class="nav nav-pills nav-sidebar flex-column mx-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnDashComercial()">
      <i class="nav-icon fas fa-book-medical"></i>
      <p>
        Comercial
      </p>
    </a>
  </li>
</ul>

<script>
	function fnDashComercial(){
		$('#contenido_principal').load('2.0/vistas/dashboard/dashboardComercial.php');
	}
</script>