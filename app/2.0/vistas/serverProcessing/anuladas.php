<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';
require_once 'conecta.php';

$idUsuario= $_REQUEST['idUsuario'];

$query_qrTipoUsuario = "SELECT * FROM $MM_oirs_DATABASE.login WHERE ID = '$idUsuario'";
$qrTipoUsuario = $oirs->SelectLimit($query_qrTipoUsuario) or die($oirs->ErrorMsg());
$totalRows_qrTipoUsuario = $qrTipoUsuario->RecordCount();

$tipo = $qrTipoUsuario->Fields('TIPO');
// $idClinica = $qrTipoUsuario->Fields('ID_PRESTADOR');

if ($tipo == 1 or $tipo == 2) {
	// DB table to use
	$table = <<<EOT
	 (
	SELECT 
	  		DISTINCT a.ID_DERIVACION,
	  		a.FOLIO,
	  	    a.N_DERIVACION,
	  	    a.FECHA_DERIVACION,
	  	    a.LATERALIDAD,
	  	    a.COD_RUTPAC,
	  	    a.PROCESO,
	  	    a.CATEGORIA,
	  	    a.INTERVENCION_SANITARIA,
	  	    a.ESTADO_RN,
	  	    a.PROBLEMA_SALUD,
	  	    di.FOLIO_HIJO,
	  	    di.ETAPA,
	  	    di.TIPO_COMPRA,
	  	    di.DESCRIPCION,
	  	    di.MONTO_PRESTACION,
	  	    di.MONTO_AT,
	  	    di.TOTAL,
	  	    di.ESTADO_HIJO,
	  	    b.NOMBRE AS NOMBRE_PACIENTE,
	  	    g.NOMBRE as TENS,
	  	    c.NOMBRE AS GESTORA,
	  	    med.NOMBRE AS MEDICO,
	  	    adm.NOMBRE AS ADMINISTRATIVA

	  		FROM 2_derivaciones a
	  	    
	  	    LEFT JOIN 2_derivaciones_hijos di
	  	    ON a.ID_DERIVACION = di.ID_DERIVACION

	  	    LEFT JOIN pacientes b
			ON a.COD_RUTPAC = b.COD_RUTPAC

			LEFT JOIN login g
			ON a.TENS = g.USUARIO

			LEFT JOIN login c
			ON a.ENFERMERA = c.USUARIO

			LEFT JOIN login med
			ON a.MEDICO = med.USUARIO

			LEFT JOIN login adm
			ON a.ADMINISTRATIVA = adm.USUARIO

			WHERE
			a.ESTADO_RN = 'Anulado'

	 ) temp
	EOT;
}

if ($tipo == 3) {
	// DB table to use
	$table = <<<EOT
	 (
	  	SELECT 
	  		DISTINCT a.ID_DERIVACION,
	  		a.FOLIO,
	  	    a.N_DERIVACION,
	  	    a.FECHA_DERIVACION,
	  	    a.LATERALIDAD,
	  	    a.COD_RUTPAC,
	  	    a.PROCESO,
	  	    a.CATEGORIA,
	  	    a.INTERVENCION_SANITARIA,
	  	    a.ESTADO_RN,
	  	    a.PROBLEMA_SALUD,
	  	    di.FOLIO_HIJO,
	  	    di.ETAPA,
	  	    di.TIPO_COMPRA,
	  	    di.DESCRIPCION,
	  	    di.MONTO_PRESTACION,
	  	    di.MONTO_AT,
	  	    di.TOTAL,
	  	    di.ESTADO_HIJO,
	  	    b.NOMBRE AS NOMBRE_PACIENTE,
	  	    g.NOMBRE as TENS,
	  	    c.NOMBRE AS GESTORA,
	  	    med.NOMBRE AS MEDICO,
	  	    adm.NOMBRE AS ADMINISTRATIVA

	  		FROM 2_derivaciones a
	  	    
	  	    LEFT JOIN 2_derivaciones_hijos di
	  	    ON a.ID_DERIVACION = di.ID_DERIVACION

	  	    LEFT JOIN pacientes b
			ON a.COD_RUTPAC = b.COD_RUTPAC

			LEFT JOIN login g
			ON a.TENS = g.USUARIO

			LEFT JOIN login c
			ON a.ENFERMERA = c.USUARIO

			LEFT JOIN login med
			ON a.MEDICO = med.USUARIO

			LEFT JOIN login adm
			ON a.ADMINISTRATIVA = adm.USUARIO

			WHERE
			a.ESTADO_RN = 'Anulado'

	 ) temp
	EOT;
}
// Table's primary key
$primaryKey = 'ID_DERIVACION';

