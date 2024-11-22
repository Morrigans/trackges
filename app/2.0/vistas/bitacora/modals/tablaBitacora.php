<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

//require_once('modalProgramarTarea.php');

$usuario = $_SESSION['dni'];
$tipoUsuario = $_SESSION['tipoUsuario'];
$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idPatologia = $qrDerivacion->Fields('ID_PATOLOGIA');

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE ID_PATOLOGIA = '$idPatologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$oncologico = $qrPatologia->Fields('ONCOLOGICO');

$query_qrBitacora = "SELECT * FROM $MM_oirs_DATABASE.2_bitacora WHERE ID_DERIVACION = '$idDerivacion' AND (ESTADO IS NULL or ESTADO = '') AND (COMPARTIDO_ICRS IS NULL OR COMPARTIDO_ICRS='si' OR COMPARTIDO_ICRS = '') order by ID_BITACORA desc";
$qrBitacora = $oirs->SelectLimit($query_qrBitacora) or die($oirs->ErrorMsg());
$totalRows_qrBitacora = $qrBitacora->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
      <div class="card-body">
      	<div id="dvAdjuntarDocumento"></div>
      	<br>
      	<?php if ($totalRows_qrBitacora > 0) {?>
      		<div class="table-responsive">
      			<table class="table" id="tbitacora">
      				<thead>
      					<tr>
      						<th>Usuario</th>
      						<th>Registro</th>
      						<th></th>
      					</tr>
      				</thead>
      				<tbody>
	      				<?php while (!$qrBitacora->EOF) {

	      					$query_qrPrestadoresDerivados = "SELECT 
	      					DISTINCT (derivaciones_canastas.RUT_PRESTADOR) as RUT_PRESTADOR 
	      					FROM $MM_oirs_DATABASE.derivaciones_canastas, $MM_oirs_DATABASE.prestador 
	      					WHERE 
	      					derivaciones_canastas.ID_DERIVACION = '$idDerivacion' and
	      					derivaciones_canastas.RUT_PRESTADOR = prestador.RUT_PRESTADOR and
	      					prestador.MODULO_PRESTADOR = 'si'
	      					order by prestador.DESC_PRESTADOR desc";
	      					$qrPrestadoresDerivados = $oirs->SelectLimit($query_qrPrestadoresDerivados) or die($oirs->ErrorMsg());
	      					$totalRows_qrPrestadoresDerivados = $qrPrestadoresDerivados->RecordCount();

	      					$qrPrestadoresDerivados->Fields('RUT_PRESTADOR');

	      					$sesion = $qrBitacora->Fields('SESION');
	      					$compartidoIcrs = $qrBitacora->Fields('COMPARTIDO_ICRS');
	      					$idBitacora = $qrBitacora->Fields('ID_BITACORA');
	      					$ruta = $qrBitacora->Fields('RUTA_DOCUMENTO');
	      					$rutaAudio = $qrBitacora->Fields('RUTA_AUDIO');
	      					$comentarioBitacora = utf8_encode($qrBitacora->Fields('BITACORA'));
	      					//quito las comillas al registro en bitacora para pasarlo por la api.
	      					$comentarioBitacora = str_replace('"', '/', $comentarioBitacora);
	      					//preg_replace( class="hljs-string">'~[\\\\/:*?"<>|]~', ' ', $string); probar en caso de que existan mas caracteres extraños que boten la api
	      					$asunto = $qrBitacora->Fields('ASUNTO');
	      					$tipoRegistro = $qrBitacora->Fields('TIPO_REGISTRO');

	      					if($compartidoIcrs=='si'){
	      						$usuarioComparte='Instituto del Cáncer';
	      					}else{
	      					
		      					$query_qrLogin= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$sesion'";
		      					$qrLogin = $oirs->SelectLimit($query_qrLogin) or die($oirs->ErrorMsg());
		      					$totalRows_qrLogin = $qrLogin->RecordCount();

		      					$usuarioComparte=utf8_encode($qrLogin->Fields('NOMBRE'));

	      					}
	      				?>
	      				<tr>
	      					<th style="width:30%">
	      						<!-- Si es del prestador muestra el texto prestador, sino muestra el profesional local que genero el comentario -->
	      						<?php if ($qrBitacora->Fields('SESION') == null) {?>
	      								Prestador
	      						<?php }else{ ?>
	      								<?php echo $usuarioComparte; ?>
	      						<?php } ?>

	      						<font color="white">/</font><br><small><?php echo date("d-m-Y",strtotime($qrBitacora->Fields('AUDITORIA'))); ?>/<?php echo $qrBitacora->Fields('HORA'); ?><font color="white">/</font><br>Asunto: <?php echo utf8_encode($qrBitacora->Fields('ASUNTO')); ?></small><br> 
	      						

	      						<?php 
	      						// ***************PROGRAMACION*****************************
	      						if($qrBitacora->Fields('PROGRAMADO')=='si' and $qrBitacora->Fields('SESION') == $usuario) { ?>
	      								<a href="#" type="button" data-toggle="modal" data-target="#modalProgramarTarea" onclick="fnProgramarTarea('<?php echo $idBitacora ?>','<?php echo $idDerivacion ?>')"><span class="badge badge-danger"><i class="far fa-clock"></i></span></a>
	      						<?php }

	      						if($qrBitacora->Fields('PROGRAMADO')!='si' and $qrBitacora->Fields('SESION') == $usuario) { ?>
	      								<a href="#" type="button" data-toggle="modal" data-target="#modalProgramarTarea" onclick="fnProgramarTarea('<?php echo $idBitacora ?>','<?php echo $idDerivacion ?>')"><span class="badge badge-info"><i class="far fa-clock"></i></span></a>
	      						<?php }
	      						//****************************************************


	      						// ***************ADJUNTO*****************************
 								?>
	      						<!-- si no hay adjunto y el emisor es el gestor de red no muestra opcion de adjuntar -->
	      						<?php if($ruta=='' and $qrBitacora->Fields('SESION') == $usuario){ ?>
	      							<a href="#" type="button">
	      								<input type="file" name="archivos[]" accept=".xlsx, .docx, .pdf, .png, .jpg, .jpeg, .gif" id="file-2" class="inputfile inputfile-2" data-multiple-caption="{count} archivos seleccionados" onchange="subirArchivo(this, '<?php echo $idBitacora; ?>', '<?php echo $idDerivacion; ?>')"/>
	      								<label for="file-2">
	      									<span class="badge badge-warning"><i class="fas fa-paperclip"></i></span> 
	      								</label>

	      							</a>
	      						<?php }
	      						
	      						//si tiene adjunto y es del prestador solo muestra el ver documento, el eliminar documento se inactiva
	      						if($ruta!='' and $qrBitacora->Fields('SESION') == null){ ?>
	      							
	      								<span class=""><a target="_blank" class="btn btn-xs btn-success" href="https://domicilio.redges.cl/vistas/bitacora/adjuntaDoc/<?php echo $ruta; ?>" ><i class="far fa-file-pdf"></i></a></span>
	      						<?php } 

	      						if ($qrBitacora->Fields('SESION') == $usuario) {
		      						//si tiene adjunto y es local muestra el ver documento y el eliminar
		      						if($ruta!='' and $qrBitacora->Fields('SESION') != null){ ?>
										[<span class=""><a target="_blank" class="btn btn-xs btn-success" href="2.0/vistas/bitacora/adjuntaDoc/<?php echo $ruta; ?>" ><i class="far fa-file-pdf"></i></a></span>
										<button class="btn btn-xs btn-danger" onclick="preguntarSiNoEliminaAdjunto('<?php echo $idBitacora ?>','<?php echo $ruta ?>','<?php echo $idDerivacion ?>')"><span class=" fas fa-trash-alt"></span></button>]
		      						<?php } 
	      						}else{
      								if($ruta!='' and $qrBitacora->Fields('SESION') != null){ 

      									if($compartidoIcrs=='si'){ ?>
      										
      										<span class=""><a target="_blank" class="btn btn-xs btn-success" href="https://icrs.redges.cl/vistas/modulos/segundoPrestador/clSantiago/bitacora/adjuntaDoc/<?php echo $ruta; ?>" ><i class="far fa-file-pdf"></i></a></span>


      									<?php }else{ ?>
      										
      										<span class=""><a target="_blank" class="btn btn-xs btn-success" href="https://crss.redges.cl/2.0/vistas/bitacora/adjuntaDoc/<?php echo $ruta; ?>" ><i class="far fa-file-pdf"></i></a></span>


      									<?php }

      									?>
										
		      						<?php } 
	      						 }
	      						//********************************************************

	      						//***************AUDIO************************************
	      						if ($qrBitacora->Fields('SESION') == $usuario) {
	      							if ($qrBitacora->Fields('RUTA_AUDIO') == null) {?>
	      								<!-- <a href="#" data-toggle="modal" data-target="#modalAudios" onclick="$('#idBitacora').val(<?php echo $idBitacora ?>);$('#idDerivacionAudio').val(<?php echo $idDerivacion ?>)"><span class="badge badge-primary"><i class="fas fa-microphone"></i></span></a> -->
	      						<?php }
	      							if ($qrBitacora->Fields('RUTA_AUDIO') != null){ ?>
	      								[<a href="#" onclick="$('#dvFrmPlayAudios').load('2.0/vistas/bitacora/modals/frmPlayAudios.php?idBitacora='+<?php echo $idBitacora ?>)"><span class="badge badge-warning"><i class="fas fa-play"></i></span></a>
	      								<a href="#" onclick="document.getElementById('audioBitacora').pause()"><span class="badge badge-warning"><i class="fas fa-stop"></i></span></a>
	      								<button class="btn btn-xs btn-danger" onclick="preguntarSiNoEliminaAudio('<?php echo $idBitacora ?>','<?php echo $rutaAudio ?>','<?php echo $idDerivacion ?>')"><span class=" fas fa-trash-alt"></span></button>] 
	      								
	      						<?php }
	      						}else{ 
      								if ($qrBitacora->Fields('RUTA_AUDIO') != null){ ?>
      								[<a href="#" onclick="$('#dvFrmPlayAudios').load('2.0/vistas/bitacora/modals/frmPlayAudios.php?idBitacora='+<?php echo $idBitacora ?>)"><span class="badge badge-warning"><i class="fas fa-play"></i></span></a>
      								<a href="#" onclick="document.getElementById('audioBitacora').pause()"><span class="badge badge-warning"><i class="fas fa-stop"></i></span></a>]
      								
      							<?php }
	      						} 	
	      						//************************************************************

	      						//***************COMPARTIR************************************
	      						//si la derivacion no tiene prestadores asociados con modulo de prestador no muestra la opcion de compartir mensaje
	      						
	      						//si es local y el campo COMPARTIDO es null muestro el compartir
	      						 if ($qrBitacora->Fields('SESION') != null and $qrBitacora->Fields('COMPARTIDO_EXT') != 'si' and $qrBitacora->Fields('SESION') == $usuario AND $oncologico=='si') {?>
      								<a href="#" onclick="preguntarSiNoCompartirRegistroBitacora('<?php echo $idBitacora ?>','<?php echo $ruta ?>','<?php echo $idDerivacion ?>','<?php echo trim(preg_replace('/\s+/', ' ',$comentarioBitacora)); ?>','<?php echo utf8_encode($asunto) ?>','<?php echo $rutaAudio ?>')"><span class="badge badge-info"><i class="fas fa-share-alt"></i></span></a>

      							
	      						<?php } 
	      						

	      						//si es local y el campo COMPARTIDO es si, muestro el compartir
	      						if ($qrBitacora->Fields('SESION') != null and $qrBitacora->Fields('COMPARTIDO_EXT') == 'si') {?>
	      							<span class="badge badge-default"><i class="fas fa-share-alt"></i></small></span>
	      						<?php } 
	      						//************************************************************
	      						?>
	      					<?php if( $tipoUsuario=='1'or $tipoUsuario=='2'){ ?>
	      					<a href="#" type="button"  onclick="fnEliminarRegistro('<?php echo $idBitacora ?>','<?php echo $idDerivacion ?>')"><span class="badge badge-danger"><i class="fas fa-trash"></i></small></span></a>

	      				<?php } ?>
	      					<style type="text/css">

	      						.inputfile {
	      						    width: 0.1px;
	      						    height: 0.1px;
	      						    opacity: 0;
	      						    overflow: hidden;
	      						    position: absolute;
	      						    z-index: -1;
	      						}
	      					
	      						}
	      					</style> 


 	      					</th>
	      					<td><?php echo utf8_encode(nl2br($qrBitacora->Fields('BITACORA'))); ?></td>
	      					<td><?php echo $qrBitacora->Fields('AUDITORIA'); ?></td>
	      				</tr>
	      				<?php $qrBitacora->MoveNext(); } ?>
      				</tbody>
      			</table>
      		</div>
      	<?php }else{?>
      		<h5 align="center">El paciente no tiene comentarios en bitácora</h5>
      	<?php } ?>
							
       	</div>
	</body>
