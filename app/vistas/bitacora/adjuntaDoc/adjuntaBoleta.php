<?php 
// require_once '../../head/headers.php';
 ?>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
 <!-- dropzonejs -->
  <link rel="stylesheet" href="vistas/assets/plugins/dropzone/min/dropzone.min.css">
    <!-- dropzonejs -->
<script src="vistas/assets/plugins/dropzone/min/dropzone.min.js"></script>

</head>
<body>
  <br>
        <div class="container" >
          	<div id="dvCurriculum" class="col-md-12">
           		<div class="callout callout-info">
            		<div class="card-body box-profile">

		                <div id="actions" class="row">
		                  <div class="col-lg-3">
		                    <div class="btn-group w-100">
		                      <span class="btn btn-success btn-sm col fileinput-button">
		                        <i class="fas fa-plus"></i>
		                        <span>Agregar Boleta (.pdf)</span>
		                      </span>
		                    </div>
		                  </div>
		                  <div class="col-lg-6 d-flex align-items-center">
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
    url: "vistas/verPagos/adjuntarBoleta/sube.php",
    paramName: "file",

    acceptedFiles: "application/pdf",
    maxFilesize: 4,
    maxFiles: 1,
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
 
// Hide the total progress bar when nothing's uploading anymore
myDropzone.on("queuecomplete", function(progress) {
    //document.querySelector("#total-progress").style.opacity = "0";
    myDropzone.destroy();
    fnCargarMiContenido('vistas/verPagos/tblMuestraMisPagosPendientes.php','content-wrapper');

});
 
// Setup the buttons for all transfers
// The "add files" button doesn't need to be setup because the config
// `clickable` has already been specified.
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
</script>