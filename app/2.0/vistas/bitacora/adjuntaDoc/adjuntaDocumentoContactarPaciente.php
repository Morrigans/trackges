<?php 
// require_once '../../head/headers.php';
//$idBitacora = $_REQUEST['idBitacora'];
$idDerivacion = $_REQUEST['idDerivacion'];
//$idDerivacionCanasta = $_REQUEST['idDerivacionCanasta'];
 ?>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
 <!-- dropzonejs -->
  <link rel="stylesheet" href="2.0/vistas/bitacora/adjuntaDoc/css/dropzone.css">
    <!-- dropzonejs -->
<script src="2.0/vistas/bitacora/adjuntaDoc/js/dropzone.js"></script>

</head>
<body>
  <br>
        <div class="container" >
        	<input type="hidden" class="form-control input-sm" name="hdidDerivacion" id="hdidDerivacion" value="<?php echo $idDerivacion ?>" />
        	<!-- <input type="text" class="form-control input-sm" name="hdIdBitacora" id="hdIdBitacora" value="<?php echo $idBitacora ?>" /> -->
          	<div id="dvCurriculum" class="col-md-12">
           		<div class="callout callout-info">
            		<div class="card-body box-profile">

		                <div id="actions" class="row">
		                  <div class="col-lg-4">
		                    <div class="btn-group w-100">
		                      <span class="btn btn-success btn-sm col fileinput-button">
		                        <i class="fas fa-plus"></i>
		                        <span>Seleccionar</span>
		                      </span>
		                    </div>
		                  </div>
		                  <div class="col-lg-8 d-flex align-items-center">
		                    <div class="fileupload-process w-100">
		                      <div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
		                        <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
		                      </div>
		                    </div>
		                  </div>
		                </div>

		                <div class="table table-striped files" id="previews">
		                  <div id="dzBoleta" class="row mt-2">
		                    <div class="col-auto">
		                        <span class="preview"><img src="data:," alt="" data-dz-thumbnail /></span>
		                    </div>
		                    <div class="col d-flex align-items-center">
		                        <p class="mb-0">
		                          <span class="lead" data-dz-name></span>
		                          (<span data-dz-size></span>)
		                        </p>
		                        <strong class="error text-danger" data-dz-errormessage></strong>
		                    </div>
		                    <div class="col-4 d-flex align-items-center">
		                        <div class="progress progress-striped active w-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
		                          <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
		                        </div>
		                    </div>
		                    <div class="col-auto d-flex align-items-center">
		                      <div class="btn-group">
		                        <button class="btn btn-primary start btn-sm">
		                          <i class="fas fa-upload"></i>
		                          <span>Subir</span>
		                        </button>

		                        <button data-dz-remove class="btn btn-danger delete btn-sm">
		                          <i class="fas fa-trash"></i>
		                          <span>Quitar</span>
		                        </button>
		                      </div>
		                    </div>
		                  </div>
		                </div>

            		</div>
              
          		</div>
        	</div>
    	</div>
</body>
</html>

