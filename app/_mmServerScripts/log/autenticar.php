<?php
session_start();
require_once("conectar.php");

//VERIFICACION DE ESCRITURA DE DATOS EN EL FORM
			if ( !isset($_POST['username'], $_POST['password']) )
            {
			// Could not get the data that should have been sent.
			exit('Please fill both the username and password fields!');
			}

//  SI SE CONECTO Y SI SE ENVIARON AMBOS DATOS SE PROCEDE CON LA CONSULTA DE EXISTENCIA DEL USUARIO EVITANDO INYECCIONES SQL ?
if ($stmt = $conn->prepare('SELECT dni, clave FROM usuarios WHERE usuario = ?'))
 {
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	$stmt->store_result();
     
     // SI EL USUARIO EXISTE EN LA TABLA SE EXTRAE Y SE APUNTA SU DNI Y SU CLAVE
     if ($stmt->num_rows > 0)
      {
		$stmt->bind_result($dni, $clave);
		$stmt->fetch();
        
			// AHORA VERIFICA SI LA CLAVE QUE SE EXTRAJO DE LA TABLA ES IGUAL A LA QUE SE ENVIA DESDE EL FORMULARIO         
        	//if ($_POST['password'] === $clave) 
          	if(password_verify( $_POST['password'],$clave))
        		{
                    // SI COINICIDEN AMBAS CONTRASEÑAS SE INICIA LA SESION Y SE LE DA LA BIENCENIDA AL USUARIO CON ECHO
					session_regenerate_id();
					$_SESSION['loggedin'] = TRUE;
					$_SESSION['name'] = $_POST['username'];
					$_SESSION['dni'] = $dni;
			        // echo 'BIENVENIDO USUARIOP : ' . $_SESSION['name'] .' CON TU DNI NUMERO : '. $_SESSION['dni'] . '!';
                    header('Location: perfil.php');
                   
				} 
           
       				// SI EL USUARIO EXISTE PERO EL PASSWORD NO COINCIDE IMPRIMIR EN PANTALLA PASSWORD INCORRECTO
       
                   		else { echo "  <p> </p>   <p style=text-align:center;> <img src=https://cdn141.picsart.com/292255026029211.png?type=webp&to=min&r=640 style=width:200px;height:220px;></p>
                        <p> </p>     <table border=1 cellspacing=0 cellpading=0 align=center BORDER BGCOLOR=#16DFDF>
                        <tr align=center > <td ><font color=red><h2>¡PASSWORD INCORRECTO !</h2>  <a href='exit.php' >SALIR</a>  </td>    </tr>
                        </table>"; }
       	}  
      
      
      			   // SI EL USUARIO NO EXISTE MOSTRAR USUARIO INCORRECTO
          				else { echo "  <p> </p>   <p style=text-align:center;> <img src=https://cdn141.picsart.com/292255026029211.png?type=webp&to=min&r=640 style=width:200px;height:220px;></p>
                        <p> </p>     <table border=1 cellspacing=0 cellpading=0 align=center BORDER BGCOLOR=#16DFDF>
                        <tr align=center > <td ><font color=red><h2>¡USUARIO INCORRECTO !</h2>  <a href='exit.php' >SALIR</a>  </td>    </tr>
                        </table>"; }

	$stmt->close();
}
?>