$columns = array(
	array( 'db' => 'ID_DERIVACION', 'dt' => 0 , "formatter" => function($d, $row) { 

		return ' 
			<div class="btn-group">
				<button type="button" class="btn btn-default"><i class="fas fa-cog"></i></button>
				<button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
				<div class="dropdown-menu" role="menu">
				<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('.$d.')">Bitacora</a> 
				</div>
			</div>';

	;}), 
	array( 'db' => 'FOLIO', 'dt' => 1 , "formatter" => function($d, $row) {return '<html><span class="badge badge-dark"><font size="2">'.$d.'</font></span></html>';}),
	array( 'db' => 'ESTADO_RN',  'dt' => 2 , "formatter" => function($d, $row) { return '<html><span class="badge badge-default"><font size="2">'.$d.'</font></span></html>';}),
	array( 'db' => 'FECHA_DERIVACION',  'dt' => 3 , "formatter" => function($d, $row) {
						$fechaLatino = date("d-m-Y",strtotime($row[3]));
						return '<html><font size="2">'.$fechaLatino.'</font></html>';
						}),

	array( 'db' => 'COD_RUTPAC',  'dt' => 4 , "formatter" => function($d, $row) {
					$rutSinPuntos = str_replace('.', '', $row[4]);
					return '<html><span><font size="2">'.$rutSinPuntos.'</font></span></html>';}),
	array( 'db' => 'NOMBRE_PACIENTE',  'dt' => 5 , "formatter" => function($d, $row) {return '<html><span><font size="1">'.$d.'</font></span></html>';}),
	array( 'db' => 'CATEGORIA',  'dt' => 6, "formatter" => function($d, $row) {return '<html><span><font size="2">'.$d.'</font></span></html>';}),
	array( 'db' => 'PROBLEMA_SALUD',  'dt' => 7, "formatter" => function($d, $row) {return '<html><span><font size="1">'.$d.'</font></span></html>';}),
	array( 'db' => 'INTERVENCION_SANITARIA',  'dt' => 8, "formatter" => function($d, $row) {return '<html><font size="2">'.$d.'</font></html>';}),
	array( 'db' => 'FOLIO_HIJO',  'dt' => 9, "formatter" => function($d, $row) {return '<html><span class="badge badge-default"><font size="2">'.$d.'</font></span></html>';}),
	array( 'db' => 'ESTADO_HIJO',  'dt' => 10, "formatter" => function($d, $row) {return '<html><span class="badge badge-default"><font size="2">'.$d.'</font></span></html>';}),
	array( 'db' => 'GESTORA',  'dt' => 11, "formatter" => function($d, $row) {return '<html><font size="2">'.$d.'</font></html>';}),
	array( 'db' => 'TIPO_COMPRA',  'dt' => 12, "formatter" => function($d, $row) {return '<html><font size="2">'.$d.'</font></html>';}),
	array( 'db' => 'DESCRIPCION',  'dt' => 13, "formatter" => function($d, $row) {return '<html><font size="2">'.$d.'</font></html>';}),
	array( 'db' => 'MONTO_PRESTACION',  'dt' => 14, "formatter" => function($d, $row) {return '<html><font size="2">'.$d.'</font></html>';}),
	array( 'db' => 'MONTO_AT',  'dt' => 15, "formatter" => function($d, $row) {return '<html><font size="2">'.$d.'</font></html>';}),
	array( 'db' => 'TOTAL',  'dt' => 16, "formatter" => function($d, $row) {return '<html><font size="2">'.$d.'</font></html>';}),
	array( 'db' => 'GESTORA',  'dt' => 17, "formatter" => function($d, $row) {return '<html><font size="2">'.$d.'</font></html>';}),
	array( 'db' => 'TENS',  'dt' => 18, "formatter" => function($d, $row) {return '<html><font size="2">'.$d.'</font></html>';}),
	array( 'db' => 'MEDICO',  'dt' => 19, "formatter" => function($d, $row) {return '<html><font size="2">'.$d.'</font></html>';}),
	array( 'db' => 'ADMINISTRATIVA',  'dt' => 20, "formatter" => function($d, $row) {return '<html><font size="2">'.$d.'</font></html>';}),

	 // array( 'db' => 'ID_DERIVACION',  'dt' => 18, "formatter" => function($d, $row) {return '<html><font size="2">'.$row[0].'</font></html>';}),
	
	
); 


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( 'ssp.class.php' );

echo json_encode(
	SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $whereAll )
);


