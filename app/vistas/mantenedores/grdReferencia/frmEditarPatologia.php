<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idPatologia = $_REQUEST['idPatologia'];

$query_buscaPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE ID_PATOLOGIA='$idPatologia'";
$buscaPatologia = $oirs->SelectLimit($query_buscaPatologia) or die($oirs->ErrorMsg());
$totalRows_buscaPatologia = $buscaPatologia->RecordCount();

  $descPatologia= $buscaPatologia->Fields('DESC_PATOLOGIA');
  $codPatologia= $buscaPatologia->Fields('CODIGO_PATOLOGIA');
  $idTipoPatologia= $buscaPatologia->Fields('ID_TIPO_PATOLOGIA');
  $diasVigencia= $buscaPatologia->Fields('DIAS_VIGENCIA');

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia order by DESC_TIPO_PATOLOGIA asc";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

?>
<form id="frmEditarPatologia">
	<div class="card card-info">
	  <input type='hidden' class="form-control input-sm" name="idPatologiaEd" id="idPatologiaEd" value="<?php echo $idPatologia ?>"/>
		  	<div class="card-body">
			  	<div class="row">				  

				    <div class="input-group mb-3 col-sm-8">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Patología</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="descripcionPatologiaEd" id="descripcionPatologiaEd" value="<?php echo $descPatologia ?>"/>
				    </div>				    

				    <div class="input-group mb-3 col-sm-4">
				      <div class="input-group-prepend">
				        <span class="input-group-text">Días Vigencia</span>
				      </div>
				      <input type='text' class="form-control input-sm" name="vigenciaPatologiaEd" id="vigenciaPatologiaEd" value="<?php echo $diasVigencia ?>"/>
				    </div>

				</div>
		   </div>
		   <div class="card-footer">
			    <div>
					    	<button type="button" class="btn btn-success" data-dismiss="modal" onclick="fnEditaPatologia('<?php echo $idPatologia ?>')">Actualizar Patología</button>
					</div>
			</div>
	</div>
</form>
<br>

<script type="text/javascript">

		function	fnEditaPatologia(id){
			cadena = $("#frmEditarPatologia").serialize();
			
		  $.ajax({
		      type: "POST",
		      url: "vistas/mantenedores/patologias/editarPatologia.php",
		      data: cadena,
		      success: function(r) {
		          if (r == 1) {
		          	Swal.fire(
		          	  'Actualizada!',
		          	  'La patología fue actualizada.',
		          	  'success'
		          	)
		          	setTimeout(function (){ $('#dvTablaPatologias').load('vistas/mantenedores/patologias/tablaPatologias.php'); }, 1);
		          } else {
		            Swal.fire(
		          	  'Error!',
		          	  'No se pudo actualizar',
		          	  'error'
		          	)
		          }
		      }
		  });
		}
</script>
