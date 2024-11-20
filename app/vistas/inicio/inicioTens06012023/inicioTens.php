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

$query_qrPorVencer = "

  SELECT 
  a.ID_DERIVACION

  FROM derivaciones a

  LEFT JOIN derivaciones_canastas h
  ON a.ID_DERIVACION = h.ID_DERIVACION 

  LEFT JOIN team_gestion j
  ON a.ID_DERIVACION = j.ID_DERIVACION

  LEFT JOIN login k
  ON j.ID_PROFESIONAL = k.ID

  WHERE
  a.ESTADO != 'cerrada' AND
  h.FECHA_LIMITE<='$fechaLimite' AND
  h.FECHA_LIMITE>='$hoy' AND
  h.FECHA_LIMITE!='0000-00-00' AND
  h.DIAS_LIMITE!='0' AND
  a.CODIGO_TIPO_PATOLOGIA = '1' AND
  h.ESTADO != 'finalizada'

";
$qrPorVencer = $oirs->SelectLimit($query_qrPorVencer) or die($oirs->ErrorMsg());
$totalRows_qrPorVencer = $qrPorVencer->RecordCount();

$query_qrVencecidas = "
  SELECT 
  a.ID_DERIVACION

  FROM derivaciones a

  LEFT JOIN derivaciones_canastas h
  ON a.ID_DERIVACION = h.ID_DERIVACION 

  LEFT JOIN team_gestion j
  ON a.ID_DERIVACION = j.ID_DERIVACION

  LEFT JOIN login k
  ON j.ID_PROFESIONAL = k.ID

  WHERE
  a.ESTADO != 'cerrada' AND
  h.FECHA_LIMITE<='$hoy' AND
  h.FECHA_LIMITE>='0000-00-00' AND
  h.DIAS_LIMITE!='0' AND
  a.CODIGO_TIPO_PATOLOGIA = '1' AND
  h.ESTADO != 'finalizada'
";
$qrVencecidas = $oirs->SelectLimit($query_qrVencecidas) or die($oirs->ErrorMsg());
$totalRows_qrVencecidas = $qrVencecidas->RecordCount();

$query_qrCumplidas = "
  SELECT 
  DISTINCT a.N_DERIVACION

  FROM derivaciones a

  LEFT JOIN derivaciones_canastas h
  ON a.ID_DERIVACION = h.ID_DERIVACION 

  LEFT JOIN team_gestion
  ON a.ID_DERIVACION = team_gestion.ID_DERIVACION
   
   LEFT JOIN login
  ON team_gestion.ID_PROFESIONAL = login.ID

  WHERE

  a.CODIGO_TIPO_PATOLOGIA = '1' AND
  h.ESTADO = 'finalizada'
";
$qrCumplidas = $oirs->SelectLimit($query_qrCumplidas) or die($oirs->ErrorMsg());
$totalRows_qrCumplidas = $qrCumplidas->RecordCount();

$query_qrDerivaciones = "
  SELECT 
  a.N_DERIVACION

  FROM derivaciones a

  LEFT JOIN team_gestion h
  ON a.ID_DERIVACION = h.ID_DERIVACION

  LEFT JOIN login i
  ON h.ID_PROFESIONAL = i.ID

  WHERE
  a.ESTADO != 'cerrada'
";
$qrDerivaciones = $oirs->SelectLimit($query_qrDerivaciones) or die($oirs->ErrorMsg());
$totalRows_qrDerivaciones = $qrDerivaciones->RecordCount();

$query_qrDerivadas = "
  SELECT 
  a.ID_DERIVACION

  FROM derivaciones a

  LEFT JOIN team_gestion
  ON a.ID_DERIVACION = team_gestion.ID_DERIVACION
    
  LEFT JOIN login
  ON team_gestion.ID_PROFESIONAL = login.ID

  WHERE
  a.ESTADO != 'cerrada' AND
  a.ESTADO = 'pendiente' AND
  login.ID = '$idUsuario' AND
  login.ID_PRESTADOR = '$idClinica'
    
  group by a.ID_DERIVACION
