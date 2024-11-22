<?php 
//Connection statement
require_once '../../Connections/oirs.php';

//Aditional Functions
require_once '../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../index.php');
exit; }

$codRutPac = $_REQUEST['rutPaciente'];

$query_qrPrevision = "SELECT * FROM $MM_oirs_DATABASE.prevision";
$qrPrevision = $oirs->SelectLimit($query_qrPrevision) or die($oirs->ErrorMsg());
$totalRows_qrPrevision = $qrPrevision->RecordCount();

$query_qrConvenio= "SELECT * FROM $MM_oirs_DATABASE.convenio order by DESC_CONVENIO asc";
$qrConvenio = $oirs->SelectLimit($query_qrConvenio) or die($oirs->ErrorMsg());
$totalRows_qrConvenio = $qrConvenio->RecordCount();

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia order by DESC_TIPO_PATOLOGIA asc";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia order by DESC_PATOLOGIA asc";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$query_qrEtapaPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia order by DESC_ETAPA_PATOLOGIA asc";
$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

$query_qrCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia order by DESC_CANASTA_PATOLOGIA asc";
$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

//card seleccione la fecha derivacion selct asignar Gestora
$query_qrAsignarEnfermeria= "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO='3' order by NOMBRE asc";
$qrAsignarEnfermeria = $oirs->SelectLimit($query_qrAsignarEnfermeria) or die($oirs->ErrorMsg());
$totalRows_qrAsignarEnfermeria = $qrAsignarEnfermeria->RecordCount();

$query_qrAsignarAdministrativa= "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO='4' order by NOMBRE asc";
$qrAsignarAdministrativa = $oirs->SelectLimit($query_qrAsignarAdministrativa) or die($oirs->ErrorMsg());
$totalRows_qrAsignarAdministrativa = $qrAsignarAdministrativa->RecordCount();


