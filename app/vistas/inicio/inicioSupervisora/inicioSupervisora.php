<?php
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';
 
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

$usuario = $_SESSION['dni'];
$idUsuario = $_SESSION['idUsuario'];

$query_verProfesion = "SELECT * FROM $MM_oirs_DATABASE.login where ID='$idUsuario'";
$verProfesion = $oirs->SelectLimit($query_verProfesion) or die($oirs->ErrorMsg());
$totalRows_verProfesion = $verProfesion->RecordCount();

$idClinica=$verProfesion->Fields('ID_PRESTADOR');

date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');

$fechaLimite = date("Y-m-d",strtotime($hoy."+ 10 days"));

// $query_qrPorVencer = "SELECT * 
// FROM $MM_oirs_DATABASE.derivaciones, $MM_oirs_DATABASE.derivaciones_canastas, $MM_oirs_DATABASE.login
// where
// $MM_oirs_DATABASE.derivaciones.ID_DERIVACION =  $MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION and
// $MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE<='$fechaLimite' and 
// $MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE!='0000-00-00' and 
// $MM_oirs_DATABASE.derivaciones_canastas.DIAS_LIMITE!='0' and
// $MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE>='$hoy' and 
// $MM_oirs_DATABASE.derivaciones.ESTADO !='cerrada' AND 
// $MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA = '1' and
// $MM_oirs_DATABASE.derivaciones_canastas.ESTADO != 'finalizada' AND
// $MM_oirs_DATABASE.login.ID_PRESTADOR = '$idClinica'";
// $qrPorVencer = $oirs->SelectLimit($query_qrPorVencer) or die($oirs->ErrorMsg());
// $totalRows_qrPorVencer = $qrPorVencer->RecordCount();

// $query_qrVencecidas = "SELECT * 
// FROM $MM_oirs_DATABASE.derivaciones, $MM_oirs_DATABASE.derivaciones_canastas, $MM_oirs_DATABASE.login
// where
// $MM_oirs_DATABASE.derivaciones.ID_DERIVACION =  $MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION and
// $MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE<'$hoy' and 
// $MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE!='0000-00-00' and 
// $MM_oirs_DATABASE.derivaciones_canastas.DIAS_LIMITE!='0' and
// $MM_oirs_DATABASE.derivaciones.ESTADO !='cerrada' AND 
// $MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA = '1' and
// $MM_oirs_DATABASE.derivaciones_canastas.ESTADO != 'finalizada' AND
// $MM_oirs_DATABASE.login.ID_PRESTADOR = '$idClinica'";
// $qrVencecidas = $oirs->SelectLimit($query_qrVencecidas) or die($oirs->ErrorMsg());
// $totalRows_qrVencecidas = $qrVencecidas->RecordCount();

// $query_qrCumplidas = "SELECT
//     DISTINCT($MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION),
//     $MM_oirs_DATABASE.derivaciones.N_DERIVACION,
//     $MM_oirs_DATABASE.derivaciones.ID_DERIVACION,
//     $MM_oirs_DATABASE.derivaciones.ESTADO,
//     $MM_oirs_DATABASE.derivaciones.FECHA_DERIVACION,
//     $MM_oirs_DATABASE.derivaciones.FECHA_LIMITE,
//     $MM_oirs_DATABASE.derivaciones.ID_PACIENTE,
//     $MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA,
//     $MM_oirs_DATABASE.derivaciones_canastas.CODIGO_CANASTA_PATOLOGIA,
//     $MM_oirs_DATABASE.derivaciones_canastas.FECHA_CANASTA,
//     $MM_oirs_DATABASE.derivaciones_canastas.FECHA_FIN_CANASTA,
//     $MM_oirs_DATABASE.derivaciones.ID_PATOLOGIA,
//     $MM_oirs_DATABASE.derivaciones.ID_CONVENIO, 
//     $MM_oirs_DATABASE.derivaciones.REASIGNADA,
//     $MM_oirs_DATABASE.derivaciones.ENFERMERA 
// FROM $MM_oirs_DATABASE.derivaciones, $MM_oirs_DATABASE.derivaciones_canastas, $MM_oirs_DATABASE.login
// where
// $MM_oirs_DATABASE.derivaciones.ID_DERIVACION =  $MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION and
// $MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA = '1' and
// $MM_oirs_DATABASE.derivaciones_canastas.ESTADO = 'finalizada' AND
// $MM_oirs_DATABASE.login.ID_PRESTADOR = '$idClinica'
// group by derivaciones_canastas.ID_DERIVACION";
// $qrCumplidas = $oirs->SelectLimit($query_qrCumplidas) or die($oirs->ErrorMsg());
// $totalRows_qrCumplidas = $qrCumplidas->RecordCount();

$query_qrDerivacionesActivas = "
    SELECT 
  a.N_DERIVACION

  FROM derivaciones a

  WHERE
  -- a.ESTADO != 'cerrada' AND
  (a.ESTADO_RN = 'Prestador Asignado' or a.ESTADO_RN = 'Derivacion Aceptada' or a.ESTADO_RN = 'Solicita autorizacion')  and 
    a.ESTADO_ANULACION ='activo'
";
$qrDerivacionesActivas = $oirs->SelectLimit($query_qrDerivacionesActivas) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionesActivas = $qrDerivacionesActivas->RecordCount();

// $query_qrDerivacionesPendientes = "
//     SELECT 
//     derivaciones.ID_DERIVACION 

//     FROM derivaciones 

//     LEFT JOIN login
//     ON derivaciones.SESION = login.ID

//     WHERE
//     login.ID_PRESTADOR = '$idClinica' AND 
//     derivaciones.ESTADO = 'pendiente'  
// ";
// $qrDerivacionesPendientes = $oirs->SelectLimit($query_qrDerivacionesPendientes) or die($oirs->ErrorMsg());
// $totalRows_qrDerivacionesPendientes = $qrDerivacionesPendientes->RecordCount();

