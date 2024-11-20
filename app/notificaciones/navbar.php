<?php
require_once '../Connections/oirs.php';
require_once '../includes/functions.inc.php';
// Solo se permite el ingreso con el inicio de sesion.
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');


// VERIFICA NUEVAS NOTIFICACIONES PARA EL PERFIL COORDINADOR
$query_qrNotificación = "SELECT * FROM $MM_oirs_DATABASE.notificaciones where ESTADO='nuevo' and USUARIO = '$usuario' AND (ASUNTO!='Solicita gestion' AND ASUNTO!='Solicita Informacion') order by ID DESC";
$qrNotificación = $oirs->SelectLimit($query_qrNotificación) or die($oirs->ErrorMsg());
$totalRows_qrNotificación = $qrNotificación->RecordCount();


// VERIFICA NUEVAS NOTIFICACIONES PARA EL PERFIL COORDINADOR
$query_qrNotificación2 = "SELECT * FROM $MM_oirs_DATABASE.notificaciones where ESTADO='nuevo' and USUARIO = '$usuario' AND (ASUNTO='Solicita gestion' OR ASUNTO='Solicita Informacion') order by ID DESC";
$qrNotificación2 = $oirs->SelectLimit($query_qrNotificación2) or die($oirs->ErrorMsg());
$totalRows_qrNotificación2 = $qrNotificación2->RecordCount();


// VERIFICA NUEVAS ALARMAS E HISTORIAL
$query_qrAlarmas = "SELECT * FROM $MM_oirs_DATABASE.alarmas where ESTADO='activa' AND FECHA_ALARMA <= '$hoy' and USUARIO_RECEPTOR = '$usuario'  order by ID_ALARMA DESC";
$qrAlarmas = $oirs->SelectLimit($query_qrAlarmas) or die($oirs->ErrorMsg());
$totalRows_qrAlarmas = $qrAlarmas->RecordCount();

// VERIFICA NUEVAS NOTIFICACIONES DE ICRS
$query_qrNotificaciónIcrs = "SELECT * FROM $MM_oirs_DATABASE.notificaciones_pp where ESTADO='nuevo' and USUARIO = '$usuario' order by ID DESC";
$qrNotificaciónIcrs = $oirs->SelectLimit($query_qrNotificaciónIcrs) or die($oirs->ErrorMsg());
$totalRows_qrNotificaciónIcrs = $qrNotificaciónIcrs->RecordCount();