<script>

	// Get the template HTML and remove it from the doument
	var previewNode = document.querySelector("#dzBoleta");
	previewNode.id = "";
	var previewTemplate = previewNode.parentNode.innerHTML;
	previewNode.parentNode.removeChild(previewNode);
	 
	Dropzone.autoDiscover=false;
	var myDropzone = new Dropzone(document.body, {
	    url: "2.0/vistas/bitacora/adjuntaDoc/subeDocContactarPaciente.php?idDerivacion="+<?php echo $idDerivacion?>,
	    paramName: "file",
	    acceptedFiles: "application/pdf,image/*,.doc,.docx,.xls,.xlsx",
	    maxFilesize: 4,
	    maxFiles: 4,
	    thumbnailWidth: 160,
	    thumbnailHeight: 160,
	    thumbnailMethod: 'contain',
	    parallelUploads: 20,
	    previewTemplate: previewTemplate,
	    autoQueue: false,
	    previewsContainer: "#previews",
	    clickable: ".fileinput-button"
	});
	 
	myDropzone.on("addedfile", function(file) {
	    $('.dropzone-here').hide();
	  
	    // Hookup the start button 
	    file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file); };
	});
	 
	// Update the total progress bar
	myDropzone.on("totaluploadprogress", function(progress) {
	    document.querySelector("#total-progress .progress-bar").style.width = progress + "%";    
	});
	 
	myDropzone.on("sending", function(file) {
	    // Show the total progress bar when upload starts
	    document.querySelector("#total-progress").style.opacity = "1";
	    // And disable the start button
	    file.previewElement.querySelector(".start").setAttribute("disabled", "disabled");
	});

	//OBTIENE RESPUESTA DESDE EL ARCHIVO subeDocAtencionPaciente.php QUE GENERA LA RUTA DEL ARCHIVO Y LO ALMACENA EN EL INPUT hdRutaAdjuntaDocAtencion
	myDropzone.on("success", function(file, response) {
		$("#hdRutaAdjuntaDocContactarPaciente").val(response);
	});
	 
	// Hide the total progress bar when nothing's uploading anymore
	myDropzone.on("queuecomplete", function(progress) {
	    //document.querySelector("#total-progress").style.opacity = "0";

	   	idDerivacion=$("#hdidDerivacion").val();
	    myDropzone.destroy();
	    $("#dvCargaAdjuntarContactarPaciente").hide();
	    $("#dvMuestraBtnAdjuntarArchivoContactarPaciente").hide();
	    $("#dvMuestraPreDocAdjuntoContactarPaciente").show();    
	});

	//CODIGOS PARA QUE DROPZONE PUEDA EJECUTARSE MAS DE UNA VEZ Y PERMITA SEGUIR ADJUNTANDO
	myDropzone.on("complete", function(file) { my.removeFile(file); });
	myDropzone.on("complete", function(file) { my.removeAllFiles(file);});

	document.querySelector("#actions .start").onclick = function() {
	    myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
	};
	 
	$('#previews').sortable({
	    items:'.file-row',
	    cursor: 'move',
	    opacity: 0.5,
	    containment: "parent",
	    distance: 20,
	    tolerance: 'pointer',
	    update: function(e, ui){
	        //actions when sorting
	    }
	});

	// INICIO ORDENAMIENTO DE LOS ARCHIVOS MULTIPLES QUE SE HAN SUBIDO
	// init: function() {
	//     // Es importante para que sortable funcione
	//     var myDropzone = this;

	//     // En dropzone tiene el evento click handler
	//     document.getElementById("submit").addEventListener("click", function(e) {

	//         // Asegúrese de que el formulario no se esté enviando realmente
	//         e.preventDefault();

	//         // the new array donde pondremos los nuevos archivos
	//         var current_queue = [];

	//         // the array que queremos actualizar
	//         var oldArray = myDropzone.files;

	//         // on the webpage busca todas las imagenes que se han cargado
	//         var imageTags = $('#previews').find('div.dz-image img');

	//         // iterar a través de todas las imágenes que han sido cargadas por el usuario
	//         imageTags.each(function( index, imageTag ) {
	//             // Obtener el nombre de la imagen de las imágenes
	//             imageName = imageTag.alt;

	//             // ahora iteramos atravez del array antiguo
	//             var i;
	//             for (i = 0; i < oldArray.length; i++) {
	//                 // si el nombre de la imagen en el sitio es el mismo que el de la imagen del arreglo anterior.
	//                 // se agrega al nuevo arreglo. se puede ver como se va ordenando el arreglo
	//                 if(imageName === oldArray[i].name){
	//                     current_queue.push(oldArray[i]);
	//                 }
	//             }
	//         });

	//         // después de que todo esté hecho, actualizaremos el arreglo antiguo
	//         // con el nuevo arreglo para que sepa que los archivos se han ordenado.

	//         myDropzone.files = current_queue;

	//         // dropzone ahora enviará la solicitud
	//         e.stopPropagation();
	//         myDropzone.processQueue();
	//     });
	// }
	// FIN ORDENAMIENTO DE LOS ARCHIVOS MULTIPLES QUE SE HAN SUBIDO

</script>