<ul class="nav nav-pills nav-sidebar flex-column mx-sm-4">
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="fnCreaContactoHospitales()">
      <i class="nav-icon fas fa-procedures"></i>
      <p>
        Contacto Hospital
      </p>
    </a>
  </li>
</ul>

<script>
	function fnCreaContactoHospitales(){
   
		$('#contenido_principal').load('vistas/mantenedores/contactosHospital/tablaHospitales.php');
    // $('#contenido_principal').load('vistas/mantenedores/pacientes/frmCreaPaciente.php');
	}
</script>