</html>

<script>

	function subirArchivo(input, idBitacora,idDerivacion) {
	    const file = input.files[0];
	    if (file) {
	        const formData = new FormData();
	        formData.append('archivo', file);
	        formData.append('idBitacora', idBitacora);
	        formData.append('idDerivacion', idDerivacion);

	        // Enviar el archivo mediante una solicitud AJAX
	        fetch('2.0/vistas/bitacora/modals/procesar_archivos.php', {
	            method: 'POST',
	            body: formData
	        })
	        .then(response => response.json())
	        .then(data => {
	            if (data.success) {
	                // alert('Archivo subido con éxito.');
	                Swal.fire({
	                  position: 'top-end',
	                  icon: 'success',
	                  title: 'Subido correctamente',
	                  showConfirmButton: false,
	                  timer: 1800
	                })
	               	                
	               $('#dvfrmBitacora').load('2.0/vistas/bitacora/modals/frmBitacora.php?idDerivacion=' + idDerivacion);
	            } else {
	                alert('Error al subir el archivo.');
	            }
	        })
	        .catch(error => {
	            console.error('Error en la solicitud AJAX:', error);
	        });
	    }
	}

function fnProgramarTarea(idBitacora,idDerivacion){
	
	$('#dvfrmProgramarTarea').load('2.0/vistas/bitacora/modals/frmProgramarTarea.php?idBitacora='+idBitacora + '&idDerivacion='+idDerivacion);
}


