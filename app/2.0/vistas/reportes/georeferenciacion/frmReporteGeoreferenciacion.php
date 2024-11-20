<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_qrRegion = "SELECT * FROM $MM_oirs_DATABASE.regiones";
$qrRegion = $oirs->SelectLimit($query_qrRegion) or die($oirs->ErrorMsg());
$totalRows_qrRegion = $qrRegion->RecordCount();

?>

<form id="frmReporteGeoreferenciacion">
  <div class="card card-info">
    
    <div class="card-header">
      <h3 class="card-title">Datos de la busqueda</h3>
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
              <div class="col-md-4">
                <span class="label label-default">Fecha Inicial</span>
                <div class='input-group date' id='datetimepickerFecInicial' data-target-input="nearest">
                    <input type='text' class="form-control datetimepicker-input" name="fecha1Geo" id="fecha1Geo" data-target="#datetimepickerFecInicial" />
                   <div class="input-group-append" data-target="#datetimepickerFecInicial" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
              </div>

              <div class="col-md-4">
                <span class="label label-default">Fecha Final</span>
                <div class='input-group date' id='datetimepickerFecFinal' data-target-input="nearest">
                    <input type='text' class="form-control datetimepicker-input" name="fecha2Geo" id="fecha2Geo" data-target="#datetimepickerFecFinal" />
                   <div class="input-group-append" data-target="#datetimepickerFecFinal" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
              </div>
              
          </div>
      </div>
      <div class="card-footer">
            <button type="submit" class="btn btn-info">Generar Reporte</button>
      </div>
  </div> 
</form>
<br>
   <div id="dvTablaReporteGeoreferenciacion"></div>

<script type="text/javascript">
    $('#datetimepickerFecInicial').datetimepicker({
        format: 'DD-MM-YYYY',
        locale:'es'
    });

    $('#datetimepickerFecFinal').datetimepicker({
        format: 'DD-MM-YYYY',
        locale:'es'
    });

// $('#tablaProfesion').load('vistas/mantenedores/profesion/tablaProfesion.php');

$(function () {
  $.validator.setDefaults({
    submitHandler: function () {
      fnReporteGeoreferenciacion();
    }
  });
  $('#frmReporteGeoreferenciacion').validate({
    rules: {
      fecha1Geo: {
        required: true
      },
      fecha2Geo: {
        required: true
      }
    },
   messages: {
    fecha1Geo: {
      required: "Dato Obligatorio"
    },
    fecha2Geo: {
      required: "Dato Obligatorio"
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

function fnReporteGeoreferenciacion(){
    fecha1Geo = $('#fecha1Geo').val();
    fecha2Geo = $('#fecha2Geo').val();
   
    cadena = 'fecha1Geo=' + fecha1Geo +
             '&fecha2Geo=' + fecha2Geo;
    $.ajax({
        type:"post",
        data:cadena,
        url:'vistas/reportes/georeferenciacion/tablaReporteGeoreferenciacion.php',
        success:function(r){
                // swal("Genial!", "Profesion creada correctamente", "success");
                $('#dvTablaReporteGeoreferenciacion').html(r);
        }
    });
}


</script>