?>
<form id="formDerivacion">
  <div class="card card-info">
        <div class="card-header">
          <h3 class="card-title">Busqueda de paciente para crear derivación</h3>
          <div class="card-tools">
            <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="card-body col-sm-12">
    <!-- <form class="form-horizontal" method="POST" action="addEvent.php"> -->
      <div class="row">
        <div class="input-group mb-3 col-sm-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rut paciente</span>
            </div>
            <input type='text' class="form-control input-sm" name="buscaPaciente" id="buscaPaciente" value="<?php echo $codRutPac ?>"/>
            <input type='hidden' class="form-control input-sm" name="idPaciente" id="idPaciente"/>
        </div>
        <div class="input-group mb-3 col-sm-3">
            <button class="btn btn-info" type="button" id="btnBuscaPacienteParaAgendar" onclick="fnBuscaPaciente()"><i class="fas fa-search"></i> Iniciar derivación</button>
        </div>
        <div class="col-sm-5"></div>
        <div  class="row col-sm-12"  id="dvDatosPersonales" > 
          <div class="input-group mb-3 col-sm-4">
              <div class="input-group-prepend">
                <span class="input-group-text">Nombre paciente</span>
              </div>
              <input type='text' class="form-control input-sm" name="nombrePac" id="nombrePac"/>
          </div>
          <div class="input-group mb-3 col-sm-4">
              <div class="input-group-prepend">
                <span class="input-group-text">Comuna</span>
              </div>
              <input type='hidden' class="form-control input-sm" name="idComuna" id="idComuna"/>
              <input type='text' class="form-control input-sm" name="comuna" id="comuna"/>
          </div>
          <div class="input-group mb-3 col-sm-4">
              <div class="input-group-prepend">
                <span class="input-group-text">Dirección</span>
              </div>
              <input type='text' class="form-control input-sm" name="direccion" id="direccion"/>
          </div>
        </div> 
       <br>
  </div>
  </div>
  </div>


  <!--  -->
  <div class="card card-info" id="dvPanelDatosDerivacion" >
      <div class="card-header">
        <h3 class="card-title">Seleccione los datos referente a su derivación</h3>
        <div class="card-tools">
          <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <div class="card-body col-sm-12">
        <div class="row">
          <div class="input-group mb-3 col-sm-4">
              <div class="input-group-prepend">
                <span class="input-group-text">Convenio</span>
              </div>
              <select name="slConvenioDerivacion" id="slConvenioDerivacion" class="form-control input-sm">
                  <option value="">Seleccione...</option>
                  <?php while (!$qrConvenio->EOF) {?>
                    <option value="<?php echo $qrConvenio->Fields('ID_CONVENIO') ?>"><?php echo $qrConvenio->Fields('DESC_CONVENIO') ?></option>
                  <?php $qrConvenio->MoveNext(); } ?>
              </select>
          </div>

          <div class="input-group mb-3 col-sm-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Tipo patología</span>
              </div>
              <select name="slTipoPatologiaDerivacion" id="slTipoPatologiaDerivacion" class="form-control input-sm" onchange="fnFiltraPatologias()">
                  <option value="">Seleccione...</option>
                   <?php 
                   while (!$qrTipoPatologia->EOF) {?>
                     <option value="<?php echo $qrTipoPatologia->Fields('ID_TIPO_PATOLOGIA'); ?>"><?php echo $qrTipoPatologia->Fields('DESC_TIPO_PATOLOGIA'); ?></option>
                  <?php $qrTipoPatologia->MoveNext(); } ?> 
              </select>
          </div>

          <div class="input-group mb-3 col-sm-5">
              <div class="input-group-prepend">
                <span class="input-group-text">Patología</span>
              </div>
              <select name="slPatologiaDerivacion" id="slPatologiaDerivacion" class="form-control input-sm select2bs4" onchange="fnFiltraEtapasPatologias()">
                  
              </select>
              
          </div>

          <div class="input-group mb-3 col-sm-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Etapa patología</span>
              </div>
              <select name="slEtapaPatologiaDerivacion" id="slEtapaPatologiaDerivacion" class="form-control input-sm" onchange="fnFiltraCanastasPatologias()">
              </select>
          </div>

          <div class="input-group mb-3 col-sm-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Canasta patología</span> 
              </div>
              <select name="slCanastaPatologiaDerivacion" id="slCanastaPatologiaDerivacion" class="form-control input-sm" onchange="fnExtraeTiempoLimite(this.value)">
              </select>
              <input type="hidden" id="hdTiempoLimite">
          </div>

          <div class="input-group mb-3 col-sm-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Fecha Activación</span>
            </div>
            <input type='date' class="form-control input-sm" name="fechaActivacion" id="fechaActivacion" onblur="calculaFinFecha()" />
          </div>

          <div class="input-group mb-3 col-sm-2">
            <div class="input-group-prepend">
              <span class="input-group-text">Fecha límite</span>
            </div>
            <input type='text' class="form-control input-sm" name="fechaFinGarantia" id="fechaFinGarantia" readonly />
          </div>

          <div class="input-group mb-3 col-sm-2">
              <div class="input-group-prepend">
                <span class="input-group-text">Folio</span>
              </div>
              <input type='text' class="form-control input-sm" name="folio" id="folio"/>
          </div>

          <div class="input-group mb-3 col-sm-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Monto Inicial $</span>
              </div>
              <input type='text' class="form-control input-sm" name="montoInicial" id="montoInicial"/>
          </div>

          <div class="input-group mb-3 col-sm-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Estado RN</span>
              </div>
              <select name="estadoRn" id="estadoRn" class="form-control input-sm">
                  <option value="">Seleccione...</option>
                  <option value="Prestador Asignado">Prestador Asignado</option>
                  <option value="Derivación Aceptada">Derivación Aceptada</option>
                  <option value="Solicita autorización">Solicita autorización</option>
                  <option value="Autorizado para pago">Autorizado para pago</option>
                   
              </select>
          </div>
          
          <div class="col-sm-4">
            <div class="form-group">
              <button class="btn btn-info btn-block" type="button" onClick="fnSeleccionaFechaDerivacion()"><i class="fas fa-calendar-plus"></i> Confirmar datos</button>
            </div>
          </div>
        </div>  
    </div>
  </div>

  <!--  -->
        <div class="card card-info" id="dvFechasDerivacion">
              <div class="card-header">
                <h3 class="card-title">Seleccione la fecha de derivación</h3>
                <div class="card-tools">
                  <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body col-sm-12">
          <br>
          <div class="row">
          
          <div class="input-group mb-3 col-sm-6">
            <div class="input-group-prepend">
              <span class="input-group-text">Fecha derivación</span>
            </div>
            <input type='date' class="form-control input-sm" name="fechaDerivacion" id="fechaDerivacion"/>
          </div>

          <div class="input-group mb-3 col-sm-6">
              <div class="input-group-prepend">
                <span class="input-group-text">Asignar gestora</span>
              </div>
              <select name="slAsignarEnfermeriaDerivacion" id="slAsignarEnfermeriaDerivacion" class="form-control input-sm">
                  <option value="">Seleccione...</option>
                  <?php while (!$qrAsignarEnfermeria->EOF) {?>
                    <option value="<?php echo $qrAsignarEnfermeria->Fields('ID') ?>"><?php echo $qrAsignarEnfermeria->Fields('NOMBRE') ?></option>
                  <?php $qrAsignarEnfermeria->MoveNext(); } ?>
              </select>
          </div>

         <!--  <div class="input-group mb-3 col-sm-4">
              <div class="input-group-prepend">
                <span class="input-group-text">Asignar administrativa</span>
              </div>
              <select name="slAsignarAdministrativaDerivacion" id="slAsignarAdministrativaDerivacion" class="form-control input-sm">
                  <option value="">Seleccione...</option>
                  <?php while (!$qrAsignarAdministrativa->EOF) {?>
                    <option value="<?php echo $qrAsignarAdministrativa->Fields('ID') ?>"><?php echo $qrAsignarAdministrativa->Fields('NOMBRE') ?></option>
                  <?php $qrAsignarAdministrativa->MoveNext(); } ?>
              </select>
          </div> -->

          <div class="input-group mb-3 col-sm-12">
            <div class="input-group-prepend">
              <span class="input-group-text">Comentario</span>
            </div>
            <textarea id="comentarioDerivacion" name="comentarioDerivacion" class="form-control input-sm" rows="1"></textarea>
          </div>
              
          <div class="input-group mb-3 col-sm-4">
              <button id="btnCrearDerivacion" class="btn btn-success"><i class="fas fa-procedures"></i> Crear Derivación</button>
          </div>
        </div>
    </div>
  </div>
