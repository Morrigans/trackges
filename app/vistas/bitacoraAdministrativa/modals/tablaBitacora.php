<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

require_once('modalProgramarTarea.php');

$usuario = $_SESSION['dni'];

$query_qrBitacora = "SELECT * FROM $MM_oirs_DATABASE.bitacora_administrativa WHERE SESION = '$usuario' order by ID_BITACORA desc";
$qrBitacora = $oirs->SelectLimit($query_qrBitacora) or die($oirs->ErrorMsg());
$totalRows_qrBitacora = $qrBitacora->RecordCount();

$query_qrNombreUsuario= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$usuario'";
$qrNombreUsuario = $oirs->SelectLimit($query_qrNombreUsuario) or die($oirs->ErrorMsg());
$totalRows_qrNombreUsuario = $qrNombreUsuario->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
      <div class="card-body">
      	<div id="dvAdjuntarDocumentoBitacoraAdministrativa"></div>
      	<br>
      	<?php if ($totalRows_qrBitacora > 0) {?>
      		<div class="table-responsive">
      			<table class="table" id="tbitacoraAdministrativa">
      				<thead>
      					<tr>
      						<th>Usuario</th>
      						<th>Registro</th>
      						<th></th>
      					</tr>
      				</thead>
      				<tbody>
	      				<?php while (!$qrBitacora->EOF) {
	      					$query_qrPrestadoresDerivados = "SELECT RUT_PRESTADOR FROM $MM_oirs_DATABASE.prestador WHERE MODULO_PRESTADOR = 'si' order by RUT_PRESTADOR desc";
	      					$qrPrestadoresDerivados = $oirs->SelectLimit($query_qrPrestadoresDerivados) or die($oirs->ErrorMsg());
	      					$totalRows_qrPrestadoresDerivados = $qrPrestadoresDerivados->RecordCount();

	      					$sesion = $qrBitacora->Fields('SESION');
	      					$idBitacora = $qrBitacora->Fields('ID_BITACORA');
	      					$ruta = $qrBitacora->Fields('RUTA_DOCUMENTO');
	      					$comentarioBitacora = utf8_encode($qrBitacora->Fields('BITACORA'));
	      					//quito las comillas al registro en bitacora para pasarlo por la api.
	      					$comentarioBitacora = str_replace('"', '/', $comentarioBitacora);
	      					//preg_replace( class="hljs-string">'~[\\\\/:*?"<>|]~', ' ', $string); probar en caso de que existan mas caracteres extraños que boten la api
	      					$asunto = $qrBitacora->Fields('ASUNTO');
	      					$tipoRegistro = $qrBitacora->Fields('TIPO_REGISTRO');
	      					$emisor = $qrBitacora->Fields('EMISOR');
	      					

	      					$query_qrLogin= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$emisor'";
	      					$qrLogin = $oirs->SelectLimit($query_qrLogin) or die($oirs->ErrorMsg());
	      					$totalRows_qrLogin = $qrLogin->RecordCount();

	      					$query_qrGestoras= "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO = '3' order by NOMBRE asc";
	      					$qrGestoras = $oirs->SelectLimit($query_qrGestoras) or die($oirs->ErrorMsg());
	      					$totalRows_qrGestoras = $qrGestoras->RecordCount();

	      					$query_qrSupervisora= "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO = '2' order by NOMBRE asc";
	      					$qrSupervisora = $oirs->SelectLimit($query_qrSupervisora) or die($oirs->ErrorMsg());
	      					$totalRows_qrSupervisora = $qrSupervisora->RecordCount();

	      				?>
	      				<tr>
	      					<th style="width:30%">
	      						<!-- Si es del gestor de red muestra el texto gestor de red, sino muestra el profesional local que genero el comentario -->
	      						<?php if ($qrBitacora->Fields('SESION') == null) {?>
	      								Prestador
	      						<?php }else{ ?>
	      								<?php echo utf8_encode($qrLogin->Fields('NOMBRE')); ?>
	      						<?php } ?>

	      						<font color="white">/</font><br><small><?php echo date("d-m-Y",strtotime($qrBitacora->Fields('AUDITORIA'))); ?>/<?php echo $qrBitacora->Fields('HORA'); ?><font color="white">/</font><br>Asunto: <?php echo utf8_encode($qrBitacora->Fields('ASUNTO')); ?></small><br> 

	      						<?php if($qrBitacora->Fields('PROGRAMADO')=='si') { ?>
	      								<a href="#" type="button" onclick="PreguntaSiNofnDesprogramarTarea('<?php echo $idBitacora ?>','<?php echo $usuario ?>')"><span class="badge badge-danger"><i class="far fa-clock"></i> <?php echo date("d-m-Y",strtotime($qrBitacora->Fields('FECHA_PROGRAMACION'))); ?></span></a>	
	      						<?php }else{ ?>
	      								<a href="#" type="button" data-toggle="modal" data-target="#modalProgramarTarea" onclick="fnProgramarTarea('<?php echo $idBitacora ?>')"><span class="badge badge-info"><i class="far fa-clock"></i> Programar</span></a>
	      						<?php } ?>

	      						<!-- si no hay adjunto y el emisor es el gestor de red no muestra opcion de adjuntar -->
	      						<?php if($ruta=='' and $qrBitacora->Fields('SESION') != null){ ?>
	      							<a href="#" onclick="$('#dvAdjuntarDocumentoBitacoraAdministrativa').load('vistas/bitacoraAdministrativa/adjuntaDoc/adjuntaDocumento.php?idBitacora='+<?php echo $idBitacora?>)"><span class="badge badge-warning"><i class="fas fa-paperclip"></i> Adjuntar</span></a>
	      						<?php }
	      						
	      						//si tiene adjunto y es del gestor de red solo muestra el ver documento, el eliminar documento se inactiva
	      						if($ruta!='' and $qrBitacora->Fields('SESION') == null){ ?>
									<span class=""><a target="_blank" class="btn btn-xs btn-success" href="https://prestador.historialclinico.cl/vistas/bitacoraAdministrativa/adjuntaDoc/<?php echo $ruta; ?>" ><i class="far fa-file-pdf"></i></a></span>
	      						<?php } 

	      						//si tiene adjunto y es local muestra el ver documento y el eliminar
	      						if($ruta!='' and $qrBitacora->Fields('SESION') != null){ ?>
									<span class=""><a target="_blank" class="btn btn-xs btn-success" href="vistas/bitacoraAdministrativa/adjuntaDoc/<?php echo $ruta; ?>" ><i class="far fa-file-pdf"></i></a></span>
									<button class="btn btn-xs btn-danger" onclick="preguntarSiNoEliminaAdjunto('<?php echo $idBitacora ?>','<?php echo $ruta ?>')"><span class=" fas fa-trash-alt"></span></button>
	      						<?php } 

	      						//si es local y el campo COMPARTIDO es null muestro el compartir
	      						 if ($qrBitacora->Fields('SESION') != null and $qrBitacora->Fields('COMPARTIDO') != 'si') {?>
	      								<a href="#" onclick="fnMuestraSelectCompartir('<?php echo $idBitacora ?>')"><span class="badge badge-info"><i class="fas fa-share-alt"></i> Compartir</span></a>
	      						<?php } 

	      						//si es local y el campo COMPARTIDO es si, muestro el compartido
	      						if ($qrBitacora->Fields('SESION') != null and $qrBitacora->Fields('COMPARTIDO') == 'si') {?>
	      							<!-- <span class="badge badge-default"><i class="fas fa-share-alt"></i> Enviado</small></span> -->
	      							<a href="#" onclick="fnMuestraSelectCompartir('<?php echo $idBitacora ?>')"><span class="badge badge-success"><i class="fas fa-share-alt"></i> Compartido</span></a>
	      						<?php } ?>

	      						<!-- select que muestra las opciones a quien compartir el registro -->
    								<select style="display: none;" name="slElijeDestino<?php echo $idBitacora ?>" id="slElijeDestino<?php echo $idBitacora ?>" onchange="preguntarSiNoCompartirRegistroBitacora('<?php echo $idBitacora ?>','<?php echo $ruta ?>','<?php echo trim(preg_replace('/\s+/', ' ', $comentarioBitacora)); ?>','<?php echo utf8_encode($asunto) ?>','<?php echo $qrNombreUsuario->Fields('NOMBRE'); ?>')">
	  						        <option value="">Seleccione...</option>
	  						        <optgroup label=[Prestador]>
				                    <?php while (!$qrPrestadoresDerivados->EOF) {
				                    	$rutPrestador = $qrPrestadoresDerivados->Fields('RUT_PRESTADOR');

				                    	$query_qrPrestador= "SELECT * FROM $MM_oirs_DATABASE.prestador WHERE RUT_PRESTADOR = '$rutPrestador'";
				                    	$qrPrestador = $oirs->SelectLimit($query_qrPrestador) or die($oirs->ErrorMsg());
				                    	$totalRows_qrPrestador = $qrPrestador->RecordCount();
				                    ?>
				                    <option value="<?php echo $qrPrestadoresDerivados->Fields('RUT_PRESTADOR') ?>"><?php echo utf8_encode($qrPrestador->Fields('DESC_PRESTADOR')) ?></option>
				                    <?php $qrPrestadoresDerivados->MoveNext(); } ?>
	                    	</optgroup>
		                    <optgroup label=[Gestoras]>
			                    <!-- cargo a las gestoras para compartir el registro con ellas -->
			                    <?php while (!$qrGestoras->EOF) { ?>

			                    <option value="<?php echo $qrGestoras->Fields('USUARIO') ?>"><?php echo utf8_encode($qrGestoras->Fields('NOMBRE')) ?></option>
			                    <?php $qrGestoras->MoveNext(); } ?>
		                    </optgroup>
                        <optgroup label=[Supervisora]>
    	                    <!-- cargo a las gestoras para compartir el registro con ellas -->
    	                    <?php while (!$qrSupervisora->EOF) { ?>

    	                    <option value="<?php echo $qrSupervisora->Fields('USUARIO') ?>"><?php echo utf8_encode($qrSupervisora->Fields('NOMBRE')) ?></option>
    	                    <?php $qrSupervisora->MoveNext(); } ?>
                        </optgroup>
	  						    </select>
	      					</th>
	      					<td>
		      						<?php 	echo utf8_encode(nl2br($qrBitacora->Fields('BITACORA'))); ?><br><br>

		      						<!-- SECCION QUE MUESTRA USUARIOS Y PRESTADORES CON QUE SE COMPARTE BITACORA *************************************************************************-->
		      						<?php
		      						// busco usuarios con los que comparti el mensaje
			      						$query_qrVerUsuariosCompartidos= "SELECT * FROM $MM_oirs_DATABASE.bitacora_administrativa WHERE EMISOR = '$usuario' and ID_BITACORA_COMPARTIDO = '$idBitacora'";
			      						$qrVerUsuariosCompartidos = $oirs->SelectLimit($query_qrVerUsuariosCompartidos) or die($oirs->ErrorMsg());
			      						$totalRows_qrVerUsuariosCompartidos = $qrVerUsuariosCompartidos->RecordCount();
		      						?>
		      						
		      								<?php while (!$qrVerUsuariosCompartidos->EOF) { 
		      									$sesionCompartido = $qrVerUsuariosCompartidos->Fields('SESION'); 

		      									$query_qrLoginCompartido= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$sesionCompartido'";
		      									$qrLoginCompartido = $oirs->SelectLimit($query_qrLoginCompartido) or die($oirs->ErrorMsg());
		      									$totalRows_qrLoginCompartido = $qrLoginCompartido->RecordCount();
		      								?>
		      											<!-- obtengo el nombre del usuario con el que lo comparti -->
		      											<small class="badge badge-default"><font color="grey"><?php echo utf8_encode($qrLoginCompartido->Fields('NOMBRE')); ?></font></small>
		      									
		      								<?php $qrVerUsuariosCompartidos->MoveNext(); } ?>
		      						
		      						<?php
		      							// busco prestadores con los que comparti mensaje
			      						$query_qrVerPrestadoresCompartidos= "SELECT * FROM $MM_oirs_DATABASE.bitacora_administrativa WHERE EMISOR = '$usuario' and PRESTADOR_COMPARTIDO != '' AND ID_BITACORA = '$idBitacora'";
			      						$qrVerPrestadoresCompartidos = $oirs->SelectLimit($query_qrVerPrestadoresCompartidos) or die($oirs->ErrorMsg());
			      						$totalRows_qrVerPrestadoresCompartidos = $qrVerPrestadoresCompartidos->RecordCount();
		      						?>

		      						<?php while (!$qrVerPrestadoresCompartidos->EOF) { 
      									$sesionPrestadorCompartido = $qrVerPrestadoresCompartidos->Fields('PRESTADOR_COMPARTIDO'); 

      									$query_qrPrestadorCompartido= "SELECT * FROM $MM_oirs_DATABASE.prestador WHERE RUT_PRESTADOR = '$sesionPrestadorCompartido'";
      									$qrPrestadorCompartido = $oirs->SelectLimit($query_qrPrestadorCompartido) or die($oirs->ErrorMsg());
      									$totalRows_qrPrestadorCompartido = $qrPrestadorCompartido->RecordCount();
      								?>
      											<!-- obtengo el nombre del prestador con el que lo comparti -->
      											<small class="badge badge-default"><font color="grey"><?php echo utf8_encode($qrPrestadorCompartido->Fields('DESC_PRESTADOR')); ?></font></small>
      									
      								<?php $qrVerPrestadoresCompartidos->MoveNext(); } ?>
      						<!-- ************************************************************************************************************************************************ -->
		      						
	      					</td>
	      					<td><?php echo $qrBitacora->Fields('AUDITORIA'); ?></td>
	      				</tr>
	      				<?php $qrBitacora->MoveNext(); } ?>
      				</tbody>
      			</table>
      		</div>
      	<?php }else{?>
      		<h5 align="center">No tienes comentarios en bitácora</h5>
      	<?php } ?>
							
       	</div>
	</body>