$query_qrAceptadas = "
    SELECT 
    derivaciones.ID_DERIVACION 

    FROM derivaciones 

    LEFT JOIN login
    ON derivaciones.SESION = login.ID

    WHERE
    -- derivaciones.ESTADO != 'cerrada' AND
    -- login.ID_PRESTADOR = '$idClinica' AND 
    derivaciones.ESTADO_RN = 'Derivacion Aceptada'  and 
    derivaciones.ESTADO_ANULACION ='activo' 
";
$qrAceptadas = $oirs->SelectLimit($query_qrAceptadas) or die($oirs->ErrorMsg());
$totalRows_qrAceptadas = $qrAceptadas->RecordCount();


$query_qrPrestadorAsignado = "
    SELECT 
    a.N_DERIVACION,
    a.ID_DERIVACION,
    b.COD_RUTPAC AS RUT_PACIENTE,
    b.NOMBRE AS NOMBRE_PACIENTE,
    d.DESC_CONVENIO,
    a.ESTADO,
    a.FECHA_DERIVACION,
    e.DESC_TIPO_PATOLOGIA,
    f.DESC_PATOLOGIA,
    c.NOMBRE AS NOMBRE_PROFESIONAL,
    a.MONTO_ACUMULADO_RN,
    a.ESTADO_RN,
    a.FOLIO

    FROM derivaciones a

    LEFT JOIN login c
    ON a.ENFERMERA = c.ID

    LEFT JOIN pacientes b
    ON a.ID_PACIENTE = b.ID

    LEFT JOIN convenio d
    ON a.ID_CONVENIO = d.ID_CONVENIO

    LEFT JOIN tipo_patologia e
    ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

    LEFT JOIN patologia f
    ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

    WHERE
    -- a.ESTADO != 'cerrada' AND
    a.ESTADO_RN = 'Prestador Asignado' and
    a.ESTADO_ANULACION = 'activo' 
";
$qrPrestadorAsignado = $oirs->SelectLimit($query_qrPrestadorAsignado) or die($oirs->ErrorMsg());
$totalRows_qrPrestadorAsignado = $qrPrestadorAsignado->RecordCount();

//se evalua que la derivacion este en uno de los 3 estados iniciales (pendiente,aceptada,prestador), por que en estado prestador se puede agendar la primera consulta de esta forma sabemos si se no se ha agendado la primera consulta, ya que elestado primeraConsultaAgendada es el que viene despues del estado prestador, ademas consultamos si la fecha de la derivacion es menor a la fecha de $hoy es por que tiene mas de 24 horas la derivacion en sistema sin agendar primera consulta.
// $query_qrAlertaMenos24Hrs = "SELECT 
// DISTINCT a.N_DERIVACION

// FROM derivaciones a

// LEFT JOIN login c
// ON a.SESION = c.ID

// LEFT JOIN estados_derivacion
// ON a.ID_DERIVACION = estados_derivacion.ID_DERIVACION

// WHERE 
// a.ESTADO != 'cerrada' AND
// (estados_derivacion.ESTADO != 'primeraConsultaAgendada' or a.FECHA_DERIVACION <= '$hoy') AND  
// (a.ESTADO = 'pendiente' or
// a.ESTADO = 'aceptada' or
// a.ESTADO = 'prestador') AND
// c.ID_PRESTADOR = '$idClinica' AND
// a.FECHA_DERIVACION >= '2022-12-20'

// ";
// $qrAlertaMenos24Hrs = $oirs->SelectLimit($query_qrAlertaMenos24Hrs) or die($oirs->ErrorMsg());
// $totalRows_qrAlertaMenos24Hrs = $qrAlertaMenos24Hrs->RecordCount();


//se evalua que la derivacion este en uno de los 3 estados iniciales (pendiente,aceptada,prestador), por que en estado prestador se puede agendar la primera consulta de esta forma sabemos si se no se ha agendado la primera consulta, ya que elestado primeraConsultaAgendada es el que viene despues del estado prestador, ademas consultamos si la fecha de la derivacion es menor a la fecha de $hoy es por que tiene mas de 24 horas la derivacion en sistema sin agendar primera consulta.
// $query_qrAlerta10DiasSinAtencion = "SELECT 
//     DISTINCT a.N_DERIVACION
//     FROM derivaciones a

//     LEFT JOIN login c
//     ON a.SESION = c.ID
       
//   LEFT JOIN estados_derivacion
//     ON a.ID_DERIVACION = estados_derivacion.ID_DERIVACION

//     LEFT JOIN montos g
//     ON a.ID_DERIVACION = g.ID_DERIVACION

//     WHERE
//     (estados_derivacion.ESTADO != 'primeraConsultaAtendida' or '$hoy' <= date_add(a.FECHA_DERIVACION, interval 10 day)) AND
//     (a.ESTADO = 'pendiente' or
//     a.ESTADO = 'aceptada' or
//     a.ESTADO = 'prestador' or
//     a.ESTADO = 'primeraConsultaAgendada') AND
//     a.ESTADO != 'cerrada' AND
//     g.TIPO_MONTO = 'inicial' AND
//     c.ID_PRESTADOR = '$idClinica' AND
//     a.FECHA_DERIVACION >= '2022-12-20'
// ";
// $qrAlerta10DiasSinAtencion = $oirs->SelectLimit($query_qrAlerta10DiasSinAtencion) or die($oirs->ErrorMsg());
// $totalRows_qrAlerta10DiasSinAtencion = $qrAlerta10DiasSinAtencion->RecordCount();


//cuenta cuantas derivaciones son quirurgicas
// $query_qrQuirurgicas = "
//     SELECT 
//        derivaciones.ID_DERIVACION