</form>

<script>

  $("#dvDatosPersonales").hide();
  $("#dvPanelDatosDerivacion").hide();
  $("#dvFechasDerivacion").hide();
  $("#btnGuardaPac").hide();
  $('#nombrePac').prop('disabled', true);
  $('#direccion').prop('disabled', true); 
  $('#comuna').prop('disabled', true);

      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      })

  // pone fecha actual en input de fechaDerivacion
  var fecha = new Date();
  document.getElementById("fechaDerivacion").value = fecha.toJSON().slice(0,10);

  // pone fecha actual en input de fechaActivacion
  var fecha = new Date();
  document.getElementById("fechaActivacion").value = fecha.toJSON().slice(0,10);

  function fnExtraeTiempoLimite(canasta){
    cadena = 'canasta='+ canasta;
    
      $.ajax({
       url: 'vistas/derivacion/php/extraeTiempoLimite.php',
       type: "POST",
       data: cadena,
       success: function(r) {
          $('#hdTiempoLimite').val(r);
          calculaFinFecha();
         }
      });
  }

  $("#buscaPaciente").rut({formatOn: 'keyup'}).on('rutInvalido', function(e, rut, dv) {
    // swal("Oops!", "El rut " + $(this).val() + " es inválido", "warning");
    swal("Oops!", "Este no es un rut valido, corrija y vuelva a intentarlo", "warning");
    $("#buscaPaciente").val('');
    });


  // $("#buscaPaciente").rut({formatOn: 'keyup'}).on('rutValido', function(e, rut, dv) {  
    function fnBuscaPaciente(){ 

        rutPac=$("#buscaPaciente").val();
        if (rutPac == '') {
          swal("Oops!", "Debe ingresar un rut a la busqueda", "warning"); 
        }else{
          $.ajax({
              url:'vistas/derivacion/php/buscaRut.php',
              type:'POST',
              // dataType:'json',
              data:{ nomPac:$('#buscaPaciente').val()}
          }).done(function(resultado){
              var result=resultado.split('!');
                nombre =result[1];
                rut =result[0];
                direccion =result[2];
                comuna =result[3];
                idComuna =result[4];
                idPaciente =result[5];

              if(resultado =='!!!!!'){//si no encuentra paciente en bd
                Swal.fire({
                  title: 'Paciente no existe en los registros, ¿Desea Crear Paciente?',
                  text: "Para crear la derivación debe crear al paciente primero",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Si, Crear!'
                }).then((result) => {
                  if (result.isConfirmed) {
                    $('#contenido_principal').load('vistas/mantenedores/pacientes/frmCreaPaciente.php?rutPac=' + rutPac);
                    
                  }
                })

                $('#nombrePac').val('');
                $("#dvPanelDatosDerivacion").hide();
              }else{//si encuentra paciente
                $("#dvDatosPersonales").show();
                $('#nombrePac').val(nombre);
                $('#direccion').val(direccion);
                $('#comuna').val(comuna);   
                $('#idComuna').val(idComuna);   
                $('#idPaciente').val(idPaciente);   
                $("#dvPanelDatosDerivacion").show(); 
              }
          });
        }//fin if
}
    // });

