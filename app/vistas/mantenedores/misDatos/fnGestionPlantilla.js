<script>

$(function(){

	/*REGLAS DE VALIDACION DE CONTRASEÑAS */
	var mayus= new RegExp("^(?=.*[A-Z].*[A-Z].*[A-Z])");//minimo 3 letras en mayuscula de la A a la Z (ESTA REGLA SE REPITE 3 VECES)
	var special= new RegExp("^(?=.*[!@#$%&*].*[!@#$%&*].*[!@#$%&*])");//minimo 3 simbolos (ESTA REGLA SE REPITE 3 VECES)
	var numbers= new RegExp("^(?=.*[0-9])");
	var lower= new RegExp("^(?=.*[a-z])");
	var len= new RegExp("^(?=.{8,})");
	/*FIN REGLAS DE VALIDACION*/

	var regExp= [mayus,special,numbers,lower,len];
	var elementos= [$("#mayus"),$("#special"),$("#numbers"),$("#lower"),$("#len")];

	$("#password1").on("keyup", function(){//NOMBRE DEL INPUT QUE SE EVALUA
		var pass= $("#password1").val();
		for(var i=0; i<5; i++){
			if(regExp[i].test(pass)){
				elementos[i].hide();
			}else{
				elementos[i].show();
			}

		}
	})
})


</script>
