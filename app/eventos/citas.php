<?php  
require_once '../Connections/oirs.php';
require_once '../includes/functions.inc.php';

$inicio = $_REQUEST['start'];
$fin = $_REQUEST['end'];
$usuario = $_REQUEST['usuario'];

$query_select = ("SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$usuario'");
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();

$tipoUsuarioNumero = $select->Fields('TIPO');

$query_qrTipo = ("SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '$tipoUsuarioNumero'");
$qrTipo = $oirs->SelectLimit($query_qrTipo) or die($oirs->ErrorMsg());
$totalRows_qrTipo = $qrTipo->RecordCount();

$tipoUsuarioNombre = $qrTipo->Fields('PROFESION');


if ($tipoUsuarioNombre == 'Administrador') {
	$query_select2 = ("SELECT $MM_oirs_DATABASE.events.id,
	$MM_oirs_DATABASE.events.cod_rutpro,
	$MM_oirs_DATABASE.events.cod_rutpac, 
	$MM_oirs_DATABASE.events.nombre, 
	$MM_oirs_DATABASE.events.start, 
	$MM_oirs_DATABASE.events.hora, 
	$MM_oirs_DATABASE.events.color, 
	$MM_oirs_DATABASE.events.ESTADO_CITA 
	FROM $MM_oirs_DATABASE.events 
	WHERE 
	$MM_oirs_DATABASE.events.ESTADO_CITA <> 'ELIMINADO' and $MM_oirs_DATABASE.events.start >='$inicio' and $MM_oirs_DATABASE.events.start <= '$fin' order by $MM_oirs_DATABASE.events.start ASC");
	$select2 = $oirs->SelectLimit($query_select2) or die($oirs->ErrorMsg());
	$totalRows_select2 = $select2->RecordCount();
}else{
	$query_select2 = ("SELECT $MM_oirs_DATABASE.events.id,
	$MM_oirs_DATABASE.events.cod_rutpro,
	$MM_oirs_DATABASE.events.cod_rutpac, 
	$MM_oirs_DATABASE.events.nombre, 
	$MM_oirs_DATABASE.events.start, 
	$MM_oirs_DATABASE.events.hora, 
	$MM_oirs_DATABASE.events.color, 
	$MM_oirs_DATABASE.events.ESTADO_CITA 
	FROM $MM_oirs_DATABASE.events 
	WHERE 
	$MM_oirs_DATABASE.events.ESTADO_CITA <> 'ELIMINADO' and $MM_oirs_DATABASE.events.start >='$inicio' and $MM_oirs_DATABASE.events.start <= '$fin' AND cod_rutpro = '$usuario' order by $MM_oirs_DATABASE.events.start ASC");
	$select2 = $oirs->SelectLimit($query_select2) or die($oirs->ErrorMsg());
	$totalRows_select2 = $select2->RecordCount();
}

	

$data = array();

while (!$select2->EOF) {
	$hora = $select2->Fields('hora');
	if ($hora=='') {
		$hora = '00:00:00';
	}else{
		list($hr, $min, $seg) = explode(":", $hora);
		$hora1 = $hr;
		$hora2 = $min;
		$hora = $hora1.':'.$hora2;
	}
	$estadoCita = $select2->Fields('ESTADO_CITA');

	$rutPro = $select2->Fields('cod_rutpro');
	$nombre = utf8_encode($select2->Fields('nombre'));

	$qrNomPro = "SELECT * FROM $MM_oirs_DATABASE.login  WHERE USUARIO = '$rutPro'";
	$nomPro = $oirs->SelectLimit($qrNomPro) or die($oirs->ErrorMsg());
	$tRowsNomPro = $nomPro->RecordCount();
	$nombrePro= utf8_encode($nomPro->Fields('NOMBRE'));
	
	$profesion= $nomPro->Fields('TIPO');
	$query_qrProfesion= "SELECT * FROM $MM_oirs_DATABASE.profesion WHERE ID = '$profesion'";
	$qrProfesion = $oirs->SelectLimit($query_qrProfesion) or die($oirs->ErrorMsg());
	$totalRows_qrProfesion = $qrProfesion->RecordCount();

	$especPro= utf8_encode($qrProfesion->Fields('PROFESION'));


	$title = '['.substr($especPro, 0,3).'] '.$nombre;
	
	if ($estadoCita == 'CITA') {
		$color = $qrProfesion->Fields('COLOR');
	}else{
		$color = $select2->Fields('color');
	}
	
	 $obj = (object) [
	'colorEvento'=> $color,//esta variable es para pasar el color al modal de la recepcionista y coordinadora
	'color'=> $color,//este variable es para pintar de colores los events
	'id'=> $select2->Fields('id'),
	'title2' => $nombre,
	'start' => $select2->Fields('start'),
	//'end' => $hora,
	'title'=> $title,
	'rutPac'=> $select2->Fields('cod_rutpac'),
	'rutPro'=> $select2->Fields('cod_rutpro'),
	'fecha'=> $select2->Fields('start'),
	'end2'=> $hora,
	'nomPro'=> $nombrePro,
	'especialidadPro'=> $especPro,
	'estadoCita'=> $estadoCita,
	'icon'=> 'fas fa-times'
	];
	$data[] = $obj;
	
$select2->MoveNext();
}
echo json_encode($data);

// $select2->close();
// $nomPro->close();		
				
				
				