";
$qrDerivadas = $oirs->SelectLimit($query_qrDerivadas) or die($oirs->ErrorMsg());
$totalRows_qrDerivadas = $qrDerivadas->RecordCount();

$query_qrAceptadas = "
  SELECT 
  a.N_DERIVACION

  FROM derivaciones a
    
  LEFT JOIN team_gestion
  ON a.ID_DERIVACION = team_gestion.ID_DERIVACION
    
  LEFT JOIN login
  ON team_gestion.ID_PROFESIONAL = login.ID

  WHERE
  a.ESTADO != 'cerrada' AND
  a.ESTADO = 'aceptada'

";
$qrAceptadas = $oirs->SelectLimit($query_qrAceptadas) or die($oirs->ErrorMsg());
$totalRows_qrAceptadas = $qrAceptadas->RecordCount();

//se evalua que la derivacion este en uno de los 3 estados iniciales (pendiente,aceptada,prestador), por que en estado prestador se puede agendar la primera consulta de esta forma sabemos si se no se ha agendado la primera consulta, ya que elestado primeraConsultaAgendada es el que viene despues del estado prestador, ademas consultamos si la fecha de la derivacion es menor a la fecha de $hoy es por que tiene mas de 24 horas la derivacion en sistema sin agendar primera consulta.
$query_qrAlertaMenos24Hrs = "
      SELECT 
  DISTINCT a.N_DERIVACION

  FROM derivaciones a
  
  LEFT JOIN estados_derivacion
  ON a.ID_DERIVACION = estados_derivacion.ID_DERIVACION

  LEFT JOIN team_gestion h
  ON a.ID_DERIVACION = h.ID_DERIVACION

  LEFT JOIN login i
  ON h.ID_PROFESIONAL = i.ID

  WHERE 
  a.ESTADO != 'cerrada' AND
  (estados_derivacion.ESTADO != 'primeraConsultaAgendada' or a.FECHA_DERIVACION <= '$hoy') AND  
  (a.ESTADO = 'pendiente' OR  a.ESTADO = 'aceptada' OR a.ESTADO = 'prestador') AND
  a.ESTADO != 'cerrada' AND
a.FECHA_DERIVACION >= '2022-12-20'
";
$qrAlertaMenos24Hrs = $oirs->SelectLimit($query_qrAlertaMenos24Hrs) or die($oirs->ErrorMsg());
$totalRows_qrAlertaMenos24Hrs = $qrAlertaMenos24Hrs->RecordCount();


//se evalua que la derivacion este en uno de los 3 estados iniciales (pendiente,aceptada,prestador), por que en estado prestador se puede agendar la primera consulta de esta forma sabemos si se no se ha agendado la primera consulta, ya que elestado primeraConsultaAgendada es el que viene despues del estado prestador, ademas consultamos si la fecha de la derivacion es menor a la fecha de $hoy es por que tiene mas de 24 horas la derivacion en sistema sin agendar primera consulta.
$query_qrAlerta10DiasSinAtencion = "
  SELECT 
  DISTINCT a.N_DERIVACION

  FROM derivaciones a

  LEFT JOIN estados_derivacion
  ON a.ID_DERIVACION = estados_derivacion.ID_DERIVACION

  LEFT JOIN team_gestion h
  ON a.ID_DERIVACION = h.ID_DERIVACION

  LEFT JOIN login i
  ON h.ID_PROFESIONAL = i.ID

  WHERE
  (estados_derivacion.ESTADO != 'primeraConsultaAtendida' or '$hoy' <= date_add(a.FECHA_DERIVACION, interval 10 day)) AND
  (a.ESTADO = 'pendiente' OR  a.ESTADO = 'aceptada' OR a.ESTADO = 'prestador' OR a.ESTADO = 'primeraConsultaAgendada') AND
  a.ESTADO != 'cerrada' AND
a.FECHA_DERIVACION >= '2022-12-20'
";
$qrAlerta10DiasSinAtencion = $oirs->SelectLimit($query_qrAlerta10DiasSinAtencion) or die($oirs->ErrorMsg());
$totalRows_qrAlerta10DiasSinAtencion = $qrAlerta10DiasSinAtencion->RecordCount();