//         FROM derivaciones

//         LEFT JOIN atenciones
//         ON derivaciones.ID_DERIVACION = atenciones.ID_DERIVACION
        
//         LEFT JOIN events
//         ON atenciones.ID_CITACION = events.id
        
//         WHERE
//         events.TIPO_ATENCION = 'segundaConsulta'
// ";
// $qrQuirurgicas = $oirs->SelectLimit($query_qrQuirurgicas) or die($oirs->ErrorMsg());
// $totalRows_qrQuirurgicas = $qrQuirurgicas->RecordCount();

$query_qrParaPago = "
    SELECT 
    derivaciones.ID_DERIVACION 

    FROM derivaciones 

    LEFT JOIN login
    ON derivaciones.SESION = login.ID

    WHERE
    -- login.ID_PRESTADOR = '$idClinica' AND 
    derivaciones.ESTADO_RN = 'Validado para Pago' and 
    derivaciones.ESTADO_ANULACION ='activo'

";
$qrParaPago = $oirs->SelectLimit($query_qrParaPago) or die($oirs->ErrorMsg());
$totalRows_qrParaPago = $qrParaPago->RecordCount();


$query_qrAltaPac = "
    SELECT 
    a.N_DERIVACION,
    a.ID_DERIVACION,
    b.COD_RUTPAC AS RUT_PACIENTE,
    b.NOMBRE AS NOMBRE_PACIENTE,
    d.DESC_CONVENIO,
    a.ESTADO,
    a.FECHA_DERIVACION,
    e.DESC_TIPO_PATOLOGIA,
    f.DESC_PATOLOGIA,
    c.NOMBRE AS NOMBRE_PROFESIONAL,
    a.MONTO_ACUMULADO_RN,
    a.ESTADO_RN,
    a.FOLIO

    FROM derivaciones a

    LEFT JOIN login c
    ON a.ENFERMERA = c.ID

    LEFT JOIN pacientes b
    ON a.ID_PACIENTE = b.ID

    LEFT JOIN convenio d
    ON a.ID_CONVENIO = d.ID_CONVENIO

    LEFT JOIN tipo_patologia e
    ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

    LEFT JOIN patologia f
    ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

    WHERE
    
    a.ESTADO_RN = 'Alta Paciente'  and 
    a.ESTADO_ANULACION ='activo'

";
$qrAltaPac = $oirs->SelectLimit($query_qrAltaPac) or die($oirs->ErrorMsg());
$totalRows_qrAltaPac = $qrAltaPac->RecordCount();

$query_qrAutorizadoPago = "
    SELECT 
    derivaciones.ID_DERIVACION 

    FROM derivaciones 

    LEFT JOIN login
    ON derivaciones.SESION = login.ID

    WHERE
    -- login.ID_PRESTADOR = '$idClinica' AND 
    derivaciones.ESTADO_RN = 'Autorizado para pago' 
";
$qrAutorizadoPago = $oirs->SelectLimit($query_qrAutorizadoPago) or die($oirs->ErrorMsg());
$totalRows_qrAutorizadoPago = $qrAutorizadoPago->RecordCount();

$query_qrSolicitaAutorizacion = "
  SELECT 
  a.ID_DERIVACION

  FROM derivaciones a

  WHERE
  -- a.ESTADO != 'cerrada' AND
  a.ESTADO_RN = 'Solicita autorizacion'  and
    a.ESTADO_ANULACION = 'activo' 
";
$qrSolicitaAutorizacion = $oirs->SelectLimit($query_qrSolicitaAutorizacion) or die($oirs->ErrorMsg());
$totalRows_qrSolicitaAutorizacion = $qrSolicitaAutorizacion->RecordCount();


$query_qrParaCierre = "
    SELECT 
  a.ID_DERIVACION

  FROM derivaciones a

  WHERE
  -- a.ESTADO != 'cerrada' AND
  a.MARCA = 'para_cierre' AND
  a.ESTADO_RN = 'Solicita autorizacion' and 
    a.ESTADO_ANULACION ='activo'

";
$qrParaCierre = $oirs->SelectLimit($query_qrParaCierre) or die($oirs->ErrorMsg());
$totalRows_qrParaCierre = $qrParaCierre->RecordCount();

$query_qrSolicitaAutorizacionEnPlazo = "
    SELECT 
  DISTINCT a.N_DERIVACION,
  a.ID_DERIVACION,
  b.COD_RUTPAC AS RUT_PACIENTE,
  b.NOMBRE AS NOMBRE_PACIENTE,
  d.DESC_CONVENIO,
  a.ESTADO,
  a.FECHA_DERIVACION,
  e.DESC_TIPO_PATOLOGIA,
  f.DESC_PATOLOGIA,
  c.NOMBRE AS NOMBRE_PROFESIONAL,
    a.MONTO_ACUMULADO_RN,
    a.ESTADO_RN,
    a.FOLIO

  FROM derivaciones a

  LEFT JOIN login c
  ON a.ENFERMERA = c.ID

  LEFT JOIN pacientes b
  ON a.ID_PACIENTE = b.ID

  LEFT JOIN convenio d
    ON a.ID_CONVENIO = d.ID_CONVENIO

  LEFT JOIN tipo_patologia e
  ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

  LEFT JOIN patologia f
  ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

  LEFT JOIN estados_derivacion
  ON a.ID_DERIVACION = estados_derivacion.ID_DERIVACION

  WHERE
  '$hoy' <= date_add(a.FECHA_DERIVACION, interval f.DIAS_VIGENCIA day) AND
  -- a.ESTADO != 'cerrada' AND
   (a.MARCA IS NULL or a.MARCA = '') AND
  a.ESTADO_RN = 'Solicita autorizacion' and 
    a.ESTADO_ANULACION ='activo'

