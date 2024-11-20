<?php 
require_once '../../Connections/oirs.php';
require_once '../../includes/functions.inc.php';
// Solo se permite el ingreso con el inicio de sesion.
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../index.php');
exit; } 

$id = $_REQUEST['id'];

$query_qrNotificación = "SELECT * FROM $MM_oirs_DATABASE.notificaciones where ID='$id'";
$qrNotificación = $oirs->SelectLimit($query_qrNotificación) or die($oirs->ErrorMsg());
$totalRows_qrNotificación = $qrNotificación->RecordCount();

$asunto = $qrNotificación->Fields('ASUNTO');
$mensaje = $qrNotificación->Fields('MENSAJE');
$fecha = $qrNotificación->Fields('FECHA');
$hora = $qrNotificación->Fields('HORA');
$idRespuesta = $qrNotificación->Fields('ID_RESPUESTA');
$idDerivacion = $qrNotificación->Fields('ID_DERIVACION');

?>
<strong>Fecha:</strong> <?php echo date("d-m-Y",strtotime($fecha)); ?><br>
<strong>Hora:</strong> <?php echo $hora; ?>
<p><strong>Asunto:</strong> <?php echo utf8_encode($asunto); ?><br>
<strong>Derivación:</strong> <a href="#" data-dismiss="modal"><span class="badge badge-warning" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrNotificación->Fields('ID_DERIVACION') ?>')"><font size="3"><?php echo 'R0'.$qrNotificación->Fields('ID_DERIVACION'); ?></font></span></a></p><br>
<!-- <strong>mensaje:</strong> -->
<p><?php
	 echo utf8_encode(nl2br($mensaje)); 
	
	 if ($idRespuesta != '') {
	 	$query_qrRespuestas = "SELECT * FROM $MM_oirs_DATABASE.notificaciones where ID='$idRespuesta'";
	 	$qrRespuestas = $oirs->SelectLimit($query_qrRespuestas) or die($oirs->ErrorMsg());
	 	$totalRows_qrRespuestas = $qrRespuestas->RecordCount();

	 	$mensajeOriginal = $qrRespuestas->Fields('MENSAJE');?>

	 	<span class="text-muted"><br><br>
	 		<!-- <strong>Mensaje:</strong> -->
	 		<br>
	 		<?php echo utf8_encode(nl2br($mensajeOriginal)); ?></span> 

	 <?php }else{ ?><br>
	 	<!-- <button type="button" id="btnResponderNotificacion" class="btn btn-info btn-xs" onclick="fnResponderNotificacion(<?php echo $id ?>)">Responder</button> -->
	<?php }
	?>
		
</p>
 
 <!-- <span class="label label-default">Respuesta<br></span> -->
 <textarea name="txaRespuestaNotificacion" id="txaRespuestaNotificacion" rows="5" class="form-control input-sm" placeholder="Ingrese respuesta"></textarea>
 <div><br><button type="button" class="btn btn-info btn-xs" id="btnEnviarRespuestaNotificacion" onclick="fnEnviarRespuestaNotificacion(<?php echo $id ?>)">Enviar respuesta</button></div>
<div class="modal-footer">	
 	<div align="right">
 	    <button type="button" class="btn btn-success" data-dismiss="modal" onclick="fnCambiaEstadoNotificacion(<?php echo $id ?>)">Cerrar Notificación</button>
	</div>	
</div>

<script type="text/javascript">
	$("#txaRespuestaNotificacion").hide();
	$("#btnEnviarRespuestaNotificacion").hide();

	function fnResponderNotificacion(id){
		$("#txaRespuestaNotificacion").show();
		$("#btnEnviarRespuestaNotificacion").show();
		$("#btnResponderNotificacion").hide();
		
	}

	function fnEnviarRespuestaNotificacion(id){
		respuesta = $("#txaRespuestaNotificacion").val();
		cadena = "id=" + id +
				 "&respuesta="+ respuesta;
		$.ajax({
		    type: "POST",
		    url: "notificaciones/php/enviarRespuestaNotificacion.php",
		    data: cadena,
		    success: function(r) {
		    	Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'respuesta enviada con exito',
					  showConfirmButton: false,
					  timer: 2000
					})

		    	$("#txaRespuestaNotificacion").hide();
		    	$("#btnEnviarRespuestaNotificacion").hide();
		    	$("#btnResponderNotificacion").hide();
		    	$("#dvVerMensajeRecibido").load('notificaciones/mensajes/mensaje.php?id='+id);// carga mensaje en modalMensajeRecibido
		       // $("#menuSuperior").load('notificaciones/navbar.php');// refresca menu superior
		    }
		});
	}

	function fnCambiaEstadoNotificacion(id){
		cadena = "id=" + id;
		$.ajax({
		    type: "POST",
		    url: "notificaciones/php/cambiaEstadoNotificacion.php",
		    data: cadena,
		    success: function(r) {
		       $("#menuSuperior").load('notificaciones/navbar.php');// refresca menu superior
		    }
		});
	}
</script>