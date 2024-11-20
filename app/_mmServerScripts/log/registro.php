<HTML>
<head>
<meta charset="utf-8">
<title>Home Page</title>
</head>
<body style="background-color:hsla(0,16%,65%,0.5);">
<p> </p>
 
  
<p style="text-align:center;"> <img src="https://wi.wallpapertip.com/wsimgs/33-337633_nikola-tesla.jpg" style="width:400px;height:120px;"></p>
  
  
<h1 align="center">REGISTRO DE USUARIOS</h1>
<table border="" width="400" cellspacing="2" cellpadding="15" align="center" bgcolor="#00FF00">
<tbody>
<tr><td align="center">
<form method="post">
   
<p><strong>DNI:            
<input name="dni" placeholder="DNI"
oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
type = "number"
maxlength = "8" />
</strong></p>
    
<p><strong>USUARIO:  <input maxlength="10" name="usuario" type="text" value="" placeholder="USUARIO" /></strong></p>
<p><strong>CLAVE:       <input maxlength="8" name="password" type="password" value="" placeholder="PASSWORD" /></strong></p>
<p><button name="submit" type="submit"><strong>REGISTRAR</strong> </button></p>
</form></td>
</tr>
</tbody>
</table>
<table border="2" width="400" cellspacing="2" cellpadding="15" align="center">
<tbody>
<tr bgcolor="#FFD700">
<td align="center"><a href="exit.php">SALIR</a></td>
</tr>
</tbody>
</table>
</HTML>


<?php

require_once("conectar.php");

if ($stmt = $conn->prepare("INSERT INTO usuarios (dni, usuario, clave) VALUES (?, ?, ?)"))
{
      $dni = $_POST['dni'];
      $usuario = $_POST['usuario'];
	    $password = $_POST['password'];
	    $options = array("cost"=>4);
	    $hashPassword = password_hash($password,PASSWORD_BCRYPT,$options);
            
      $stmt->bind_param("iss", $dni, $usuario, $hashPassword);
	    $stmt->execute();
  
 if (!$stmt->error){
   
         echo "<table border=1 cellspacing=0 cellpading=0 align=center BORDER BGCOLOR=#141318>
         <p><tr align=center > <td><font color=yellow ><div style=font-size:1.25em color:yellow> USUARIO REGISTRADO CON EXITO </div></td></tr></p>
              </table>"; 
                   }
}

?>