";
$qrSolicitaAutorizacionEnPlazo = $oirs->SelectLimit($query_qrSolicitaAutorizacionEnPlazo) or die($oirs->ErrorMsg());
$totalRows_qrSolicitaAutorizacionEnPlazo = $qrSolicitaAutorizacionEnPlazo->RecordCount();


// ************************************************
$query_qrSolicitaAutorizacionRetrasada = "
    SELECT 
  DISTINCT a.N_DERIVACION, 
  a.ID_DERIVACION,
  b.COD_RUTPAC AS RUT_PACIENTE,
  b.NOMBRE AS NOMBRE_PACIENTE,
  d.DESC_CONVENIO,
  a.ESTADO,
  a.FECHA_DERIVACION,
  e.DESC_TIPO_PATOLOGIA,
  f.DESC_PATOLOGIA,
  c.NOMBRE AS NOMBRE_PROFESIONAL,
    a.MONTO_ACUMULADO_RN,
    a.ESTADO_RN,
    a.FOLIO

  FROM derivaciones a

  LEFT JOIN login c
  ON a.ENFERMERA = c.ID

  LEFT JOIN pacientes b
  ON a.ID_PACIENTE = b.ID

  LEFT JOIN convenio d
    ON a.ID_CONVENIO = d.ID_CONVENIO

  LEFT JOIN tipo_patologia e
  ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

  LEFT JOIN patologia f
  ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

  LEFT JOIN estados_derivacion
  ON a.ID_DERIVACION = estados_derivacion.ID_DERIVACION

  WHERE
  '$hoy' > date_add(a.FECHA_DERIVACION, interval f.DIAS_VIGENCIA day) AND
  -- a.ESTADO != 'cerrada' AND
  (a.MARCA IS NULL or a.MARCA = '') AND
  a.ESTADO_RN = 'Solicita autorizacion'  and 
    a.ESTADO_ANULACION ='activo'

";
$qrSolicitaAutorizacionRetrasada = $oirs->SelectLimit($query_qrSolicitaAutorizacionRetrasada) or die($oirs->ErrorMsg());
$totalRows_qrSolicitaAutorizacionRetrasada = $qrSolicitaAutorizacionRetrasada->RecordCount();


// ************************************************
$query_qrCensoDiario = "
    SELECT 
  a.N_DERIVACION,
  a.ID_DERIVACION,
  a.FOLIO,
  a.ESTADO_RN,
  b.COD_RUTPAC AS RUT_PACIENTE,
  b.NOMBRE AS NOMBRE_PACIENTE,
  d.DESC_CONVENIO,
  a.ESTADO,
  a.FECHA_DERIVACION,
  e.DESC_TIPO_PATOLOGIA,
  f.DESC_PATOLOGIA,
  c.NOMBRE AS NOMBRE_PROFESIONAL,
  a.MONTO_ACUMULADO_RN,
  api_censo.fecha_censo,
  api_censo.id_admision,
  api_censo.fecha_ingreso,
  api_censo.dias_ingresado,
  api_censo.codigo_prestacion,
  api_censo.diagnostico,
  api_censo.nombre_convenio,
  api_censo.ley_urgencia,
  api_censo.fecha_foto
    
  FROM api_censo 
  
  LEFT JOIN derivaciones a
  ON api_censo.ID_DERIVACION = a.ID_DERIVACION

  LEFT JOIN login c
  ON a.ENFERMERA = c.ID

  LEFT JOIN pacientes b
  ON a.ID_PACIENTE = b.ID

  LEFT JOIN convenio d
  ON a.ID_CONVENIO = d.ID_CONVENIO

  LEFT JOIN tipo_patologia e
  ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

  LEFT JOIN patologia f
  ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

  WHERE
  a.ESTADO != 'cerrada' AND
  (a.ESTADO_RN = 'Prestador Asignado' or a.ESTADO_RN = 'Derivacion Aceptada' or a.ESTADO_RN = 'Solicita autorizacion') AND
  api_censo.fecha_registro = '$hoy'
";
$qrCensoDiario = $oirs->SelectLimit($query_qrCensoDiario) or die($oirs->ErrorMsg());
$totalRows_qrCensoDiario = $qrCensoDiario->RecordCount();

// ************************************************
$query_qrPabellonDiario = "
    SELECT 
  a.N_DERIVACION,
  a.ID_DERIVACION,
  a.FOLIO,
  a.ESTADO_RN,
  b.COD_RUTPAC AS RUT_PACIENTE,
  b.NOMBRE AS NOMBRE_PACIENTE,
  d.DESC_CONVENIO,
  a.ESTADO,
  a.FECHA_DERIVACION,
  e.DESC_TIPO_PATOLOGIA,
  f.DESC_PATOLOGIA,
  c.NOMBRE AS NOMBRE_PROFESIONAL,
  a.MONTO_ACUMULADO_RN,
  api_pabellones.fecha_reserva,
  api_pabellones.fecha_inicio_pabellon,
  api_pabellones.nombre_medico,
  api_pabellones.codigo_prestacion,
  api_pabellones.codigo_prestacion,
  api_pabellones.nombre_cirugia,
  api_pabellones.estado
    
  FROM api_pabellones
  
  LEFT JOIN derivaciones a
  ON api_pabellones.ID_DERIVACION = a.ID_DERIVACION

  LEFT JOIN login c
  ON a.ENFERMERA = c.ID

  LEFT JOIN pacientes b
  ON a.ID_PACIENTE = b.ID

  LEFT JOIN convenio d
  ON a.ID_CONVENIO = d.ID_CONVENIO

  LEFT JOIN tipo_patologia e
  ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

  LEFT JOIN patologia f
  ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

  WHERE
  a.ESTADO != 'cerrada' AND
  (a.ESTADO_RN = 'Prestador Asignado' or a.ESTADO_RN = 'Derivacion Aceptada' or a.ESTADO_RN = 'Solicita autorizacion') AND
  api_pabellones.fecha_registro = '$hoy'
