<ul class="nav nav-pills nav-sidebar flex-column mx-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnDashFinanciero()">
      <i class="nav-icon fas fa-book-medical"></i>
      <p>
        Financiero
      </p>
    </a>
  </li>
</ul>

<script>
	function fnDashFinanciero(){
		$('#contenido_principal').load('2.0/vistas/dashboard/dashboardFinanciero.php');
	}
</script>