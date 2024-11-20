<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$query_qrRegion = "SELECT * FROM $MM_oirs_DATABASE.regiones";
$qrRegion = $oirs->SelectLimit($query_qrRegion) or die($oirs->ErrorMsg());
$totalRows_qrRegion = $qrRegion->RecordCount();

$query_qrProfesion= "SELECT * FROM $MM_oirs_DATABASE.profesion ORDER BY PROFESION ASC";
$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
$totalRows_qrProfesion = $qrProfesion->RecordCount();

?>

<form id="frmCreaProfesional">
  <div class="card card-info">
    
    <div class="card-header">
      <h3 class="card-title">Datos del profesional</h3>
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
                  <span class="input-group-text">Rut Profesional</span>
                </div>
                <input type='text' class="form-control input-sm" name="rutProfesional" id="rutProfesional"  onchange="verificaRutPrestador()"/>
              </div>
              <div class="input-group mb-3 col-sm-8">
                <div class="input-group-prepend">
                  <span class="input-group-text">Nombre Profesional</span>
                </div>
                <input type='text' class="form-control input-sm" name="nombreProfesional" id="nombreProfesional"/>
              </div>
              <div class="input-group mb-3 col-sm-4">
                <div class="input-group-prepend">
                  <span class="input-group-text">Tipo Profesional</span>
                </div>
                <select name="tipoProfesional" id="tipoProfesional" class="form-control input-sm">
                    <option value="">Seleccione...</option>
                     <?php 
                     while (!$qrProfesion->EOF) {?>
                       <option value="<?php echo $qrProfesion->Fields('ID'); ?>"><?php echo utf8_encode($qrProfesion->Fields('PROFESION')); ?></option>
                    <?php $qrProfesion->MoveNext(); } ?>
                </select>
              </div>
              <div class="input-group mb-3 col-sm-4">
                <div class="input-group-prepend">
                  <span class="input-group-text">Telefono</span>
                </div>
                <input type='text' class="form-control input-sm" name="fonoProfesional" id="fonoProfesional"/>
              </div>
              <div class="input-group mb-3 col-sm-4">
                <div class="input-group-prepend">
                  <span class="input-group-text">Correo Electrónico</span>
                </div>
                <input type='text' class="form-control input-sm" name="mailProfesional" id="mailProfesional"/>
              </div>
          </div>
      </div>
      <div class="card-footer">
            <button type="submit" class="btn btn-info">Guardar Datos Profesional</button>
      </div>
  </div> 
</form>
<br>
   <div id="tablaProfesionales"></div>

<script type="text/javascript">
$('#tablaProfesionales').load('vistas/mantenedores/profesionales/tablaProfesionales.php');

$("#rutProfesional").rut({formatOn: 'keyup'}).on('rutInvalido', function(e) {
    swal("Oops!", "El rut " + $(this).val() + " es inválido", "warning");
    $("#rutProfesional").val('');
});

$(function () {
  $.validator.setDefaults({
    submitHandler: function () {
      fnGuardaCreaProfesional();
    }
  });
  $('#frmCreaProfesional').validate({
    rules: {
      rutProfesional: {
        required: true
      },
      nombreProfesional: {
        required: true
      },
      tipoProfesional: {
        required: true
      },
      fonoProfesional: {
        required: true
      },
      mailProfesional: {
        required: true,
        email:true
      }
    },
   messages: {
    rutProfesional: {
      required: "Ingrese rut del profesional"
    },
    nombreProfesional: {
      required: "Ingrese nombre del profesional"
    },
    tipoProfesional: {
      required: "Seleccione tipo de profesional"
    },
    fonoProfesional: {
      required: "Seleccione tipo de profesional"
    },
    mailProfesional: {
      required: "Seleccione mail de profesional",
      email: "Formato Correo Incorrecto"
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

function fnGuardaCreaProfesional(){
    rutProfesional = $('#rutProfesional').val();
    nombreProfesional = $('#nombreProfesional').val();
    tipoProfesional = $('#tipoProfesional').val();
    fonoProfesional = $('#fonoProfesional').val();
    mailProfesional = $('#mailProfesional').val();

    cadena = 'rutProfesional=' + rutProfesional +
             '&nombreProfesional=' + nombreProfesional +
             '&tipoProfesional=' + tipoProfesional +
             '&fonoProfesional=' + fonoProfesional +
             '&mailProfesional=' + mailProfesional;
            
    $.ajax({
        type:"post",
        data:cadena,
        url:'vistas/mantenedores/profesionales/guardaCreaProfesionales.php',
        success:function(r){
            if (r == 1) {
                swal("Genial!", "Profesional creado correctamente", "success");
                $('#tablaProfesionales').load('vistas/mantenedores/profesionales/tablaProfesionales.php');
                $('#rutProfesional').val('');
                $('#nombreProfesional').val('');
                $('#tipoProfesional').val('');
                $('#fonoProfesional').val('');
                $('#mailProfesional').val('');
              
            } else {
            }
            
        }
    });
}


function verificaRutPrestador(){ 
  rutProfesional=$('#rutProfesional').val();

  cadena = 'rutProfesional=' + rutProfesional;


  $.ajax({
    type:"post",
    data:cadena,
    url:'vistas/mantenedores/profesionales/verificaRutProfesional.php',
    success:function(r){
      if (r == 1) {

       swal("Oops!", "¡Rut del profesional existe!", "warning"); 

        // $('#dvTablaPrestador').load('vistas/mantenedores/prestadores/tablaPrestadores.php');
            $('#rutProfesional').val('');
        
    } else {
    }
       
    }
  });
}

</script>