";
$qrPabellonDiario = $oirs->SelectLimit($query_qrPabellonDiario) or die($oirs->ErrorMsg());
$totalRows_qrPabellonDiario = $qrPabellonDiario->RecordCount();

// ************************************************
$query_qrPgrPabellones = "
    SELECT 
  a.N_DERIVACION,
  a.ID_DERIVACION,
  a.FOLIO,
  a.ESTADO_RN,
  b.COD_RUTPAC AS RUT_PACIENTE,
  b.NOMBRE AS NOMBRE_PACIENTE,
  d.DESC_CONVENIO,
  a.ESTADO,
  a.FECHA_DERIVACION,
  e.DESC_TIPO_PATOLOGIA,
  f.DESC_PATOLOGIA,
  c.NOMBRE AS NOMBRE_PROFESIONAL,
  a.MONTO_ACUMULADO_RN,
  api_prog_pabellones.COD_PRESTACION,
  api_prog_pabellones.CIRUGIA,
  api_prog_pabellones.ESTADO,
  api_prog_pabellones.ID_RESERVA,
  api_prog_pabellones.FECHA_RESERVA
    
  FROM api_prog_pabellones
  
  LEFT JOIN derivaciones a
  ON api_prog_pabellones.ID_DERIVACION = a.ID_DERIVACION

  LEFT JOIN login c
  ON a.ENFERMERA = c.ID

  LEFT JOIN pacientes b
  ON a.ID_PACIENTE = b.ID

  LEFT JOIN convenio d
  ON a.ID_CONVENIO = d.ID_CONVENIO

  LEFT JOIN tipo_patologia e
  ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

  LEFT JOIN patologia f
  ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

  
";
$qrPgrPabellones = $oirs->SelectLimit($query_qrPgrPabellones) or die($oirs->ErrorMsg());
$totalRows_qrPgrPabellones = $qrPgrPabellones->RecordCount();


$query_qrInterconsultas = "
    SELECT 
  a.N_DERIVACION,
  a.ID_DERIVACION,
  a.FOLIO,
  a.ESTADO_RN,
  b.COD_RUTPAC AS RUT_PACIENTE,
  b.NOMBRE AS NOMBRE_PACIENTE,
  d.DESC_CONVENIO,
  a.ESTADO,
  a.FECHA_DERIVACION,
  e.DESC_TIPO_PATOLOGIA,
  f.DESC_PATOLOGIA,
  c.NOMBRE AS NOMBRE_PROFESIONAL,
  a.MONTO_ACUMULADO_RN,
  api_interconsultas.ID_INGRESO,
  api_interconsultas.ESTADO,
  api_interconsultas.ESPECIALIDAD,
  api_interconsultas.FECHA_SOLICITUD,
  api_interconsultas.FECHA_FINALIZADA,
  api_interconsultas.PROFESIONAL,
  api_interconsultas.DEMORA
    
  FROM api_interconsultas
  
  LEFT JOIN derivaciones a
  ON api_interconsultas.ID_DERIVACION = a.ID_DERIVACION

  LEFT JOIN login c
  ON a.ENFERMERA = c.ID

  LEFT JOIN pacientes b
  ON a.ID_PACIENTE = b.ID

  LEFT JOIN convenio d
  ON a.ID_CONVENIO = d.ID_CONVENIO

  LEFT JOIN tipo_patologia e
  ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

  LEFT JOIN patologia f
  ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

  
";
$qrInterconsultas = $oirs->SelectLimit($query_qrInterconsultas) or die($oirs->ErrorMsg());
$totalRows_qrInterconsultas = $qrInterconsultas->RecordCount();


// ************************************************
$query_qrUrgenciaDiario = "
    SELECT 
  a.N_DERIVACION,
  a.ID_DERIVACION,
  a.FOLIO,
  a.ESTADO_RN,
  b.COD_RUTPAC AS RUT_PACIENTE,
  b.NOMBRE AS NOMBRE_PACIENTE,
  d.DESC_CONVENIO,
  a.ESTADO,
  a.FECHA_DERIVACION,
  e.DESC_TIPO_PATOLOGIA,
  f.DESC_PATOLOGIA,
  c.NOMBRE AS NOMBRE_PROFESIONAL,
  a.MONTO_ACUMULADO_RN,
  api_urgencias.id_urgencia,
  api_urgencias.fecha_admision,
  api_urgencias.area_atencion,
  api_urgencias.tipo_alta,
  api_urgencias.nombre_convenio,
  api_urgencias.ley_urgencia,
  api_urgencias.fecha_foto
    
  FROM api_urgencias
  
  LEFT JOIN derivaciones a
  ON api_urgencias.ID_DERIVACION = a.ID_DERIVACION

  LEFT JOIN login c
  ON a.ENFERMERA = c.ID

  LEFT JOIN pacientes b
  ON a.ID_PACIENTE = b.ID

  LEFT JOIN convenio d
  ON a.ID_CONVENIO = d.ID_CONVENIO

  LEFT JOIN tipo_patologia e
  ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

  LEFT JOIN patologia f
  ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

  WHERE
  a.ESTADO != 'cerrada' AND
  (a.ESTADO_RN = 'Prestador Asignado' or a.ESTADO_RN = 'Derivacion Aceptada' or a.ESTADO_RN = 'Solicita autorizacion') AND
  api_urgencias.fecha_registro = '$hoy'
";
$qrUrgenciaDiario = $oirs->SelectLimit($query_qrUrgenciaDiario) or die($oirs->ErrorMsg());
$totalRows_qrUrgenciaDiario = $qrUrgenciaDiario->RecordCount();


