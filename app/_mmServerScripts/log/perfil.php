<?php
// Solo se permite el ingreso con el inicio de sesion.
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
	header('Location: login.html');
	exit; }
?>


<html>
<head>
<meta charset="utf-8">
<title>Home Page</title>
</head>

<body style="background-color:hsla(0,16%,65%,0.5);">
<p style="text-align:center;"> <img src="https://www.groovylabinabox.com/wp-content/uploads/2015/06/FB_tesla.jpg" style="width:400px;height:120px;"></p>
<table BORDER BGCOLOR="#00FF00" border="2" cellpadding="15" cellspacing="2" width="400" align="center">

<tr><td align="center">
<a><h2> INGRESÓ A LA BASE DE DATOS DE TECNOCIENCIA PERÚ</h2></a>
</td></tr>
</tr>
</table>

  
<table border="2" cellpadding="15" cellspacing="2" width="400" align="center">
<tr BORDER BGCOLOR="#F07F54"><td align="center">
<h2> Bienvenido Usuario : <?=$_SESSION['name']?> <p> Dni : <?=$_SESSION['dni']?>  </p> </h2></td>
<tr BORDER BGCOLOR="#00fff0"><td align="center">
<a href='exit.php'>SALIR</a>
</td></tr>
</tr>
</table>


</body>
</html>