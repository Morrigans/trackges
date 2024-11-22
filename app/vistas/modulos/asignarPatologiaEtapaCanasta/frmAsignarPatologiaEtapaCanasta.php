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

$idUsuario = $_SESSION['idUsuario'];
$tipoUsuario = $_SESSION['tipoUsuario'];

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$decreto = $qrDerivacion->Fields('DECRETO');

$query_qrDerivacionCanasta= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
$qrDerivacionCanasta = $oirs->SelectLimit($query_qrDerivacionCanasta) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanasta = $qrDerivacionCanasta->RecordCount();

$query_qrDerivacionEtapa= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_etapas WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacionEtapa = $oirs->SelectLimit($query_qrDerivacionEtapa) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionEtapa = $qrDerivacionEtapa->RecordCount();

$query_qrPatologiaTabla  = "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE ID_TIPO_PATOLOGIA='1' ORDER BY DESC_PATOLOGIA ASC";
$qrPatologiaTabla = $oirs->SelectLimit($query_qrPatologiaTabla ) or die($oirs->ErrorMsg());
$totalRows_qrPatologiaTabla  = $qrPatologiaTabla->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Asignar Patologia, etapa y canasta</h2></div>
						<div class="col-md-6" id="dvInfoVentanasOpciones"></div>	
						<div class="col-md-6">
							<?php if ($idPatologiaDerivacion != '') { ?><br>
								<span><h5>Esta derivación ya posee su patologia, etapa y canasta asignada</h5></span>
							<?php }else{ ?>
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Tipo patología</span>
							    </div>
							    <select name="slTipoPatologiaDerivacion" id="slTipoPatologiaDerivacion" class="form-control input-sm">
							        <option value="1">GES</option>
							    </select>
							</div>

							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Patología</span>
							    </div>
							    <select name="slPatologiaDerivacion" id="slPatologiaDerivacion" class="form-control input-sm select2bs4" onchange="fnFiltraEtapasPatologias()">
							         <option value="">Seleccione...</option>
							         <?php
							        while(!$qrPatologiaTabla->EOF){  ?> 	 
							            <option value="<?php echo $qrPatologiaTabla->Fields('ID_PATOLOGIA')?>"><?php echo $qrPatologiaTabla->Fields('DESC_PATOLOGIA')?></option>
							        <?php
							          $qrPatologiaTabla->MoveNext();
							          } ?>
							    </select>
							    
							</div>

							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Etapa patología</span>
							    </div>
							    <select name="slEtapaPatologiaDerivacion" id="slEtapaPatologiaDerivacion" class="form-control input-sm" onchange="fnFiltraCanastasPatologias()">
							    </select>
							</div>

							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Canasta patología</span> 
							    </div>
							    <select name="slCanastaPatologiaDerivacion" id="slCanastaPatologiaDerivacion" class="form-control input-sm" onchange="fnExtraeTiempoLimite(this.value)">
							    </select>
							    <input type="hidden" id="hdTiempoLimite">
							</div>

							<div class="input-group mb-3 col-sm-12">
							  <div class="input-group-prepend">
							    <span class="input-group-text">Fecha Activación Canasta</span>
							  </div>
							  <input type='date' class="form-control input-sm" name="fechaActivacion" id="fechaActivacion" onblur="calculaFinFecha()"/>
							</div>
							<div class="input-group mb-3 col-sm-12" style="display: none">
							  <div class="input-group-prepend">
							    <span class="input-group-text">Fecha límite Garantia</span>
							  </div>
							  <input type='text' class="form-control input-sm" name="fechaFinGarantia" id="fechaFinGarantia" readonly />
							</div>
						
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-success" data-dismiss="modal" onclick="fnAsignarPatologiaEtapaCanasta('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>')">Guardar Patología</button>
			  		</div> 
			  		<?php } ?>    
	  		</div>
	  		<input type="hidden" id="idDerivacion" value="<?php echo $idDerivacion ?>">
	  		<input type="hidden" id="decreto" value="<?php echo $decreto ?>">
	</body>
</html>

