<!DOCTYPE html>
<html>
<body>

    <div class="card card-info">
          <div class="card-header">
            <h3 class="card-title">Adjunte planilla de Right Now</h3>
            <div class="card-tools">
              <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
            <br>
            <div class="row">

              <!-- DIV FORMULARIO***************************************************************************************************** -->
              <div class="col-md-6">
                <form id="frmSubeRn" name="frmSubeRn" method="POST" enctype="multipart/form-data"/>
                  <div class="file-input text-center">
                      <input  type="file" id="datosRn" name="datosRn" id="file-input" class=""/>
                  </div>
                  <div class="text-center mt-4">
                      <input type="button" id="btnSubmit" name="btnSubmit" class="btn btn-success btn-lg" value="Subir archivo Right Now!" />
                      <div id="msjExito"></div>
                      <br><br>
                      <div align="center" id="result"></div>
                  </div>
                </form>
              </div>
              <!-- ******************************************************************************************************************* -->


              <div class="col-md-6">
                <table class="table">
                  <tr>
                    <td><strong>Casos Nuevos</strong></td>
                    <td><div id="dvCasosNuevos">0</div></td>
                  </tr>
                  <tr>
                   <td><strong>Casos Actualizados</strong></td>
                   <td><div id="dvCasosActualizados">0</div></td>
                  </tr>
                  <tr>
                    <td><strong>Casos Analizados</strong></td>
                    <td><div id="dvCasosAnalizados">0</div></td>
                  </tr>
                </table>
              </div>
            </div>
</div>


<script type="text/javascript">

  $(document).ready(function () {

      $("#btnSubmit").click(function (event) {

          //stop submit the form, we will post it manually.
          event.preventDefault();

          // Get form
          var form = $('#frmSubeRn')[0];

      // Create an FormData object 
          var data = new FormData(form);

      // If you want to add an extra field for the FormData
          data.append("CustomField", "This is some extra data, testing");

      // disabled the submit button
          $("#btnSubmit").prop("disabled", true);
          $('#result').html('<img src="images/loading.gif"/><br/>Un momento, por favor...este proceso puede tardar unos segundos <br/> No salga de esta página hasta que termine de ejecutarse');

          $.ajax({
              type: "POST",
              enctype: 'multipart/form-data',
              url: "vistas/subeRn/recibe_excel_validando.php",
              data: data,
              processData: false,
              contentType: false,
              cache: false,
              timeout: 600000,
              success: function (info) {
                  var result=info.split('!');
                   casosAnalizados =result[0];
                   casosNuevos =result[1];
                   casosActualizados =result[2];
                   cantidadCasos =result[3];

                   $('#dvCasosAnalizados').html(casosAnalizados);
                   $('#dvCasosNuevos').html(casosNuevos);
                   $('#dvCasosActualizados').html(casosActualizados);
                  $('#result').html('');
                  $("#btnSubmit").prop("disabled", false);

                  if (cantidadCasos == 1) { // vuelve 1 cuando no se adjunta nada
                    Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: 'No se evaluo ningun caso!, ¿Adjunto el archivo?',
                    })
                  }else{
                    Swal.fire({
                      icon: 'success',
                      title: 'Bien...',
                      text: casosAnalizados+' Casos evaluados!',
                    })

                    $("#datosRn").hide();
                    $("#btnSubmit").hide();
                    $("#msjExito").text('Proceso Terminado!');
                  }
                  
              },
              error: function (e) {

                  $("#result").text(e.responseText);
                  console.log("ERROR : ", e);
                  $("#btnSubmit").prop("disabled", false);

              }
          });

      });

  });

</script>

</body>
</html>