$query_qrOncoIcrs = "
  SELECT DISTINCT
    a.N_DERIVACION,
    a.ID_DERIVACION,
    b.COD_RUTPAC AS RUT_PACIENTE,
    b.NOMBRE AS NOMBRE_PACIENTE,
    d.DESC_CONVENIO,
    a.ESTADO,
    a.FECHA_DERIVACION,
    e.DESC_TIPO_PATOLOGIA,
    f.DESC_PATOLOGIA,
    c.NOMBRE AS NOMBRE_PROFESIONAL,
    a.MONTO_ACUMULADO_RN,
    a.ESTADO_RN,
    a.FOLIO,
    a.MONTO_DEVENGADO,
    a.DIAS_DESDE_CIRUGIA,
    p.QMT,
    p.DECRETO,
    g.NOMBRE as TENS,
    h.NOMBRE AS MEDICO

  FROM derivaciones a

  LEFT JOIN login c
    ON a.ENFERMERA = c.ID

  LEFT JOIN pacientes b
    ON a.ID_PACIENTE = b.ID

  LEFT JOIN convenio d
    ON a.ID_CONVENIO = d.ID_CONVENIO

  LEFT JOIN tipo_patologia e
    ON a.CODIGO_TIPO_PATOLOGIA = e.ID_TIPO_PATOLOGIA

  LEFT JOIN patologia f
    ON a.ID_PATOLOGIA = f.ID_PATOLOGIA

  LEFT JOIN login g
    ON a.TENS = g.ID

  LEFT JOIN login h
    ON a.RUT_PRESTADOR = h.ID

  LEFT JOIN derivaciones_canastas m
    ON a.ID_DERIVACION = m.ID_DERIVACION

  LEFT JOIN canasta_patologia p
    ON m.CODIGO_CANASTA_PATOLOGIA = p.CODIGO_CANASTA_PATOLOGIA

  WHERE
  
    f.ONCOLOGICO = 'si' and a.ESTADO <> 'cerrada' AND a.ESTADO_ANULACION <> 'anulado' AND
    (a.ESTADO_RN = 'Prestador Asignado' or a.ESTADO_RN = 'Derivacion Aceptada' or a.ESTADO_RN = 'Solicita autorizacion') AND
    p.ONCO='1' AND p.DECRETO='LEP2225'
"; 
$qrOncoIcrs = $oirs->SelectLimit($query_qrOncoIcrs) or die($oirs->ErrorMsg());
$totalRows_qrOncoIcrs = $qrOncoIcrs->RecordCount();





?>





<style>
  .color-palette {
    height: 35px;
    line-height: 35px;
    text-align: right;
    padding-right: .75rem;
  }

  .color-palette.disabled {
    text-align: center;
    padding-right: 0;
    display: block;
  }

  .color-palette-set {
    margin-bottom: 15px;
  }

  .color-palette span {
    display: none;
    font-size: 12px;
  }

  .color-palette:hover span {
    display: block;
  }

  .color-palette.disabled span {
    display: block;
    text-align: left;
    padding-left: .75rem;
  }

  .color-palette-box h4 {
    position: absolute;
    left: 1.25rem;
    margin-top: .75rem;
    color: rgba(255, 255, 255, 0.8);
    font-size: 12px;
    display: block;
    z-index: 7;
  }
</style>

