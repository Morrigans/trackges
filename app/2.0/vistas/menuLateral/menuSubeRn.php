<ul class="nav nav-pills nav-sidebar flex-column">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnSubeRn()">
      <i class="nav-icon fas fa-user-edit"></i>
      <p>
        Sube BBDD RN
      </p>
    </a>
  </li>
</ul>

<script>
	function fnSubeRn(){
		$('#contenido_principal').load('2.0/vistas/subeRn/index.php');
	}
</script>