//cuenta cuantas derivaciones son quirurgicas
$query_qrQuirurgicas = "
    SELECT 
       derivaciones.ID_DERIVACION

        FROM derivaciones

        LEFT JOIN atenciones
        ON derivaciones.ID_DERIVACION = atenciones.ID_DERIVACION
        
        LEFT JOIN events
        ON atenciones.ID_CITACION = events.id

        LEFT JOIN team_gestion
        ON derivaciones.ID_DERIVACION = team_gestion.ID_DERIVACION
 
        LEFT JOIN login
        ON team_gestion.ID_PROFESIONAL = login.ID
        
        WHERE
        events.TIPO_ATENCION = 'segundaConsulta'
";
$qrQuirurgicas = $oirs->SelectLimit($query_qrQuirurgicas) or die($oirs->ErrorMsg());
$totalRows_qrQuirurgicas = $qrQuirurgicas->RecordCount();


$query_qrParaPago = "
    SELECT 
    derivaciones.ID_DERIVACION 

    FROM derivaciones 

    LEFT JOIN login
    ON derivaciones.SESION = login.ID

    WHERE
    login.ID_PRESTADOR = '$idClinica' AND 
    derivaciones.ESTADO_RN = 'Validado para Pago'
";
$qrParaPago = $oirs->SelectLimit($query_qrParaPago) or die($oirs->ErrorMsg());
$totalRows_qrParaPago = $qrParaPago->RecordCount();


$query_qrAltaPac = "
    SELECT 
    derivaciones.ID_DERIVACION 

    FROM derivaciones 

    LEFT JOIN login
    ON derivaciones.SESION = login.ID

    WHERE
    login.ID_PRESTADOR = '$idClinica' AND     
    derivaciones.ESTADO_RN = 'Alta Paciente'
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
    login.ID_PRESTADOR = '$idClinica' AND 
    derivaciones.ESTADO_RN = 'Autorizado para pago'
";
$qrAutorizadoPago = $oirs->SelectLimit($query_qrAutorizadoPago) or die($oirs->ErrorMsg());
$totalRows_qrAutorizadoPago = $qrAutorizadoPago->RecordCount();

$query_qrParaCierre = "
    SELECT 
  a.ID_DERIVACION

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
  a.ESTADO != 'cerrada' AND
  a.MARCA = 'para_cierre'
";
$qrParaCierre = $oirs->SelectLimit($query_qrParaCierre) or die($oirs->ErrorMsg());
$totalRows_qrParaCierre = $qrParaCierre->RecordCount();

$query_qrSolicitaAutorizacion = "
    SELECT 
  a.ID_DERIVACION

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
  a.ESTADO != 'cerrada' AND
  a.ESTADO_RN = 'Solicita autorizacion'
";
$qrSolicitaAutorizacion = $oirs->SelectLimit($query_qrSolicitaAutorizacion) or die($oirs->ErrorMsg());
$totalRows_qrSolicitaAutorizacion = $qrSolicitaAutorizacion->RecordCount();

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
  '$hoy' >= date_add(a.FECHA_DERIVACION, interval f.DIAS_VIGENCIA day) AND
  a.ESTADO != 'cerrada' AND
  a.ESTADO_RN = 'Solicita autorizacion' AND
  a.FECHA_DERIVACION >= '2022-12-20'
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
  '$hoy' < date_add(a.FECHA_DERIVACION, interval f.DIAS_VIGENCIA day) AND
  a.ESTADO != 'cerrada' AND
  a.ESTADO_RN = 'Solicita autorizacion' AND
  a.FECHA_DERIVACION >= '2022-12-20'
";
$qrSolicitaAutorizacionRetrasada = $oirs->SelectLimit($query_qrSolicitaAutorizacionRetrasada) or die($oirs->ErrorMsg());
$totalRows_qrSolicitaAutorizacionRetrasada = $qrSolicitaAutorizacionRetrasada->RecordCount();

