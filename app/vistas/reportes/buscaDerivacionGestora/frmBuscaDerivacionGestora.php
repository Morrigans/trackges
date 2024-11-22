<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_qrBuscaGestor= "SELECT * FROM $MM_oirs_DATABASE.login where TIPO='3'";
$qrBuscaGestor = $oirs->SelectLimit($query_qrBuscaGestor) or die($oirs->ErrorMsg());
$totalRows_qrBuscaGestor = $qrBuscaGestor->RecordCount();

?>

<form id="frmCreaProfesional">
  <div class="card card-info">
    
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
            
          
              <!-- <div class="input-group mb-3 col-sm-6">
                <div class="input-group-prepend">
                  <span class="input-group-text">Gestora</span>
                </div>
                <select name="slGestora" id="slGestora" onchange="fnBuscaDerivacion(this);" class="form-control input-sm">
                    <option  value="">Seleccione...</option>
                     <?php 
                     while (!$qrBuscaGestor->EOF) {?>
                       <option value="<?php echo $qrBuscaGestor->Fields('USUARIO'); ?>"><?php echo $qrBuscaGestor->Fields('NOMBRE'); ?></option>
                    <?php $qrBuscaGestor->MoveNext(); } ?>
                </select>
              </div> -->


              <div class="input-group mb-3 col-sm-6">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rut Paciente</span>
                </div>
                <input type="text" id="rutPacienteBuscaDerivacion" name="rutPacienteBuscaDerivacion" class="form-control input-sm">
              </div>
              
              <div class="input-group mb-3 col-sm-3">
                <button type="button" class="btn btn-xs btn-info" onclick="fnBuscaDerivacionxRutPac()">Buscar</button>
              </div>
   
          </div>
      </div>
    </div> 
</form>
<br>
   <div id="tablabuscaDerivacionGestora"></div>

<script type="text/javascript">

$("#rutPacienteBuscaDerivacion").rut({formatOn: 'keyup'}).on('rutInvalido', function(e, rut, dv) {
  // swal("Oops!", "El rut " + $(this).val() + " es inválido", "warning");
  swal("Oops!", "Este no es un rut valido, corrija y vuelva a intentarlo", "warning");
}); 

// function fnBuscaDerivacion(rutGestora){
   
//     rutGestora=rutGestora.value;

 
// $('#tablabuscaDerivacionGestora').load('vistas/reportes/buscaDerivacionGestora/tablaBuscaDerivacionGestora.php?rutGestora='+rutGestora);

// }


function fnBuscaDerivacionxRutPac(){

  var rutPaciente= $("#rutPacienteBuscaDerivacion").val();
  $('#tablabuscaDerivacionGestora').load('vistas/reportes/buscaDerivacionGestora/tablaBuscaDerivacionxPaciente.php?rutPaciente='+rutPaciente);

}


</script>