function fnEliminarRegistro(idBitacora,idDerivacion){

Swal.fire({
	  title: 'Estas Segur@?',
	  text: "Perderas este registro!",
	  icon: 'warning',
	  showCancelButton: true,
	  confirmButtonColor: '#3085d6',
	  cancelButtonColor: '#d33',
	  confirmButtonText: 'Si, Quitar!'
	}).then((result) => {
	  if (result.isConfirmed) {
	  	eliminaRegistoBitacora(idBitacora,idDerivacion)
	  }
	})
}


function eliminaRegistoBitacora(idBitacora,idDerivacion){
	cadena = 'idBitacora=' + idBitacora;

	$.ajax({
		type:"post",
		data:cadena,
		url:'2.0/vistas/bitacora/modals/eliminaRegistoBitacora.php',
		success:function(r){
			
			if (r == 1) {
				Swal.fire({
				  position: 'top-end',
				  icon: 'success',
				  title: 'Eliminado correctamente',
				  showConfirmButton: false,
				  timer: 1500
				})
				setTimeout(function (){ $('#dvTablaBitacora').load('2.0/vistas/bitacora/modals/tablaBitacora.php?idDerivacion='+idDerivacion); }, 1501);//retardo PARA EVITAR dropdown
    	}
		}
	});
}



	


