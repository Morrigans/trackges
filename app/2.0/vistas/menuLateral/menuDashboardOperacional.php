<ul class="nav nav-pills nav-sidebar flex-column mx-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnDashOperacional()">
      <i class="nav-icon fas fa-book-medical"></i>
      <p>
        Operacional
      </p>
    </a>
  </li>
</ul>

<script>
	function fnDashOperacional(){
		$('#contenido_principal').load('2.0/vistas/dashboard/dashboardOperacional.php');
	}
</script>