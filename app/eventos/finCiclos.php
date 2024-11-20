<?php  
require_once '../Connections/oirs.php';
require_once '../includes/functions.inc.php';


$query_select2 = ("SELECT 
	$MM_oirs_DATABASE.pacientes.id, 
	$MM_oirs_DATABASE.pacientes.COD_RUTPAC, 
	$MM_oirs_DATABASE.pacientes.NOMBRE, 
	$MM_oirs_DATABASE.pacientes.FECHA_DERIVACION 
	FROM $MM_oirs_DATABASE.pacientes");
$select2 = $oirs->SelectLimit($query_select2) or die($oirs->ErrorMsg());
$totalRows_select2 = $select2->RecordCount();

$data = array();


while (!$select2->EOF) {
	$codRutPAc = $select2->Fields('COD_RUTPAC');
	//$fechaDerivacion = explode(" ", $select2->Fields('FECHA_DERIVACION');
	$hora = '';
	$hora3 = '';
	$fechaDerivacion = $select2->Fields('FECHA_DERIVACION');

	$query_buscaCiclos = ("SELECT * FROM $MM_oirs_DATABASE.ciclos WHERE COD_RUTPAC = '$codRutPAc'");
	$buscaCiclos = $oirs->SelectLimit($query_buscaCiclos) or die($oirs->ErrorMsg());
	$totalRows_buscaCiclos = $buscaCiclos->RecordCount();

	while (!$buscaCiclos->EOF) {
	 $inicioCiclo = $buscaCiclos->Fields('INICIO_CICLO');
	 $finCiclo = $buscaCiclos->Fields('FIN_CICLO');
	 $start = $finCiclo;
	

	  $obj = (object) [

	  	'id'=> $select2->Fields('ID'),
	    'title'=> 'C'.$totalRows_buscaCiclos.' '.utf8_decode($select2->Fields('NOMBRE')),
	    'title2'=> utf8_decode($select2->Fields('NOMBRE')),
	    'start'=> $start,
	    'codRutPac'=> $select2->Fields('COD_RUTPAC'),
	    'end2'=> $hora3
	  ];
	$data[] = $obj;
	 
	 $buscaCiclos->MoveNext();
	} /*fin while*/

	$select2->MoveNext();
	
}
echo json_encode($data);

$select2->close();
?>

		
				
				