?>

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
                <div class="col-lg-3 col-6">
                  <div class="small-box bg-warning">
                    <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('mas24HrsSinAgenda')"><h3><?php echo $totalRows_qrAlertaMenos24Hrs?></h3> Derivación sin primera agenda <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>

                <div class="col-lg-3 col-6">
                  <div class="small-box bg-warning">                      
                    <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('mas10DiasSinAtencion')"><h3><?php echo $totalRows_qrAlerta10DiasSinAtencion?></h3> Derivación 10 dias sin atención <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>

                <div class="col-lg-3 col-6">
                  <div class="small-box bg-warning">
                    <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('<?php echo $fechaLimite ?>')"><h3><?php echo $totalRows_qrPorVencer?></h3> Canastas por vencer <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>

                <div class="col-lg-3 col-6">
                  <div class="small-box bg-warning">
                    <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('<?php echo $hoy ?>','vencidas')"> <h3><?php echo $totalRows_qrVencecidas?></h3> Canastas Vencidas <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>

                <div class="col-lg-3 col-6">
                  <div class="small-box bg-success">
                    <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('pendiente')"><h3><?php echo $totalRows_qrDerivadas ?></h3> Pendientes <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div> 
                
                <div class="col-lg-3 col-6">
                  <div class="small-box bg-info">
                    <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('aceptada')"><h3><?php echo $totalRows_qrAceptadas ?></h3> Aceptadas <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>

                 <div class="col-lg-3 col-6">
                  <div class="small-box bg-success">
                    <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('cumplidas')"><h3><?php echo $totalRows_qrCumplidas ?></h3> Canastas cumplidas <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>

                <!-- <div class="col-sm-3 col-6">
                  <div class="small-box bg-danger">
                    <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('quirurgicas')"><h3><?php echo $totalRows_qrQuirurgicas ?></h3> Quirúrgicas <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>  -->  

                <div class="col-sm-3 col-6">
                    <div class="small-box bg-danger">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('solicita_autorizacion')"><h3><?php echo $totalRows_qrSolicitaAutorizacion ?></h3> Solicita autorización <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>               

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
                  <div class="small-box bg-white">
                    <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('')"><h3><?php echo $totalRows_qrDerivaciones ?></h3> Activas <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>

                <div class="col-lg-3 col-6">
                  <div class="small-box bg-info">
                    <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('para_cierre')"><h3><?php echo $totalRows_qrParaCierre ?></h3> Para cierres <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                   <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('pendiente')"><h3><?php echo $totalRows_qrDerivadas ?></h3> Pendientes <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div> 
                  
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('aceptada')"><h3><?php echo $totalRows_qrAceptadas ?></h3> Aceptadas <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>
                   <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('solicita_autorizacion_en_plazo')"><h3><?php echo $totalRows_qrSolicitaAutorizacionEnPlazo ?></h3> Solicita Autorización - En Plazo <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <a href="#" class="small-box-footer" onclick="fnFiltraTablaDerivaciones('solicita_autorizacion_retrasada')"><h3><?php echo $totalRows_qrSolicitaAutorizacionRetrasada ?></h3> Solicita Autorización - Retrasado <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>
                
                <div class="col-md-12" id="dvTablaDerivaciones">

                    </div>
                    </div>
            </div>
      </div>
    </body>
</html>

<script>
$('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
$('#dvTablaDerivaciones').load('vistas/inicio/inicioTens/tablaDerivaciones.php');

function fnFiltraTablaDerivaciones(estado,vencidas){
    $('#dvTablaDerivaciones').html('<img src="images/loading.gif"/>');
    $('#dvTablaDerivaciones').load('vistas/inicio/inicioTens/tablaDerivaciones.php?estado=' + estado+'&vencidas='+vencidas);
}


function fnfrmBitacora(idDerivacion){
    $('#dvfrmBitacora').load('vistas/bitacora/modals/frmBitacora.php?idDerivacion=' + idDerivacion);
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

function fnfrmAgregarMarca(idDerivacion){
    $('#dvfrmAgregarMarca').load('vistas/modulos/agregarMarca/frmAgregarMarca.php?idDerivacion=' + idDerivacion);
}

// function fnfrmAsignarPrestadorCaso(idDerivacion){
//   $('#dvfrmAsignarPrestadorCaso').load('vistas/modulos/asignarPrestadorCaso/frmAsignarPrestadorCaso.php?idDerivacion=' + idDerivacion);
// }



</script>