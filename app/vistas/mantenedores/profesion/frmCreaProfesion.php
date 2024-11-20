<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_qrRegion = "SELECT * FROM $MM_oirs_DATABASE.regiones";
$qrRegion = $oirs->SelectLimit($query_qrRegion) or die($oirs->ErrorMsg());
$totalRows_qrRegion = $qrRegion->RecordCount();

?>

<form id="frmCreaProfesion">
  <div class="card card-info">
    
    <div class="card-header">
      <h3 class="card-title">Datos de la profesion</h3>
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
              <div class="input-group mb-3 col-sm-4">
                <div class="input-group-prepend">
                  <span class="input-group-text">Profesión</span>
                </div>
                <input type='text' class="form-control input-sm" name="profesion" id="profesion"/>
              </div>
              <div class="input-group mb-3 col-sm-4">
                <div class="input-group-prepend">
                  <span class="input-group-text">Valor Pago Profesión</span>
                </div>
                <input type='number' class="form-control input-sm" name="pagoProfesional" id="pagoProfesional"/>
              </div>
              <div class="input-group mb-3 col-sm-4">
                <div class="input-group-prepend">
                  <span class="input-group-text">Color Profesión</span>
                </div>
                <input type='color' class="form-control input-sm" name="colorProfesion" id="colorProfesion" value="#0a96d1" />
              </div>
          </div>
      </div>
      <div class="card-footer">
            <button type="submit" class="btn btn-info">Guardar Profesion</button>
      </div>
  </div> 
</form>
<br>
   <div id="tablaProfesion"></div>

<script type="text/javascript">
$('#tablaProfesion').load('vistas/mantenedores/profesion/tablaProfesion.php');

$(function () {
  $.validator.setDefaults({
    submitHandler: function () {
      fnGuardaCreaProfesion();
    }
  });
  $('#frmCreaProfesion').validate({
    rules: {
      profesion: {
        required: true
      },
      pagoProfesional: {
        required: true
      }
    },
   messages: {
    profesion: {
      required: "Ingrese Profesión"
    },
    pagoProfesional: {
      required: "Ingrese valor pago profesional"
    }
   },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.input-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });
});

function fnGuardaCreaProfesion(){
    profesion = $('#profesion').val();
    pagoProfesional = $('#pagoProfesional').val();
    colorProfesion = $('#colorProfesion').val();
   
    cadena = 'profesion=' + profesion +
             '&pagoProfesional=' + pagoProfesional +
             '&colorProfesion=' + colorProfesion;
    $.ajax({
        type:"post",
        data:cadena,
        url:'vistas/mantenedores/profesion/guardaCreaProfesion.php',
        success:function(r){
            if (r == 1) {
                swal("Genial!", "Profesion creada correctamente", "success");
                $('#tablaProfesion').load('vistas/mantenedores/profesion/tablaProfesion.php');
                $('#profesion').val('');
                $('#pagoProfesional').val('');
                $('#colorProfesion').val('#0a96d1');
                
            } else {
            }
            
        }
    });
}


</script>
