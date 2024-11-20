<?php
// require_once '../../../../../Connections/oirs.php';
// require_once '../../../../../includes/functions.inc.php';

// $idDerivacion = $_REQUEST['idDerivacion'];
// $rutPaciente = $_REQUEST['rutPaciente'];

// $query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
// $qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
// $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

// $codRutPac = $qrDerivacion->Fields('COD_RUTPAC');

// $query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'";
// $qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
// $totalRows_qrPaciente = $qrPaciente->RecordCount();

?>

        <div class="card-body">
        	<!-- <form id="frmRegistroLogin" method="post"> -->
        	  <div class="card card-info">
        	    <div class="card-header">
        	      <h3 class="card-title">Ingrese los datos solicitados</h3>
        	      <div class="card-tools">
        	        <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
        	          <i class="fas fa-minus"></i>
        	        </button>
        	        <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
        	          <i class="fas fa-times"></i>
        	        </button>
        	      </div>
        	    </div>
        	    <div class="card-body col-sm-12">          
        	        <div class="row">
        	          <div class="input-group mb-3 col-sm-6">
        	              <div class="input-group-prepend">
        	                <span class="input-group-text">Rut</span>
        	              </div>
        	              <input type='text' class="form-control input-sm" name="rutPostulante" id="rutPostulante"/>
        	          </div>

        	          <div class="input-group mb-3 col-sm-6">
        	              <div class="input-group-prepend">
        	                <span class="input-group-text">Nombre</span>
        	              </div>
        	              <input type='text' class="form-control input-sm" name="nombrePostulante" id="nombrePostulante"/>
        	          </div>

        	          <div class="input-group mb-3 col-sm-6">
        	              <div class="input-group-prepend">
        	                <span class="input-group-text">Especialidad</span>
        	              </div>
        	              <select class="form-control input-sm" name="especialidadPostulante" id="especialidadPostulante">
        	                <option value="">Seleccione...</option>
        	                <option value="medico">Médico</option>
        	                <option value="enfermera">Enfermera</option>
        	                <option value="kinesiologo">Kinesiólogo</option>
        	                <option value="nutricionista">Nutricionista</option>
        	                <option value="psicologo">Psicólogo</option>
        	                <option value="tens">Tens</option>
        	            </select>
        	          </div>

        	          <div class="input-group mb-3 col-sm-6">
        	              <div class="input-group-prepend">
        	                <span class="input-group-text">Teléfono</span>
        	              </div>
        	              <input type='text' class="form-control input-sm" name="fonoPostulante" id="fonoPostulante"/>
        	          </div>

        	          <div class="input-group mb-3 col-sm-6">
        	              <div class="input-group-prepend">
        	                <span class="input-group-text">Región</span>
        	              </div>
        	              <select class="form-control input-sm" name="regionPostulante">
        	                <option value="">Seleccione...</option>
        	                <option value="tarapaca">Tarapacá</option>
        	                <option value="antofagasta">Antofagasta</option>
        	                <option value="atacama">Atacama</option>
        	                <option value="coquimbo">Coquimbo</option>
        	                <option value="valparaiso">Valparaíso</option>
        	                <option value="ohiggins">O'Higgins</option>
        	                <option value="maule">El Maule</option>
        	                <option value="biobio">El Bío Bío</option>
        	                <option value="araucania">La Araucanía</option>
        	                <option value="los lagos">Los Lagos</option>
        	                <option value="aysen">Aysén</option>
        	                <option value="magallanes-antartica">Magallanes y Antártica Chilena</option>
        	                <option value="metropolitana">Región Metropolitana de Santiago</option>
        	                <option value="los rios">Los Ríos</option>
        	                <option value="arica PARINACOTA">Arica y Parinacota</option>
        	                <option value="nuble">Ñuble</option>
        	            </select>
        	          </div>

        	          <div class="input-group mb-3 col-sm-6">
        	              <div class="input-group-prepend">
        	                <span class="input-group-text">Comuna</span>
        	              </div>
        	              <input type='text' class="form-control input-sm" name="comunaPostulante" id="comunaPostulante"/>
        	          </div>
        	         <br>
        	        </div>
        	    </div>
        	    <!-- <div align="right" class="card-footer">
        	    <button type="submit" class="btn btn-info">Registrarme</button>
        	  </div> -->
        	  </div>
        	<!-- </form> -->    
	  	</div>