<!DOCTYPE html>
<html>
    <body>
     <div class="card card-info">
        <div class="card-header">
          <h3 class="card-title">Derivaciones</h3>
          <div class="card-tools">
            <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
            <div class="row">
                  <!-- <div class="col-lg-3 col-6">
                    <div class="small-box bg-white">
                      <a href="#" class="small-box-footer"><h4> En construcción</h4><br> <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div> -->

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('prestador_asignado')"><h3><?php echo $totalRows_qrPrestadorAsignado ?></h3> Prestador Asignado <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <!-- <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('mas24HrsSinAgenda')"><h3><?php echo $totalRows_qrAlertaMenos24Hrs?></h3> Derivación sin primera agenda <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">                      
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('mas10DiasSinAtencion')"><h3><?php echo $totalRows_qrAlerta10DiasSinAtencion?></h3> Derivación 10 dias sin atención <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div> -->

                  <!-- <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('<?php echo $fechaLimite ?>')"><h3><?php echo $totalRows_qrPorVencer?></h3> Canastas por vencer <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div> -->

                  <!-- <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('<?php echo $hoy ?>','vencidas')"> <h3><?php echo $totalRows_qrVencecidas?></h3> Canastas Vencidas <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div> -->

                  <!-- <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('pendiente')"><h3><?php echo $totalRows_qrDerivacionesPendientes ?></h3> Pendientes <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>  -->
                  
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('aceptada')"><h3><?php echo $totalRows_qrAceptadas ?></h3> Derivaciones Aceptadas <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>
                  <!-- ./col -->
                  <!-- <div class="col-lg-2 col-6">
                    small box
                    <div class="small-box bg-success">
                      <div class="inner">
                        <h3><?php echo $totalRows_qrAsignadas ?></h3>

                        <p>Prestador</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-hospital-user"></i>
                      </div>
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('prestador')">Filtrar información <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div> -->

                   <!-- <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('cumplidas')"><h3><?php echo $totalRows_qrCumplidas ?></h3> Canastas cumplidas <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div> -->

                  <!-- <div class="col-sm-3 col-6">
                    <div class="small-box bg-danger">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('quirurgicas')"><h3><?php echo $totalRows_qrQuirurgicas ?></h3> Quirúrgicas <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div> --> 

                  <div class="col-sm-3 col-6">
                    <div class="small-box bg-info">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('solicita_autorizacion')"><h3><?php echo $totalRows_qrSolicitaAutorizacion ?></h3> Solicita autorización <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <!-- <a href="#" class="small-box-footer"><h4> En construcción </h4><br><i class="fas fa-arrow-circle-right"></i></a> -->
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaApiCenso('censo')"><h3><?php echo $totalRows_qrCensoDiario ?></h3>Censo diario  <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaEnPlazo('solicita_autorizacion_en_plazo')"><h3><?php echo $totalRows_qrSolicitaAutorizacionEnPlazo ?></h3> Solicita Autorización - En Plazo <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaRetrazado('solicita_autorizacion_retrasada')"><h3><?php echo $totalRows_qrSolicitaAutorizacionRetrasada ?></h3> Solicita Autorización - Retrasado <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>                                    

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaParaCierre('para_cierre')"><h3><?php echo $totalRows_qrParaCierre ?></h3> Para cierres <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <!-- ====================== -->

                
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <!-- <a href="#" class="small-box-footer"><h4>En construcción</h4><br><i class="fas fa-arrow-circle-right"></i></a> -->
                        <a href="#" class="small-box-footer" onclick="fnFiltraTablaApiPabellon('pabellon')"><h3><?php echo $totalRows_qrPabellonDiario ?></h3>Pabellon diario  <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                

                 <!--  <div class="col-lg-3 col-6">
                    <div class="small-box bg-white">
                        <a href="#" class="small-box-footer"><h4>En construcción</h4><br><i class="fas fa-arrow-circle-right"></i></a>
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaApiPabellon('pabellon')"><h3><?php echo $totalRows_qrPabellonDiario ?></h3>Pabellon diario  <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div> -->
                

                  <!-- ===================== -->

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('alta_paciente')"><h3><?php echo $totalRows_qrAltaPac ?></h3> Alta paciente <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('autorizado_para_pago')"><h3><?php echo $totalRows_qrAutorizadoPago ?></h3> Autorizado para pago <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('validado_para_pago')"><h3><?php echo $totalRows_qrParaPago ?></h3> Validado para pago <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  
                    <div class="col-lg-3 col-6">
                      <div class="small-box bg-info">
                          <!-- <a href="#" class="small-box-footer"><h4>En construcción</h4><br><i class="fas fa-arrow-circle-right"></i></a> -->
                          <a href="#" class="small-box-footer" onclick="fnFiltraTablaApiUrgencia('urgencia')"><h3><?php echo $totalRows_qrUrgenciaDiario ?></h3>Urgencia diario  <i class="fas fa-arrow-circle-right"></i></a>
                      </div>
                    </div>
                  

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-white">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('')"><h3><?php echo $totalRows_qrDerivacionesActivas ?></h3> Todas <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-white">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaOncologicosIcrs('oncoIcrs')"><h3><?php echo $totalRows_qrOncoIcrs ?></h3> Oncológicos <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <?php
                    if ($usuario == '99.999.999-9') {?>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                              <a href="#" class="small-box-footer" onclick="fnFiltraTablaApiPrgPabellones('prgPab')"><h3><?php echo $totalRows_qrPgrPabellones ?></h3>Programación Pabellón  <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                          </div>

                          <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                              <a href="#" class="small-box-footer" onclick="fnFiltraTablaApiInterconsultas('interconsultas')"><h3><?php echo $totalRows_qrInterconsultas ?></h3> Interconsultas <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    <?php }  ?>

                  
                  
                      <!-- <div class="col-sm-4 col-md-2">
                        <h4 class="text-center bg-purple">Purple</h4>
                        <div class="color-palette-set">
                          <div class="bg-purple color-palette"><span>#605ca8</span></div>
                          <div class="bg-purple disabled color-palette"><span>Disabled</span></div>
                        </div>
                      </div>

                      <div class="col-sm-4 col-md-2">
                        <h4 class="text-center bg-orange">Orange</h4>

                        <div class="color-palette-set">
                          <div class="bg-orange color-palette"><span>#ff851b</span></div>
                          <div class="bg-orange disabled color-palette"><span>Disabled</span></div>
                        </div>
                      </div>
                     
                      <div class="col-sm-4 col-md-2">
                        <h4 class="text-center bg-lime">Lime</h4>

                        <div class="color-palette-set">
                          <div class="bg-lime color-palette"><span>#01ff70</span></div>
                          <div class="bg-lime disabled color-palette"><span>Disabled</span></div>
                        </div>
                      </div> -->
                  
                      <!-- <a class="btn btn-app bg-success">
                        <span class="badge bg-purple">891</span>
                        <i class="fas fa-users"></i> Users
                      </a>
                      <a class="btn btn-app bg-danger">
                        <span class="badge bg-teal">67</span>
                        <i class="fas fa-inbox"></i> Orders
                      </a> -->

                      <!-- <a class="btn btn-app bg-warning" onclick="fnFiltraTablaDerivaciones('mas24HrsSinAgenda')">
                        <span class="badge bg-info"><?php echo $totalRows_qrAlertaMenos24Hrs?></span>
                        <i class="fas fa-exclamation-triangle"></i> D. sin 1era agenda
                      </a>

                      <a class="btn btn-app bg-warning" onclick="fnFiltraTablaDerivaciones('mas10DiasSinAtencion')">
                        <span class="badge bg-info"><?php echo $totalRows_qrAlerta10DiasSinAtencion?></span>
                        <i class="fas fa-exclamation-triangle"></i> D. 10 dias sin atención
                      </a>

                      <a class="btn btn-app bg-warning" onclick="fnFiltraTablaDerivaciones('<?php echo $fechaLimite ?>')">
                        <span class="badge bg-info"><?php echo $totalRows_qrPorVencer?></span>
                        <i class="fas fa-exclamation-triangle"></i> Canasta x vencer
                      </a>

                      <a class="btn btn-app bg-warning" onclick="fnFiltraTablaDerivaciones('<?php echo $hoy ?>','vencidas')">
                        <span class="badge bg-info"><?php echo $totalRows_qrVencecidas?></span>
                        <i class="fas fa-exclamation-triangle"></i> Canastas vencidas
                      </a> -->

                <div class="col-md-12" id="dvTablaDerivaciones">

                    </div>
                    </div>
            </div>
      </div>
    </body>