function PreguntaSiNofnDesprogramarTarea(idBitacora,usuario,idDerivacion){
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
	  	fnDesprogramarTarea(idBitacora,usuario,idDerivacion)
	  }
	})
}



function fnDesprogramarTarea(idBitacora,usuario,idDerivacion){
	cadena = 'idBitacora=' + idBitacora;

	$.ajax({
		type:"post",
		data:cadena,
		url:'2.0/vistas/bitacora/modals/desprogramarTarea.php',
		success:function(r){
			if (r == 1) {
				Swal.fire({
				  position: 'top-end',
				  icon: 'success',
				  title: 'Se desprogramo correctamente',
				  showConfirmButton: false,
				  timer: 1500
				})
				setTimeout(function (){ $('#dvTablaBitacora').load('2.0/vistas/bitacora/modals/tablaBitacora.php?idDerivacion='+idDerivacion); }, 1501);//retardo PARA EVITAR dropdown
    	}
		}
	});
}

$(function () {
	    $('#tbitacora').DataTable({
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
	
	function fnFrmGrabarAudio(idBitacora){
		//$('#dvFrmGrabarAudio').load('2.0/vistas/bitacora/audio/audio.php');
		  // cadena = '';
		  // $.ajax({
				// type: "POST",
			 //    url: "2.0/vistas/bitacora/audio/audio.php",
			 //    data: cadena,
			 //    success:function(r){
		  //      	$('#dvAdjuntarDocumento').html(r);	
		  //     }
		  // });
	}

	function fnMuestraSelectPrestadorCompartir(idBitacora) {

		$('#slElijePrestador'+idBitacora).show();
	}

	function preguntarSiNoCompartirRegistroBitacora(idBitacora,ruta,idDerivacion,comentarioBitacora,asunto,rutaAudio){
			Swal.fire({
		  title: 'Estas Seguro?',
		  text: "Esta información sera compartida con el gestor de red!",
		  icon: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Si, Compartir!'
		}).then((result) => {
		  if (result.isConfirmed) {
		  	fnCompartirRegistroBitacora(idBitacora,ruta,idDerivacion,comentarioBitacora,asunto,rutaAudio)
		    
		  }
		})
	}

function fnCompartirRegistroBitacora(idBitacora,ruta,idDerivacion,comentarioBitacora,asunto,rutaAudio){
	cadena = 'idBitacora=' + idBitacora+
			  '&ruta='+ ruta +
		  	  '&comentarioBitacora='+ comentarioBitacora +
			  '&asunto='+ asunto +
			  '&idDerivacion='+ idDerivacion +
			  '&rutaAudio='+ rutaAudio;

    $.ajax({
  		type: "POST",
  	    url: "2.0/vistas/bitacora/modals/estadoCompartido.php",
  	    data: cadena,
  		success:function(r){
          	$('#dvTablaBitacora').load('2.0/vistas/bitacora/modals/tablaBitacora.php?idDerivacion=' + idDerivacion);
        	if (r=1) {
        		swal("Todo bien!", "se compartio registro", "success");
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
	      url: "2.0/vistas/bitacora/adjuntaDoc/docs/eliminaAdjunto.php",
	      data: cadena,
	      success: function(r) {
	        $('#dvTablaBitacora').load('2.0/vistas/bitacora/modals/tablaBitacora.php?idDerivacion=' + idDerivacion);
			
	      }
	  });

	}

	function preguntarSiNoEliminaAudio(idBitacora,ruta,idDerivacion) {
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
		  	fnQuitarEliminaAudio(idBitacora,ruta,idDerivacion)
		    Swal.fire(
		      'Eliminado!',
		      'Tu archivo fue eliminado.',
		      'success'
		    )
		  }
		})
	}

	function fnQuitarEliminaAudio(idBitacora,ruta,idDerivacion){
		cadena = 'idBitacora=' + idBitacora+
				  '&ruta='+ ruta;
	  $.ajax({
	      type: "POST",
	      url: "2.0/vistas/bitacora/modals/audios/eliminaAudio.php",
	      data: cadena,
	      success: function(r) {
	        $('#dvTablaBitacora').load('2.0/vistas/bitacora/modals/tablaBitacora.php?idDerivacion=' + idDerivacion);
			
	      }
	  });

	}
</script>