//folio
//montoInicial

  function fnSeleccionaFechaDerivacion(){
    slConvenioDerivacion = $("#slConvenioDerivacion").val();
    slTipoPatologiaDerivacion = $("#slTipoPatologiaDerivacion").val();
    slPatologiaDerivacion = $("#slPatologiaDerivacion").val();
    slEtapaPatologiaDerivacion = $("#slEtapaPatologiaDerivacion").val();
    slCanastaPatologiaDerivacion = $("#slCanastaPatologiaDerivacion").val();
    folio = $("#folio").val();
    montoInicial = $("#montoInicial").val();

    if (slConvenioDerivacion == '' || slTipoPatologiaDerivacion == '' || slPatologiaDerivacion == '' || slEtapaPatologiaDerivacion == '' || slCanastaPatologiaDerivacion == '') {
      swal("Oops!", "Seleccione todos los datos para continuar", "warning");
    }else{
      $("#dvFechasDerivacion").show();
    }
  }

 
  function fnFiltraPatologias(){
      $("#slTipoPatologiaDerivacion option:selected").each(function () {
          tipoPatologia=$(this).val();
          if (tipoPatologia == '2') {
            $("#slEtapaPatologiaDerivacion").val('0');
            $("#slCanastaPatologiaDerivacion").val('0');
          }
            $.post("vistas/derivacion/php/filtraPatologias.php",
            { tipoPatologia: tipoPatologia },
              function(data){
              $("#slPatologiaDerivacion").html(data);
             
            });
      }); 
  }
  function fnFiltraEtapasPatologias(){
      
      $("#slPatologiaDerivacion option:selected").each(function () { 
          decreto = 'LEP2225';     
          patologia=$(this).val();
          fnBuscaDerivacionExistente(patologia);//verifica si existe la derivacion con esa misma patologia y de ese paciente
          tipoPatologia = $("#slTipoPatologiaDerivacion").val();
          if (tipoPatologia == '2') {
            $("#slEtapaPatologiaDerivacion").val('0');
            $("#slCanastaPatologiaDerivacion").val('0');
          }else{
            $.post("vistas/derivacion/php/filtraEtapasPatologias.php",
            { patologia: patologia, decreto },
              function(data){
                  $("#slEtapaPatologiaDerivacion").html(data);
            });
          }
      });
  }
  function fnFiltraCanastasPatologias(){
      decreto = 'LEP2225';
      $("#slEtapaPatologiaDerivacion option:selected").each(function () {
          etapaPatologia=$(this).val();
          $.post("vistas/derivacion/php/filtraCanastasPatologias.php",
          { etapaPatologia: etapaPatologia, decreto },
            function(data){
            $("#slCanastaPatologiaDerivacion").html(data);
          });
      });
  }

  function fnBuscaDerivacionExistente(patologia){
    idPaciente = $('#idPaciente').val();
    cadena = 'idPaciente='+idPaciente+
             '&patologia='+patologia;

    $.ajax({
     url: 'vistas/derivacion/php/buscaDerivacionExistente.php',
     type: "POST",
     data: cadena,
     success: function(r) {
        if (r != 0) {
          Swal.fire({
            icon: 'error',
            title: 'Atención...',
            text: 'Paciente tiene derivación número '+ r + ' con la misma patología seleccionada!',
          })
        }
       }
    });

  }