<script>
	idDerivacion = $('#idDerivacion').val();	
	$('#dvInfoVentanasOpciones').load('vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

	function fnFiltraEtapasPatologias(){
	    
	    $("#slPatologiaDerivacion option:selected").each(function () {        
	        patologia=$(this).val();
	        tipoPatologia = $("#slTipoPatologiaDerivacion").val();
	        decreto = $("#decreto").val();
	        if (tipoPatologia == '2') {
	          $("#slEtapaPatologiaDerivacion").val('0');
	          $("#slCanastaPatologiaDerivacion").val('0');
	        }else{
	          $.post("vistas/derivacion/php/filtraEtapasPatologias.php",
	          { patologia: patologia, decreto: decreto },
	            function(data){
	                $("#slEtapaPatologiaDerivacion").html(data);
	          });
	        }
	    });
	}

	function fnFiltraCanastasPatologias(){
      $("#slEtapaPatologiaDerivacion option:selected").each(function () {
          etapaPatologia=$(this).val();
          decreto = $("#decreto").val();
          $.post("vistas/derivacion/php/filtraCanastasPatologias.php",
          { etapaPatologia: etapaPatologia, decreto: decreto },
            function(data){
            $("#slCanastaPatologiaDerivacion").html(data);
          });
      });
    }

    function fnExtraeTiempoLimite(canasta){
    cadena = 'canasta='+ canasta;
    
      $.ajax({
       url: 'vistas/derivacion/php/extraeTiempoLimite.php',
       type: "POST",
       data: cadena,
       success: function(r) {
          $('#hdTiempoLimite').val(r);
          calculaFinFecha();
         }
      });
  }

  calculaFinFecha = function(){
      fecha = $("#fechaActivacion").val();
      d = $("#hdTiempoLimite").val();
      if (d=='' || d == 0) {
        $('#fechaFinGarantia').val('Sin Limite');
      }else{
        var info = fecha.split('-');
        fecha=  info[2] + '-' + info[1] + '-' + info[0];
        var Fecha = new Date();
        var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
        var sep = sFecha.indexOf('/') != -1 ? '/' : '-';
        var aFecha = sFecha.split(sep);
        var fecha = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
        fecha= new Date(fecha);
        fecha.setDate(fecha.getDate()+parseInt(d));
        var anno=fecha.getFullYear();
        var mes= fecha.getMonth()+1;
        var dia= fecha.getDate();
        mes = (mes < 10) ? ("0" + mes) : mes;
        dia = (dia < 10) ? ("0" + dia) : dia;
        var fechaFinal = dia+sep+mes+sep+anno;
        $('#fechaFinGarantia').val(fechaFinal); 
        return (fechaFinal);         
      }
  }


function fnAsignarPatologiaEtapaCanasta(idDerivacion, tipoUsuario){
	idPatologia = $('#slPatologiaDerivacion').val();
	idEtapa = $('#slEtapaPatologiaDerivacion').val();
	idCanasta = $('#slCanastaPatologiaDerivacion').val();
	fechaActivacion = $("#fechaActivacion").val();
	fechaFinGarantia = $("#fechaFinGarantia").val();
	

	if (idPatologia == '' || idEtapa == '' || idCanasta == '') {
		Swal.fire({
		  icon: 'error',
		  title: 'Oops...',
		  text: 'debe seleccionar los datos necesarios!',
		})
	}else{
	cadena = 'idDerivacion=' + idDerivacion +
			 '&idPatologia=' + idPatologia +
			 '&idEtapa=' + idEtapa +
			 '&idCanasta=' + idCanasta +
			 '&fechaFinGarantia=' + fechaFinGarantia +
			 '&fechaActivacion=' + fechaActivacion;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/asignarPatologiaEtapaCanasta/asignarPatologiaEtapaCanasta.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'Datos guardados con exito',
					  showConfirmButton: false,
					  timer: 1400
					})
							if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
								setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
							}
							if (tipoUsuario == 4) {// administrativa
								setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioAdministrativa/inicioSAdministrativa.php'); }, 1);
							}
							// if (tipoUsuario == 5) {//medico
							// 	setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
							// }
							if (tipoUsuario == 6) {//tens
								setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioTens/inicioTens.php'); }, 1);
							}
	    		}
				
			}
		});
	}
} 

	
</script>