</html>

<script>
$(function () {
	    $('#tbitacoraAdministrativa').DataTable({
	      "paging": false,
	      "lengthChange": false,
	      "searching": true,
	      "ordering": true,
	      "info": false,
	      "autoWidth": true,
	      "responsive": true,
	      "order": [[ 2, 'desc' ]],
	      dom: 'lBfrtip',
		    buttons: [ 'excel', 'pdf'],
		    "language": {
		        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
		        },
		    "columnDefs": [
                    {
                        "targets": [ 2 ],
                        "visible": false,
                        "searchable": false
                    }
                   
                ]
	    });

	  });
	

	function fnMuestraSelectCompartir(idBitacora) {

		$('#slElijeDestino'+idBitacora).show();
	}

	function preguntarSiNoCompartirRegistroBitacora(idBitacora,ruta,comentarioBitacora,asunto,usuario){

		rut = $('#slElijeDestino'+idBitacora).val();
		var combo = document.getElementById('slElijeDestino'+idBitacora);
		var nombre = combo.options[combo.selectedIndex].text;

		if (rut == 48) {
			Swal.fire({
			  title: 'Estas Segur@?',
			  text: "Esta información sera compartida con el Prestador!",
			  icon: 'warning',
			  showCancelButton: true,
			  confirmButtonColor: '#3085d6',
			  cancelButtonColor: '#d33',
			  confirmButtonText: 'Si, Compartir!'
			}).then((result) => {
			  if (result.isConfirmed) {
			  	fnCompartirRegistroBitacora(idBitacora,ruta,idDerivacion,comentarioBitacora,asunto,rut)
			    Swal.fire(
			      'Compartido!',
			      'Registro Compartido con el Prestador.',
			      'success'
			    )
			  }
			})
		}else{
			Swal.fire({
			  title: 'Estas Segur@?',
			  text: "Esta información sera compartida con "+nombre+"!",
			  icon: 'warning',
			  showCancelButton: true,
			  confirmButtonColor: '#3085d6',
			  cancelButtonColor: '#d33',
			  confirmButtonText: 'Si, Compartir!'
			}).then((result) => {
			  if (result.isConfirmed) {
			  	fnCompartirRegistroLocal(idBitacora,rut,nombre,usuario)
			  }
			})
		}

			
	}

