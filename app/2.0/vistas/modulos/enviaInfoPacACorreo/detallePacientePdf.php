<?php 
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';
require_once '../../../plugins/MPDF57/mpdf.php';

date_default_timezone_set("America/Santiago");
$fechaRegistro= date('Y-m-d');

$idDerivacion = $_REQUEST['idDerivacion'];
$codRutPac = $_REQUEST['codRutPac'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$nombrePac = $qrPaciente->Fields('NOMBRE');
$naci = $qrPaciente->Fields('FEC_NACIMI');
$comuna = $qrPaciente->Fields('COMUNA');
$direccion = $qrPaciente->Fields('DIRECCION');
$mail = $qrPaciente->Fields('MAIL');
$fono = $qrPaciente->Fields('FONO');
$region = $qrPaciente->Fields('REGION');
$idConvenio = $qrDerivacion->Fields('ID_CONVENIO');

$query_qrPrevision= "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$idConvenio'";
$qrPrevision = $oirs->SelectLimit($query_qrPrevision) or die($oirs->ErrorMsg());
$totalRows_qrPrevision = $qrPrevision->RecordCount();

$prevision = $qrPrevision->Fields('PREVISION');

$codPatologia= $qrDerivacion->Fields('CODIGO_PATOLOGIA');

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE CODIGO_PATOLOGIA = '$codPatologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$nomPatologia= $qrPatologia->Fields('DESC_PATOLOGIA');

$query_qrDerivacionEtapa= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_etapas WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacionEtapa = $oirs->SelectLimit($query_qrDerivacionEtapa) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionEtapa = $qrDerivacionEtapa->RecordCount();

$query_qrDerivacionCanasta= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
$qrDerivacionCanasta = $oirs->SelectLimit($query_qrDerivacionCanasta) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanasta = $qrDerivacionCanasta->RecordCount();

$query_qrRegion= "SELECT * FROM $MM_oirs_DATABASE.regiones WHERE region_id = '$region'";
$qrRegion = $oirs->SelectLimit($query_qrRegion) or die($oirs->ErrorMsg());
$totalRows_qrRegion = $qrRegion->RecordCount();

$nomRegion= $qrRegion->Fields('region_nombre');

$query_qrComuna= "SELECT * FROM $MM_oirs_DATABASE.comunas WHERE comuna_id = '$comuna'";
$qrComuna = $oirs->SelectLimit($query_qrComuna) or die($oirs->ErrorMsg());
$totalRows_qrComuna = $qrComuna->RecordCount();

$nomComuna= $qrComuna->Fields('comuna_nombre');

//   function calculaedad($naci){
//   list($ano,$mes,$dia) = explode("-",$naci);
//   $ano_diferencia  = date("Y") - $ano;
//   $mes_diferencia = date("m") - $mes;
//   $dia_diferencia   = date("d") - $dia;
//   if ($dia_diferencia < 0 || $mes_diferencia < 0)
//     $ano_diferencia--;
//   return $ano_diferencia;
// }

  
$html= '
	<header class="">
		<div align="center"><img src="../../../images/logo-red-salud.png" width="200" height="70"></div>
		<p align="center"><font size="7">Datos Derivación</font><br>
		<p align="center"><font size="4">Paciente: '.utf8_encode($codRutPac).'</font><br>
		<font size="5">Número Derivación: '.utf8_encode($qrDerivacion->Fields('N_DERIVACION')).'</font></p>
	</header>
	<div class="container">
		<fieldset>
			<br>
			<table width="100%" align="center">
				<tr>
					<td><div><strong>NOMBRE DEL PACIENTE:</strong>  '.utf8_encode($nombrePac).' </div></td>										
				</tr>
				<tr>
					<td><div><strong>COMUNA:</strong>  '.utf8_encode($nomComuna).' </div></td>										
				</tr>
				<tr>
					<td><div><strong>CORREO:</strong>  '.utf8_encode($mail).' </div></td>										
				</tr> 
				<tr>
					<td><div><strong>DIRECCIÓN:</strong>  '.utf8_encode($direccion).', '.utf8_encode($nomRegion).' </div></td>										
				</tr>
			</table>
		</fieldset>

		<fieldset>
			<table width="100%" align="center">
				<tr>
					<td><div><strong>CONVENIO:</strong>  '.utf8_encode($prevision).' </div></td>										
				</tr>
				<tr>
					<td><div><strong>PATOLOGIA:</strong>'.utf8_encode($nomPatologia).'</div></td>									
				</tr>
				<tr>					
					<td><div><strong>FECHA DERIVACIÓN:</strong> '.date("d-m-Y",strtotime($qrDerivacion->Fields('FECHA_DERIVACION'))).' </div></td>
				</tr>
				<tr>					
					<td><div><strong>ESTADO:</strong> '.utf8_encode($qrDerivacion->Fields('ESTADO')).' </div></td>
				</tr>
				<tr>					
					<td><div><strong>FECHA LIMITE:</strong> '.date("d-m-Y",strtotime($qrDerivacion->Fields('FECHA_LIMITE'))).' </div></td><br> 
				</tr>				
			</table>
			<table width="100%" align="left"> 
				<thead>
					<tr>
		              	<th width="10%" align="left">ETAPAS:</th><hr>
		            </tr>
              	
              	</thead>
              	<tbody>
	          	';				          	
	          	while (!$qrDerivacionEtapa->EOF) {
	          		$codEtapaPatologia = $qrDerivacionEtapa->Fields('CODIGO_ETAPA_PATOLOGIA');
	          		$idEtapaPatologia = $qrDerivacionEtapa->Fields('ID_ETAPA_PATOLOGIA');

	          		$query_qrEtapaPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA = '$codEtapaPatologia'";
	          		$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
	          		$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

	          		$query_qrBuscaCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_ETAPA_PATOLOGIA = '$idEtapaPatologia' AND ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
	          		$qrBuscaCanastaPatologia = $oirs->SelectLimit($query_qrBuscaCanastaPatologia) or die($oirs->ErrorMsg());
	          		$totalRows_qrBuscaCanastaPatologia = $qrBuscaCanastaPatologia->RecordCount();

	          		if ($totalRows_qrBuscaCanastaPatologia == 0) {
	          			// code...
	          		}else{
	          			$html.='<tr>
				          		<td align="left"><font size="2">'.utf8_encode($qrEtapaPatologia->Fields('DESC_ETAPA_PATOLOGIA')).'</font></td>
			          		</tr>';
	          		}

	          		
	          	$qrDerivacionEtapa->MoveNext(); }
	          	$html.='</tbody>
	        </table>
			<table width="100%"> 
				<thead>
					<tr>
		              	<th width="10%" align="left">CANASTAS:</th><hr>
		            </tr> 
              	
              	</thead>
              	<tbody>
	          	';
	          	if ($totalRows_qrDerivacionCanasta == 0) {
          			$html.='<tr>
			          		<td align="left"><font size="2">No hay canastas activas</font></td>
		          		</tr>';
	          	}else{
	          		$i=1;
	          		while (!$qrDerivacionCanasta->EOF) {
	          			$codCanastaPatologia = $qrDerivacionCanasta->Fields('CODIGO_CANASTA_PATOLOGIA');

	          			$query_qrCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanastaPatologia'";
	          			$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
	          			$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

	          			$html.='<tr>
				          		<td><font size="2">'.utf8_encode($qrCanastaPatologia->Fields('DESC_CANASTA_PATOLOGIA')).'</font></td>
			          		</tr>';
	          		$qrDerivacionCanasta->MoveNext();
	          		}
	          	}
	          	$html.='</tbody>
	        </table>
	        <br>

		</fieldset>		
	</div>
	<br>
	<div> 
	 
	</div>
	';

	$mpdf= new mPDF('c', 'A4');
	//$mpdf->writeHTML('<div>holaaaa.......</div>');
	$mpdf->writeHTML($html); 
	$mpdf->Output('Datos_'.$nombrePac.'_'.$fechaRegistro.'.pdf', 'I');
	//$mpdf->MultiCell(90,8,utf8_decode($html),1,'FJ',1);
	?>