?>

	<nav class="main-header navbar navbar-expand navbar-white navbar-light">
	  	<ul class="navbar-nav">
	    	<li class="nav-item">
	      		<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
	    	</li>
	    	<li class="nav-item d-none d-sm-inline-block"> 
	      		<a href="principal.php" class="nav-link">Inicio</a>
	    	</li>
	    	 <li class="nav-item"> 
            	<a href="home.php" class="nav-link">Home</a> 
        	</li>
	  	</ul>

	  
	  	<ul class="navbar-nav ml-auto">
	  		<li class="nav-item dropdown">
	  			<div class="row">

	  						<!-- NOTIFICACIONES ICRS -->
				  			<div class="">
					  	  		<a class="nav-link" data-toggle="dropdown" href="#" data-toggle="tooltip" data-placement="bottom" title="Notificaciones UGC">
					  	    		<i class="far fa-bell"></i>
					  	    		<?php if ($totalRows_qrNotificaciónIcrs == 0) {?>
					  	    			<span class="badge badge-warning navbar-badge"><?php echo $totalRows_qrNotificaciónIcrs ?></span>
					  	    		<?php }else{ ?>
										<span class="badge badge-primary navbar-badge"><?php echo $totalRows_qrNotificaciónIcrs ?></span>
					  	    		<?php } ?>	  	    
					  	  		</a>

						  	  	<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
						  	    	<span class="dropdown-item dropdown-header"><?php echo $totalRows_qrNotificaciónIcrs ?> Notificación(es)</span>

						  	    	<?php while (!$qrNotificaciónIcrs->EOF) {?>
						  	    	<div class="dropdown-divider"></div>
						  	    	<a href="#" class="dropdown-item" data-toggle="modal" data-target="#modalMensajeRecibidoIcrs" onclick="fnVerMensajeRecibidoIcrs(<?php echo $qrNotificaciónIcrs->Fields('ID'); ?>)">
						  	      		<i class="fas fa-envelope mr-2"></i> <?php echo utf8_encode($qrNotificaciónIcrs->Fields('ASUNTO')); ?><br>&ensp;&ensp;&ensp;<span class="text-muted text-sm"><?php echo date("d-m-Y",strtotime($qrNotificaciónIcrs->Fields('FECHA'))); ?></span><span class="float-right text-muted text-sm"><?php echo date("G:i",strtotime($qrNotificaciónIcrs->Fields('HORA'))); ?></span>
						  	    	</a>
						  	    	<?php $qrNotificaciónIcrs->MoveNext();
									}?>

						  	    	<div class="dropdown-divider"></div>
						  	    	<a href="#" class="dropdown-item dropdown-footer" data-toggle="modal" data-target="#modalVerTodoMensajesIcrs" onclick="fnVerTodoMensajesIcrs('<?php echo $usuario ?>')">Ver todas las notificaciones</a>
						  	  	</div>
					  	  	</div>
	  				<!-- ALARMAS ACTIVAS E HISTORIAL -->
			  	  		<a class="nav-link" data-toggle="dropdown" href="#" onclick="$('#modalAlarmasInicio').modal('show'); $('#dvFrmAlarmasInicio').load('vistas/alarmas/modals/frmAlarmasInicio.php');" data-toggle="tooltip" data-placement="bottom" title="Tareas programadas">
			  	    		<i class="far fa-clock"></i>
			  	    		<?php if ($totalRows_qrAlarmas == 0) {?>
			  	    			<span class="badge badge-warning navbar-badge"><?php echo $totalRows_qrAlarmas ?></span>
			  	    		<?php }else{ ?>
								<span class="badge badge-info navbar-badge"><?php echo $totalRows_qrAlarmas ?></span>
			  	    		<?php } ?>	  	    
			  	  		</a>
				  	  	
			  	  	

			  	  	<!-- NOTIFICACIONES SIN GESTION -->
		  			<div class="">
			  	  		<a class="nav-link" data-toggle="dropdown" href="#" data-toggle="tooltip" data-placement="bottom" title="Notificaciones generales">
			  	    		<i class="far fa-bell"></i>
			  	    		<?php if ($totalRows_qrNotificación == 0) {?>
			  	    			<span class="badge badge-warning navbar-badge"><?php echo $totalRows_qrNotificación ?></span>
			  	    		<?php }else{ ?>
								<span class="badge badge-danger navbar-badge"><?php echo $totalRows_qrNotificación ?></span>
			  	    		<?php } ?>	  	    
			  	  		</a>

				  	  	<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
				  	    	<span class="dropdown-item dropdown-header"><?php echo $totalRows_qrNotificación ?> Notificación(es)</span>

				  	    	<?php while (!$qrNotificación->EOF) {?>
				  	    	<div class="dropdown-divider"></div>
				  	    	<a href="#" class="dropdown-item" data-toggle="modal" data-target="#modalMensajeRecibido" onclick="fnVerMensajeRecibido(<?php echo $qrNotificación->Fields('ID'); ?>)">
				  	      		<i class="fas fa-envelope mr-2"></i> <?php echo utf8_encode($qrNotificación->Fields('ASUNTO')).' R0'.$qrNotificación->Fields('ID_DERIVACION'); ?><br>&ensp;&ensp;&ensp;<span class="text-muted text-sm"><?php echo date("d-m-Y",strtotime($qrNotificación->Fields('FECHA'))); ?></span><span class="float-right text-muted text-sm"><?php echo date("G:i",strtotime($qrNotificación->Fields('HORA'))); ?></span>
				  	    	</a>
				  	    	<?php $qrNotificación->MoveNext();
							}?>

				  	    	<div class="dropdown-divider"></div>
				  	    	<a href="#" class="dropdown-item dropdown-footer" data-toggle="modal" data-target="#modalVerTodoMensajesClinica" onclick="fnVerTodoMensajes('<?php echo $usuario ?>')">Ver todas las notificaciones</a>
				  	  	</div>
			  	  	</div>

					<!-- NOTIFICACION DE GESTION -->

					<div class="">
			    	  	<a class="nav-link" data-toggle="dropdown" href="#" data-toggle="tooltip" data-placement="bottom" title="Notificaciones de gestión">
			    	    	<i class="far fa-bell"></i>
			    	    	<?php if ($totalRows_qrNotificación2 == 0) {?>
			    	    	<span class="badge badge-warning navbar-badge"><?php echo $totalRows_qrNotificación2 ?></span>
			    	    	<?php }else{ ?>
			  				<span class="badge badge-success navbar-badge"><?php echo $totalRows_qrNotificación2 ?></span>
			    	    	<?php } ?>    	    
			    	  	</a>

			    	  	<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
			    	    	<span class="dropdown-item dropdown-header"><?php echo $totalRows_qrNotificación2 ?> Notificación(es)</span>
			    	    	<?php while (!$qrNotificación2->EOF) {?>
			    	    	<div class="dropdown-divider"></div>
			    	    	<a href="#" class="dropdown-item" data-toggle="modal" data-target="#modalMensajeRecibido" onclick="fnVerMensajeRecibido(<?php echo $qrNotificación2->Fields('ID'); ?>)">
			    	      		<i class="fas fa-envelope mr-2"></i> <?php echo utf8_encode($qrNotificación2->Fields('ASUNTO')).' R0'.$qrNotificación2->Fields('ID_DERIVACION'); ?><br>&ensp;&ensp;&ensp;<span class="text-muted text-sm"><?php echo date("d-m-Y",strtotime($qrNotificación2->Fields('FECHA'))); ?></span><span class="float-right text-muted text-sm"><?php echo date("G:i",strtotime($qrNotificación2->Fields('HORA'))); ?></span>
			    	    	</a>
			    	    	<?php $qrNotificación2->MoveNext();
			  				}?>

			    	    	<div class="dropdown-divider"></div>
				    		<a href="#" class="dropdown-item dropdown-footer" data-toggle="modal" data-target="#modalVerTodoMensajesClinica" onclick="fnVerTodoMensajes('<?php echo $usuario ?>')">Ver todas las notificaciones</a>
			    	  	</div>
		    	  	</div>
	    	  	</div>
	    	</li>

	      	<li class="nav-item">
	        	<!-- <a class="nav-link" data-widget="fullscreen" href="#" role="button">
	          		<i class="fas fa-expand-arrows-alt"></i>
	        	</a> -->
	      	</li>
	    </ul>

	</nav>

<script type="text/javascript">

	$(function () {
	  $('[data-toggle="tooltip"]').tooltip()
	})

	function fnVerMensajeRecibido(id){
		$("#dvVerMensajeRecibido").load('notificaciones/mensajes/mensaje.php?id='+id);// carga mensaje en modalMensajeRecibido
	}
	function fnVerMensajeRecibidoIcrs(id){
		$("#dvVerMensajeRecibidoIcrs").load('notificaciones/mensajes/mensajeIcrs.php?id='+id);// carga mensaje en modalMensajeRecibido
	}
	function fnVerTodoMensajes(usuario){
		$("#dvVerTodoMensajesClinica").load('notificaciones/mensajes/todosLosMensajes.php?usuario='+usuario);// carga mensaje en modalMensajeRecibido
	}
	function fnVerTodoMensajesIcrs(usuario){
		$("#dvVerTodoMensajesIcrs").load('notificaciones/mensajes/todosLosMensajesIcrs.php?usuario='+usuario);// carga mensaje en modalMensajeRecibido
	}
</script>

