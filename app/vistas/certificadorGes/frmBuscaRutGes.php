<?php 
require_once 'headers.php';
require_once '../../Connections/oirs.php';

//Aditional Functions
require_once '../../includes/functions.inc.php';

$query_qrBuscaGestor= "SELECT * FROM $MM_oirs_DATABASE.login where TIPO='3'";
$qrBuscaGestor = $oirs->SelectLimit($query_qrBuscaGestor) or die($oirs->ErrorMsg());
$totalRows_qrBuscaGestor = $qrBuscaGestor->RecordCount();

?>
</br>
<div class="container">
  <p><h3 class="text-primary" align="center">Consulta de vigencia de beneficios GES - Convenio Fonasa Segundo Prestador</h3></p>
  </br>
  <div></div>
  <div class="card card-info" >
    
    <div class="card-header">
      <h3 class="card-title">Ingrese rut de paciente</h3>
      <div class="card-tools">
        <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
          <i class="fas fa-minus"></i>
        </button>
        <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>

      <div class="card-body">
          <div class="row col-sm-12">

              <div class="input-group mb-3 col-sm-3">                
              </div>
              <div class="input-group mb-3 col-sm-5" align="center">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rut Paciente</span>
                </div>
                <input type="text" id="rutPacienteGes" name="rutPacienteGes" class="form-control input-sm" placeholder="12345678-9">
              </div>              
              <div class="input-group mb-3 col-sm-4">
                <button type="button" class="btn btn-xs btn-info" onclick="fnBuscaRutGes()">Buscar</button>
              </div>
   
          </div>

          <div id="dvMuestraTablaPacienteGes"></div>
      </div>
    </div> 

</div>


<script type="text/javascript">

// $("#rutPacienteGes").rut({formatOn: 'keyup'}).on('rutInvalido', function(e, rut, dv) {
//   // swal("Oops!", "El rut " + $(this).val() + " es inválido", "warning");
//   swal("Oops!", "Este no es un rut valido, corrija y vuelva a intentarlo", "warning");
// }); 

// function fnBuscaDerivacion(rutGestora){
   
//     rutGestora=rutGestora.value;

 
// $('#tablabuscaDerivacionGestora').load('vistas/reportes/buscaDerivacionGestora/tablaBuscaDerivacionGestora.php?rutGestora='+rutGestora);

// }


function fnBuscaRutGes(){

  var rutPaciente= $("#rutPacienteGes").val();
  $('#dvMuestraTablaPacienteGes').load('tablaMuestraPacienteGes.php?rutPaciente='+rutPaciente);

}


</script>
