<ul class="nav nav-pills nav-sidebar flex-column">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnDash()">
      <i class="nav-icon fas fa-book-medical"></i>
      <p>
        Dashboard Provisorio
      </p>
    </a>
  </li>
</ul>

<script>
	function fnDash(){
    
		$('#contenido_principal').load('2.0/vistas/dashboard/dashboard.php');
	}
</script>