function fnCompartirRegistroLocal(idBitacora,rut,nombre,usuario){
	cadena="idBitacora="+idBitacora +
			"&rut="+rut+
			"&usuario="+usuario;
	$.ajax({
		type: "POST",
	    url: "vistas/bitacoraAdministrativa/modals/poneDestinoLocal.php",
	    data: cadena,
		success:function(r){
	    	$('#dvTablaBitacoraAdministrativa').load('vistas/bitacoraAdministrativa/modals/tablaBitacora.php');
	  	if (r==1) {
	  		Swal.fire(
		      'Compartido!',
		      'Registro Compartido con '+nombre+'!',
		      'success'
		    )
	  	}
		
	  }
	});
}	

function fnCompartirRegistroBitacora(idBitacora,ruta,idDerivacion,comentarioBitacora,asunto,rut){
	cadena = 'idBitacora=' + idBitacora+
			  '&ruta='+ ruta +
		  	  '&comentarioBitacora='+ comentarioBitacora +
			  '&asunto='+ asunto +
			  '&idDerivacion='+ idDerivacion;
  $.ajax({
		type: "POST",
	    url: "https://prestador.historialclinico.cl/api/compartirRegistroBitacora.php",
	    data: cadena,
	    dataType:'json',
		success:function(r){
       	//recargo la tabla abajo en el otro ajax despues que cambia el estado del campo COMPARTIDO a si		
      }
  });
  	cadena2="idBitacora="+idBitacora +
  					"&prestadorCompartido="+rut;
    $.ajax({
  		type: "POST",
  	    url: "vistas/bitacoraAdministrativa/modals/estadoCompartido.php",
  	    data: cadena2,
  		success:function(r){
          	$('#dvTablaBitacora').load('vistas/bitacoraAdministrativa/modals/tablaBitacora.php');
        	if (r==1) {
        		Swal.fire(
			      'Compartido!',
			      'Registro Compartido con prestador!',
			      'success'
			    )
        		$('#dvTablaBitacoraAdministrativa').load('vistas/bitacoraAdministrativa/modals/tablaBitacora.php');
        	}
  		
        }
    });

}

		function preguntarSiNoEliminaAdjunto(idBitacora,ruta,idDerivacion) {


				Swal.fire({
			  title: 'Estas Seguro?',
			  text: "No podras revertir la eliminación!",
			  icon: 'warning',
			  showCancelButton: true,
			  confirmButtonColor: '#3085d6',
			  cancelButtonColor: '#d33',
			  confirmButtonText: 'Si, Eliminar!'
			}).then((result) => {
			  if (result.isConfirmed) {
			  	fnQuitarEliminaAdjunto(idBitacora,ruta,idDerivacion)
			    Swal.fire(
			      'Eliminado!',
			      'Tu archivo fue eliminado.', 
			      'success'
			    )
			  }
			})
		}

	function fnQuitarEliminaAdjunto(idBitacora,ruta,idDerivacion){
		cadena = 'idBitacora=' + idBitacora+
				  '&ruta='+ ruta;
	  $.ajax({
	      type: "POST",
	      url: "vistas/bitacoraAdministrativa/adjuntaDoc/docs/eliminaAdjunto.php",
	      data: cadena,
	      success: function(r) {
	        $('#dvTablaBitacoraAdministrativa').load('vistas/bitacoraAdministrativa/modals/tablaBitacora.php');
			
	      }
	  });

	}

	function fnProgramarTarea(idBitacora){
		$('#dvfrmProgramarTarea').load('vistas/bitacoraAdministrativa/modals/frmProgramarTarea.php?idBitacora='+idBitacora);
	}

	function PreguntaSiNofnDesprogramarTarea(idBitacora,usuario){
		Swal.fire({
		  title: 'Estas Segur@?',
		  text: "Perderas la notificación de este evento!",
		  icon: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Si, Quitar!'
		}).then((result) => {
		  if (result.isConfirmed) {
		  	fnDesprogramarTarea(idBitacora,usuario)
		  }
		})
	}

	function fnDesprogramarTarea(idBitacora,usuario){
		cadena = 'idBitacora=' + idBitacora;

		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/bitacoraAdministrativa/modals/desprogramarTarea.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'Se desprogramo correctamente',
					  showConfirmButton: false,
					  timer: 1500
					})
					setTimeout(function (){ $('#dvTablaBitacoraAdministrativa').load('vistas/bitacoraAdministrativa/modals/tablaBitacora.php?usuario='+usuario); }, 1501);//retardo PARA EVITAR dropdown
				
	    	}
				
			}
		});
	}
</script>




