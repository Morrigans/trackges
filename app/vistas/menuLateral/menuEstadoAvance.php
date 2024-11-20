<ul class="nav nav-pills nav-sidebar flex-column">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnFrmEstadoAvance()">
      <i class="nav-icon fas fa-user-edit"></i>
      <p>
        Estado Avance
      </p>
    </a>
  </li>
</ul>

<script>
	function fnFrmEstadoAvance(){
		$('#contenido_principal').load('2.0/vistas/reportes/estadoAvance/estadoAvance.php');
	}
</script>