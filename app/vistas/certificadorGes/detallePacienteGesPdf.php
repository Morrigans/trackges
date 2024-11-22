<?php 
//Connection statement
require_once '../../Connections/oirs.php';

//Aditional Functions
require_once '../../includes/functions.inc.php';
require_once '../../plugins/MPDF57/mpdf.php'; 

$folio = $_REQUEST['folio'];

date_default_timezone_set('America/Santiago');
$fechaRegistro= date('Y-m-d');
$horaRegistro= date('G:i');

$query_qrDerivacion = "
    SELECT 

    	derivaciones.FOLIO,
    	patologia.DESC_PATOLOGIA,
    	canasta_patologia.DESC_CANASTA_PATOLOGIA,
    	login.NOMBRE AS NOMBRE_PROFESIONAL,
    	derivaciones.FECHA_DERIVACION,
    	pacientes.NOMBRE AS NOMBRE_PACIENTE,
    	pacientes.COD_RUTPAC

	FROM derivaciones 

	LEFT JOIN login
	ON derivaciones.ENFERMERA = login.ID

	LEFT JOIN pacientes
	ON derivaciones.ID_PACIENTE = pacientes.ID

	LEFT JOIN patologia
	ON derivaciones.ID_PATOLOGIA = patologia.ID_PATOLOGIA

	LEFT JOIN derivaciones_canastas
	ON derivaciones.ID_DERIVACION = derivaciones_canastas.ID_DERIVACION

	LEFT JOIN canasta_patologia
	ON derivaciones_canastas.ID_CANASTA_PATOLOGIA = canasta_patologia.ID_CANASTA_PATOLOGIA

	WHERE	
	derivaciones.FOLIO='$folio'

";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$patologia = $qrDerivacion->Fields('DESC_PATOLOGIA');
$fechaDerivacion = $qrDerivacion->Fields('FECHA_DERIVACION');
$nomPaciente = $qrDerivacion->Fields('NOMBRE_PACIENTE');
$rutPaciente = $qrDerivacion->Fields('COD_RUTPAC');
$canasta = $qrDerivacion->Fields('DESC_CANASTA_PATOLOGIA');


$insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.carta_ges_historial (FOLIO, RUT_PACIENTE, NOMBRE_PACIENTE, FECHA_DERIVACION, PATOLOGIA, CANASTA, FECHA_REGISTRO, HORA_REGISTRO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($folio, "int"),
    GetSQLValueString($rutPaciente, "text"),
    GetSQLValueString($nomPaciente, "text"),
    GetSQLValueString($fechaDerivacion, "date"),
    GetSQLValueString($patologia, "text"),
    GetSQLValueString($canasta, "text"),
    GetSQLValueString($fechaRegistro, "date"),
    GetSQLValueString($horaRegistro, "date"));
$Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());


$query_qrUltimaCarta = ("SELECT max(ID_CARTA) as ID_CARTA FROM $MM_oirs_DATABASE.carta_ges_historial");
$qrUltimaCarta = $oirs->SelectLimit($query_qrUltimaCarta) or die($oirs->ErrorMsg());
$totalRows_qrUltimaCarta = $qrUltimaCarta->RecordCount();

$idCarta = $qrUltimaCarta->Fields('ID_CARTA');

$query_qrCarta = ("SELECT * FROM $MM_oirs_DATABASE.carta_ges_historial where ID_CARTA = '$idCarta'");
$qrCarta = $oirs->SelectLimit($query_qrCarta) or die($oirs->ErrorMsg());
$totalRows_qrCarta = $qrCarta->RecordCount();

$fechaCarta = $qrCarta->Fields('FECHA_REGISTRO');
$horaCarta = $qrCarta->Fields('HORA_REGISTRO');

  
$html= '
	<header class="">
		<div align="center" >
		<img src="logoRedsalud.png">
		</div>
		<p align="center"><font size="7">Sistema TrackGes</font><br>
		<font size="5">Redsalud Santiago</font><br>
		<font size="5">Carta de Resguardo</font></p>
		<br>
	</header>
	<div class="container">
		<fieldset>
			<br>
			<table width="100%" align="center">
				<tr>
					<td width="10%"><div><strong>Nro:</strong> '.$idCarta.'</div></td>
					<td width="60%"></td>
					<td width="30%"><div><strong></strong> '.$fechaCarta.' / '.$horaCarta.'</div></td>
				</tr>
			</table>
		</fieldset><br>
		<br>
		<fieldset>
			<legend>Datos del Paciente</legend>
			======================<br>
			<table width="100%" align="center">
				<tr>
					<td><div><strong>Rut:</strong> '.$rutPaciente.'</div></td>
				</tr>
				<tr>
					<td><div><strong>Nombre:</strong>  '.$nomPaciente.' </div></td>										
				</tr>
			</table>
		</fieldset>	
		<p> </p>
		<fieldset>
			<legend>Datos de la Derivacion</legend>
			======================<br>
			<table width="100%" align="center">
				<tr>
					<td><div><strong>Fecha Derivacion:</strong> '.$fechaDerivacion.'</div></td>
				</tr>
				<tr>
					<td><div><strong>Patologia:</strong>  '.$patologia.' </div></td>										
				</tr>
				<tr>
					<td><div><strong>Canasta:</strong>  '.$canasta.' </div></td>										
				</tr>
			</table>
		</fieldset>

	</div>
	<br>
	';

	$mpdf= new mPDF('c', 'A4');
	//$mpdf->writeHTML('<div>holaaaa.......</div>');
	$mpdf->writeHTML($html); 
	$mpdf->Output('informe.pdf', 'I');

	?>