</html>

<script>
$('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
$('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivaciones.php');

function fnFiltraTablaOncologicosIcrs(estado){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesOncoICrs.php?estado=' + estado);
}

function fnFiltraTablaDerivaciones(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivaciones.php?estado=' + estado+'&vencidas='+vencidas);

}
function fnFiltraTablaDerivacionesPrestadorAsignado(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesPrestadorAsignado.php?estado=' + estado+'&vencidas='+vencidas);

}
function fnFiltraTablaDerivacionesAceptadas(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesAceptadas.php?estado=' + estado+'&vencidas='+vencidas);

}

function fnFiltraTablaApiCenso(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesApiCenso.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaApiPabellon(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesApiPabellon.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaApiPrgPabellones(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesApiPrgPabellones.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaApiInterconsultas(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesApiInterconsultas.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaApiUrgencia(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesApiUrgencia.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaParaCierre(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesParaCierre.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaRetrazado(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesRetrazado.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnFiltraTablaEnPlazo(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesEnPlazo.php?estado=' + estado+'&vencidas='+vencidas);
}

function fnfrmAceptarCaso(idDerivacion){
    $('#dvfrmAceptarCaso').load('vistas/modulos/aceptarCaso/frmAceptarCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmCerrarCaso(idDerivacion){
    $('#dvfrmCerrarCaso').load('vistas/modulos/cerrarCaso/frmCerrarCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmReasignarCaso(idDerivacion){
    $('#dvfrmReasignarCaso').load('vistas/modulos/reasignarCaso/frmReasignarCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmAsignarMedicoCaso(idDerivacion){
    $('#dvfrmAsignarMedicoCaso').load('vistas/modulos/asignarMedicoCaso/frmAsignarMedicoCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmBitacora(idDerivacion){
    $('#dvfrmBitacora').load('vistas/bitacora/modals/frmBitacora.php?idDerivacion=' + idDerivacion);
}

function fnfrmDetalleDerivacion(idDerivacion){
    $('#dvfrmDetalleDerivacion').load('vistas/modulos/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
}

function fnFiltraTablaDerivacionesCerradas(){
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesCerradas.php');
}

function fnFiltraTablaDerivacionesCumplidas(){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesCumplidas.php');
}

function fnFrmEditaInformacionPacienteSupervisora(idDerivacion){
    $('#dvFrmEditaInformacionPacienteSupervisora').load('vistas/modulos/informacionPaciente/frmEditaInformacionPacienteSupervisora.php?idDerivacion=' + idDerivacion);
}

function fnfrmAsignarTeamGestion(idDerivacion){
    $('#dvfrmAsignarTeamGestion').load('vistas/modulos/asignarTeamGestion/frmAsignarTeamGestion.php?idDerivacion=' + idDerivacion);
}

function fnfrmContactarPaciente(idDerivacion){
    $('#dvfrmContactarPaciente').load('vistas/modulos/contactarPaciente/frmContactarPaciente.php?idDerivacion=' + idDerivacion);
}

function fnfrmAsignarCita(idDerivacion){
    $('#dvfrmAsignarCita').load('vistas/modulos/asignarCita/frmAsignarCita.php?idDerivacion=' + idDerivacion);
}

function fnfrmAtenderPaciente(idDerivacion){
    $('#dvfrmAtenderPaciente').load('vistas/modulos/atenderPaciente/frmAtenderPaciente.php?idDerivacion=' + idDerivacion);
}

function fnfrmAsignarPatologiaEtapaCanasta(idDerivacion){
    $('#dvfrmAsignarPatologiaEtapaCanasta').load('vistas/modulos/asignarPatologiaEtapaCanasta/frmAsignarPatologiaEtapaCanasta.php?idDerivacion=' + idDerivacion); 
}

function fnfrmAsignarCaso(idDerivacion){
    $('#dvfrmAsignarCaso').load('vistas/modulos/asignarCaso/frmAsignarCaso.php?idDerivacion=' + idDerivacion);
}

function fnfrmAgregarMarca(idDerivacion){
    $('#dvfrmAgregarMarca').load('vistas/modulos/agregarMarca/frmAgregarMarca.php?idDerivacion=' + idDerivacion);
}

function fnfrmValidarRechazar(idDerivacion){
    $('#dvfrmValidarRechazar').load('vistas/modulos/validarRechazarParaGesCenso/frmValidarRechazar.php?idDerivacion=' + idDerivacion);
}

function fnfrmValidarRechazarPab(idDerivacion){
    $('#dvfrmValidarRechazarPab').load('vistas/modulos/validarRechazarParaGesPab/frmValidarRechazarPab.php?idDerivacion=' + idDerivacion);
}

function fnfrmValidarRechazarUrg(idDerivacion, idUrg){
    $('#dvfrmValidarRechazarUrg').load('vistas/modulos/validarRechazarParaGesUrg/frmValidarRechazarUrg.php?idDerivacion=' + idDerivacion + '&idUrg=' + idUrg);
}

function fnfrmMotivoVencidaNoFinalizada(idDerivacion){
    $('#dvfrmMotivoVencidaNoFinalizada').load('vistas/modulos/motivoVencidaNoFinalizada/frmMotivoVencidaNoFinalizada.php?idDerivacion=' + idDerivacion);

}
function fnfrmMotRetPabPrg(idPrgPab){
    $('#dvfrmMotRetPabPrg').load('vistas/modulos/motRetPabPrg/frmMotRetPabPrg.php?idPrgPab=' + idPrgPab);  
}



</script>