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
if ($stmt = $conn->prepare('SELECT USUARIO, PASS, ID, TIPO FROM login WHERE USUARIO = ?'))
{
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	$stmt->store_result();
     
     // SI EL USUARIO EXISTE EN LA TABLA SE EXTRAE Y SE APUNTA SU DNI Y SU CLAVE
     if ($stmt->num_rows > 0)
      {
		$stmt->bind_result($dni, $clave, $idUsuario, $tipoUsuario);
		$stmt->fetch();
        	//if que controla si es la contraseña por defecto para que pida que la personalice
        	if ($_POST['password'] == 'trackGes') {
        		echo 'inicial';
        	}else{
			// AHORA VERIFICA SI LA CLAVE QUE SE EXTRAJO DE LA TABLA ES IGUAL A LA QUE SE ENVIA DESDE EL FORMULARIO         
        	//if ($_POST['password'] === $clave) 
          	if(password_verify( $_POST['password'],$clave))
        		{
                    // SI COINICIDEN AMBAS CONTRASEÑAS SE INICIA LA SESION Y SE LE DA LA BIENCENIDA AL USUARIO CON ECHO
					session_regenerate_id();
					$_SESSION['loggedin'] = TRUE;
					$_SESSION['name'] = $_POST['username'];
					$_SESSION['dni'] = $dni;
					$_SESSION['idUsuario'] = $idUsuario;
					$_SESSION['tipoUsuario'] = $tipoUsuario;
			        echo 3;
                    // header('Location: ../principal.php');

                   
				} 
           
       				// SI EL USUARIO EXISTE PERO EL PASSWORD NO COINCIDE IMPRIMIR EN PANTALLA PASSWORD INCORRECTO
       
                   		else { echo 0; }
       	}  
      }
      
      			   // SI EL USUARIO NO EXISTE MOSTRAR USUARIO INCORRECTO
          				else { echo 1; }

	$stmt->close();
}
?>