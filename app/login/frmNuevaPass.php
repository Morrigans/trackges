<?php 
require_once '../Connections/oirs.php';
require_once '../includes/functions.inc.php';
//require_once '../head/headers.php';

//header("Content-Type: text/html;charset=utf-8");
$usuario= $_REQUEST['usuario'];

?>
<form id="frmCambiaPassLogin">
  <div class="card card-info">
    <div class="card-header">
      <h3 class="card-title">Complete la información solicitada</h3>
    </div>

    <div class="card-body col-sm-12">          
        <div class="row">

          <input type='hidden' class="form-control input-sm" name="usuarioSesion" id="usuarioSesion" value="<?php echo $usuario ?>" />
          <br>
          <!-- <div class="input-group mb-3 col-sm-12">
              <div class="input-group-prepend"><span class="input-group-text">Nueva contraseña</span></div>
              <input type='password' class="form-control input-sm" name="nuevaPass" id="nuevaPass"/>
          </div>
          <br>
          <div class="input-group mb-3 col-sm-12">
              <div class="input-group-prepend"><span class="input-group-text">Confirmar contraseña</span></div>
              <input type='password' class="form-control input-sm" name="confirmaPass" id="confirmaPass"/>
          </div> -->


          <!-- INICIO MODIFICA PASSWORD -->
          <div class="input-group mb-3 col-sm-6">
              <div class="input-group-prepend">
                <span id="spPassword3" class="input-group-text">Nueva contraseña</span>
              </div>
              <input type="password" class="form-control input-sm" name="nuevaPass" id="nuevaPass"/>
              <br>
              <span id="msjNuevaPass3"></span>
              <!-- REGLAS DE VALIDACION DE CONTRASEÑA -->
              <div class="input-group mb-3 col-sm-12">
                  <ul>
                      <li id="mayus3">1 Mayúscula mínimo</li>
                      <li id="tamanio3">6 Caractéres mínimo</li>
                  </ul>
              </div>
          </div>

          <div class="input-group mb-3 col-sm-6">
              <div class="input-group-prepend">
                <span id="spPassword4" class="input-group-text">Confirmar contraseña</span>
              </div>
              <input type="password" class="form-control input-sm" name="confirmaPass" id="confirmaPass"/>
              <br>
              <!-- REGLAS DE VALIDACION DE CONTRASEÑA -->
              <span id="msjNuevaPass4"></span>
              <div class="input-group mb-3 col-sm-12">
                  <ul>
                      <li id="mayus4">1 Mayúscula mínimo</li>
                      <li id="tamanio4">6 Caractéres mínimo</li>
                  </ul>
              </div>
          </div>
          <!-- FIN MODIFICA PASSWORD -->

        </div>
    </div>
    <div class="row col-sm-12">
      <div align="right" class="card-footer col-sm-4"><button type="submit" class="btn btn-success col-sm-12">Guardar cambios</button></div>
      <div align="right" class="card-footer col-sm-4"></div>
      <div align="right" class="card-footer col-sm-4"><button type="button" class="btn btn-default col-sm-12" data-dismiss="modal">Cerrar</button></div>
    </div>
  </div>
</form>

<script type="text/javascript">

  $(function () {
    $.validator.setDefaults({
      submitHandler: function () {
        //llamo la funcion que inserta
        fnCambiaPassLogin();        
        $('#modalCambiaPass').modal('hide');
      }
    });

    $('#frmCambiaPassLogin').validate({
      rules: {
        nuevaPass: {
          required: true
        },
        confirmaPass: {
          required: true
        }
      },
      messages: {
        nuevaPass: {
          required: "Dato Obligatorio"
        },
         confirmaPass: {
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


  //VALIDACION CONTRASEÑA
  $(document).ready(function(){

    var mayus= new RegExp("^(?=.*[A-Z])");
    var tamanio= new RegExp("^(?=.{6,})");

    var regExp= [mayus,tamanio];
    var regExp2= [mayus,tamanio];
    var elementos= [$("#mayus3"),$("#tamanio3")];
    var elementos2= [$("#mayus4"),$("#tamanio4")];

    $("#nuevaPass").on("keyup", function(){
      var pass= $("#nuevaPass").val();
      var check= 0;
      // alert(pass.length);
      for(var i = 0; i < 2; i++){
        if(regExp[i].test(pass)){
          elementos[i].hide();
          check++;  
        }else{
          elementos[i].show();
        }
        if(check == 2){
          $("#spPassword3").last().addClass("alert-success");
          $("#msjPass3").text("Segura").css("color", "green");
          $("#nuevaPass").css("border","2px solid green");
        }
        if(check != 2){
          $("#msjPass3").text("Insegura").css("color", "red");
          $("#nuevaPass").css("border","2px solid red");
        }
      }
    });

    $("#confirmaPass").on("keyup", function(){
      var pass1= $("#nuevaPass").val();
      var pass2= $("#confirmaPass").val();
      var check2= 0;

      //alert(pass.length);
      for(var j = 0; j < 2; j++){
        if(regExp2[j].test(pass2)){
          elementos2[j].hide();
          check2++;
        }else{
          elementos2[j].show();
        }
      }

      if(check2 == 2){
        $("#spPassword4").last().addClass("alert-success");
        $("#msjPass4").text("Segura").css("color", "green");
        $("#confirmaPass").css("border","2px solid green");       
      }
      if(check2 != 2){
        $("#msjPass4").text("Insegura").css("color", "red");
        $("#confirmaPass").css("border","2px solid red");
      }

      // if(check2 >= 0 && check2 <= 2){
      //  //$("#msjPass2").text("Muy insegura").css("color", "red");
      //  $("#confirmaPassEd").css("border","2px solid red");
      // }else if(check2 >= 3 && check2 <= 5){
      //  //$("#msjPass2").text("Poco segura").css("color", "orange");
      //  $("#confirmaPassEd").css("border","2px solid orange");
        
      // }else if(check2 == 6){
      //  //$("#msjPass2").text("segura").css("color", "green");
      //  //$("#msjPass").html("<p class='text-success'>SeguraXXX</p>");
      //  $("#confirmaPassEd").css("border","2px solid green");
      // }      
    });

  });
  // FIN VALIDACION CONTRASEÑA

function fnCambiaPassLogin(){

    usuarioSesion = $('#usuarioSesion').val();
    nuevaPass = $('#nuevaPass').val();
    confirmaPass = $('#confirmaPass').val();

    if (nuevaPass != confirmaPass) {
        swal("Oops!", "las contraseñas no coinciden", "warning");
        $('#confirmaPass').val('');
        $('#nuevaPass').val('');
    }else{
        ncaracter=nuevaPass.length;
        if (ncaracter < 6) {
            swal("Oops!", "la contraseña tiene menos de 6 caracteres", "warning");
        }else{

          cadena = 'usuarioSesion='+ usuarioSesion +
                  '&nuevaPass='+ nuevaPass +
                  '&confirmaPass='+ confirmaPass;

          $.ajax({
              url: 'login/actualizaPw.php',
              type: 'POST',
              data: cadena,
              success: function(r) {
                if (r==1) {
                  Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'La actualización se realizó con éxito, por favor ahora inicie sesión',
                    showConfirmButton: false,
                    timer: 1500
                  })
                  window.location.replace("principal.php");
                }else{
                swal("Oops!", "Algo salio mal", "warning");    
              }
            }
          });
        }
  }
}
</script>