$('#datetimepickerHoraAgendar').datetimepicker({
    format: 'LT'       
});

$('#datetimepickerFechaAgendar').datetimepicker({
    format: 'YYYY-MM-DD'       
});

calculaFinFecha = function(){
    fecha = $("#fechaActivacion").val();
    d = $("#hdTiempoLimite").val();
    if (d=='') {
      $('#fechaFinGarantia').val('Sin Limite');
    }else{
      var info = fecha.split('-');
      fecha=  info[2] + '-' + info[1] + '-' + info[0];
      var Fecha = new Date();
      var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
      var sep = sFecha.indexOf('/') != -1 ? '/' : '-';
      var aFecha = sFecha.split(sep);
      var fecha = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
      fecha= new Date(fecha);
      fecha.setDate(fecha.getDate()+parseInt(d));
      var anno=fecha.getFullYear();
      var mes= fecha.getMonth()+1;
      var dia= fecha.getDate();
      mes = (mes < 10) ? ("0" + mes) : mes;
      dia = (dia < 10) ? ("0" + dia) : dia;
      var fechaFinal = dia+sep+mes+sep+anno;
      $('#fechaFinGarantia').val(fechaFinal); 
      return (fechaFinal);         
    }
}



  $(function () {
    $.validator.setDefaults({
      submitHandler: function () {
        fnGuardarDerivacion();
      }
    });
    $('#formDerivacion').validate({
      rules: {
        buscaPaciente: {
          required: true
        },
        slConvenioDerivacion: {
          required: true
        },
        slTipoPatologiaDerivacion: {
          required: true
        },
        slPatologiaDerivacion: {
          required: true,
        },
        // slEtapaPatologiaDerivacion: {
        //   required: true,
        // },
        // slCanastaPatologiaDerivacion: {
        //   required: true
        // },
        fechaDerivacion: {
          required: true
        },
        slAsignarEnfermeriaDerivacion: {
          required: true
        },
	 	   folio: {
           required: true
       	},
       	montoInicial: {
          required: true
       	},
        estadoRn: {
          required: true
        }
      },

      messages: {
        buscaPaciente: {
          required: "Dato Obligatorio"
        },
        slConvenioDerivacion: {
          required: "Dato Obligatorio"
        },
        slTipoPatologiaDerivacion: {
          required: "Dato Obligatorio"
        },
        slPatologiaDerivacion: {
          required: "Dato Obligatorio",
        },
        // slEtapaPatologiaDerivacion: {
        //   required: "Dato Obligatorio",
        // },
        // slCanastaPatologiaDerivacion: {
        //   required: "Dato Obligatorio"
        // },
        fechaDerivacion: {
          required: "Dato Obligatorio"
        },
        slAsignarEnfermeriaDerivacion: {
          required: "Dato Obligatorio"
        },
        folio: {
          required: "Ingrese Folio de derivación"
        },
        montoInicial: {
          required: "Ingrese monto inicial de la derivación"
        },
         estadoRn: {
          required: "Seleccione el estado de la derivación"
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

  function fnGuardarDerivacion(){
    cadena = $('#formDerivacion').serialize();
   
      $.ajax({
       url: 'vistas/derivacion/php/guardarDerivacion.php',
       type: "POST",
       data: cadena,
       success: function() {
          swal("Todo bien!", "Derivación Creada Con exito", "success");        
          $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php');
         }
      });
  }
</script>