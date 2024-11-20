<?php
require_once '../Connections/oirs.php';
require_once '../Connections/crss.php';
require_once '../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../index.php');
exit; }

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');

//busco si existe rut paciente en mantenedor paciente de lado prestador
$query_qrBuscaDerivacionesCliStgo = "SELECT * FROM $MM_crss_DATABASE.derivaciones WHERE RUT_PRESTADOR='19'";
$qrBuscaDerivacionesCliStgo = $crss->SelectLimit($query_qrBuscaDerivacionesCliStgo) or die($crss->ErrorMsg());
$totalRows_qrBuscaDerivacionesCliStgo = $qrBuscaDerivacionesCliStgo->RecordCount(); 


//recojemos variables que vienen de REDGES
$usuarioRedGes = '99.999.999-9';
$idDerivacionPp = $_POST['idDerivacionPp'];
$codTipoPatologia = $_POST['codTipoPatologia'];
$codRutPac = $_POST['codRutPac'];
$idConvenio = $_POST['idConvenio'];
$codPatologia = $_POST['codPatologia'];
$enfermera = $_POST['enfermera'];
$fecha_derivacion = $_POST['fecha_derivacion'];
$codEtapaPatologia = $_POST['codEtapaPatologia'];
$codCanastaPatologia = $_POST['codCanastaPatologia'];
$fechaCanasta = $_POST['fechaCanastaInicial'];
$idTablaCanastaPatologia = $_POST['idTablaCanastaPatologia'];
$idTablaEtapaPatologia = $_POST['idTablaEtapaPatologia'];
$prestador = $_POST['prestador'];
$idPatologia = $_POST['idPatologia'];
$prestadorOrigen=49;


//declaro variables locales   
$estado = 'pendiente';//variable que declara estado inicial de derivacion del lado del prestador
$auditoria= date('Y-m-d');

//busco si existe rut paciente en mantenedor paciente de lado prestador
$query_qrBuscaPaciente = "SELECT * FROM $MM_crss_DATABASE.pacientes WHERE COD_RUTPAC='$codRutPac'";
$qrBuscaPaciente = $crss->SelectLimit($query_qrBuscaPaciente) or die($crss->ErrorMsg());
$totalRows_qrBuscaPaciente = $qrBuscaPaciente->RecordCount(); 

//Consulto si paciente ya existe en mantenedor local de prestador
if ($totalRows_qrBuscaPaciente == 0) {
    # paciente no existe por lo tanto insertara paciente en mantenedor de pacientes lado prestador
    //recibo datos para insertar en tabla pacientes lado prestador
    $nombrePaciente = $_POST['nombrePaciente'];
    $nacimientoPaciente = $_POST['nacimientoPaciente'];
    $fonoPaciente = $_POST['fonoPaciente'];
    $direccionPaciente = $_POST['direccionPaciente'];
    $regionPaciente = $_POST['regionPaciente'];
    $provinciaPaciente = $_POST['provinciaPaciente'];
    $comunaPaciente = $_POST['comunaPaciente'];
    $mailPaciente = $_POST['mailPaciente'];
    $ocupacionPaciente = $_POST['ocupacionPaciente'];
    $previsionPaciente = $_POST['previsionPaciente'];
    $planSaludPaciente = $_POST['planSaludPaciente'];
    $seguroComplementarioPaciente = $_POST['seguroComplementarioPaciente'];
    $companiaSeguroPaciente = $_POST['companiaSeguroPaciente'];
    $sexo = $_POST['sexo'];

    $insertSQL = sprintf("INSERT INTO $MM_crss_DATABASE.pacientes (COD_RUTPAC, NOMBRE, SEXO, FEC_NACIMI, FONO, DIRECCION, REGION, PROVINCIA, COMUNA, MAIL, OCUPACION, PREVISION, PLAN_SALUD, SEGURO_COMPLEMENTARIO, COMPANIA_SEGURO, RUT_SESION, AUDITORIA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($codRutPac, "text"), 
        GetSQLValueString(utf8_decode($nombrePaciente), "text"),
        GetSQLValueString(utf8_decode($sexo), "text"),
        GetSQLValueString($nacimientoPaciente, "date"),
        GetSQLValueString($fonoPaciente, "text"),
        GetSQLValueString(utf8_decode($direccionPaciente), "text"),
        GetSQLValueString(utf8_decode($regionPaciente), "int"),
        GetSQLValueString(utf8_decode($provinciaPaciente), "int"),
        GetSQLValueString(utf8_decode($comunaPaciente), "int"),
        GetSQLValueString(utf8_decode($mailPaciente), "text"),
        GetSQLValueString(utf8_decode($ocupacionPaciente), "text"),
        GetSQLValueString(utf8_decode($previsionPaciente), "text"),
        GetSQLValueString(utf8_decode($planSaludPaciente), "text"),
        GetSQLValueString(utf8_decode($seguroComplementarioPaciente), "text"),
        GetSQLValueString(utf8_decode($companiaSeguroPaciente), "text"),
        GetSQLValueString($usuario, "text"),
        GetSQLValueString($auditoria, "text"));
    $Result1 = $crss->Execute($insertSQL) or die($crss->ErrorMsg());
   
}


//busco si existe rut paciente en mantenedor paciente de lado prestador
$query_qrBuscaPaciente2 = "SELECT * FROM $MM_crss_DATABASE.pacientes WHERE COD_RUTPAC='$codRutPac'";
$qrBuscaPaciente2 = $crss->SelectLimit($query_qrBuscaPaciente2) or die($crss->ErrorMsg());
$totalRows_qrBuscaPaciente2 = $qrBuscaPaciente2->RecordCount(); 

$idPaciente = $qrBuscaPaciente2->Fields('ID');

    # paciente existe por lo tanto solo insertara la derivacion
    //Inserto derivacion asignada desde redges
    $insertSQL = sprintf("INSERT INTO $MM_crss_DATABASE.derivaciones_pp (ID_DERIVACION_PP, CODIGO_TIPO_PATOLOGIA, COD_RUTPAC,ID_PACIENTE, ID_CONVENIO,CODIGO_ETAPA_PATOLOGIA,CODIGO_CANASTA_PATOLOGIA,RUT_PRESTADOR, CODIGO_PATOLOGIA, ID_PATOLOGIA,  ESTADO, AUDITORIA, SESION, FECHA_DERIVACION,PRESTADOR_ORIGEN) VALUES (%s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacionPp, "int"),
    GetSQLValueString($codTipoPatologia, "int"),
    GetSQLValueString($codRutPac, "text"),
    GetSQLValueString($idPaciente, "int"),
    GetSQLValueString($idConvenio, "int"),
    GetSQLValueString($codEtapaPatologia, "text"),
    GetSQLValueString($codCanastaPatologia, "text"),
    GetSQLValueString($prestador, "int"),
    GetSQLValueString($codPatologia, "text"),   
    GetSQLValueString($idPatologia, "text"),   
    GetSQLValueString($estado, "text"),
    GetSQLValueString($auditoria, "text"),
    GetSQLValueString($usuarioRedGes, "text"),
    GetSQLValueString($fecha_derivacion, "date"),
    GetSQLValueString($prestadorOrigen, "int"));
    $Result1 = $crss->Execute($insertSQL) or die($crss->ErrorMsg()); 

    //consulto por ultima derivacion asignada para crear numero de derivacion con formato de folio D... 
    $query_select = ("SELECT max(ID_DERIVACION) as ID_DERIVACION FROM $MM_crss_DATABASE.derivaciones_pp");
    $select = $crss->SelectLimit($query_select) or die($crss->ErrorMsg());
    $totalRows_select = $select->RecordCount();

    $idDerivacion = $select->Fields('ID_DERIVACION');
    $nderivacion = 'P0'.$idDerivacion;

    //Coloco numero de derivacion con formato de folio
    $updateSQL = sprintf("UPDATE $MM_crss_DATABASE.derivaciones_pp SET N_DERIVACION=%s WHERE ID_DERIVACION= '$idDerivacion'",
                GetSQLValueString($nderivacion, "text"));
    $Result1 = $crss->Execute($updateSQL) or die($crss->ErrorMsg());


    // if ($codTipoPatologia == 1) {

    // inserta la primera etapa seleccionada para la derivacion
        $insertSQL = sprintf("INSERT INTO $MM_crss_DATABASE.derivaciones_etapas_pp (ID_DERIVACION, N_DERIVACION, CODIGO_ETAPA_PATOLOGIA, SESION, AUDITORIA) VALUES (%s, %s, %s, %s, %s)",        
        GetSQLValueString($idDerivacion, "int"), 
        GetSQLValueString($nderivacion, "text"),         
        GetSQLValueString($codEtapaPatologia, "text"), 
        GetSQLValueString($usuario, "text"),
        GetSQLValueString($auditoria, "text"));
    $Result1 = $crss->Execute($insertSQL) or die($crss->ErrorMsg());

    $query_select2 = ("SELECT max(ID_ETAPA_PATOLOGIA) as ID_ETAPA_PATOLOGIA FROM $MM_crss_DATABASE.derivaciones_etapas_pp");
    $select2 = $crss->SelectLimit($query_select2) or die($crss->ErrorMsg());
    $totalRows_select2 = $select2->RecordCount();

    $idEtapaPatologia = $select2->Fields('ID_ETAPA_PATOLOGIA');

    //inicio proceso para insertar canasta inicial a tabla derivaciones_canastas
    $query_qrCanastaPatologia = "SELECT * FROM $MM_crss_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA='$codCanastaPatologia'";
    $qrCanastaPatologia = $crss->SelectLimit($query_qrCanastaPatologia) or die($crss->ErrorMsg());
    $totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount(); 

    //obtengo dias limite de la canasta seleccionada para guardarlo en tabla de derivaciones_canastas
    $diasLimite = $qrCanastaPatologia->Fields('TIEMPO_LIMITE');


    if ($diasLimite==null or $diasLimite==0) {
       $fechaLimite = '0000-00-00';
    }else{
       //obtengo fecha limite de la canasta para guardarla en derivaciones_canastas
       $fechaLimite = date("Y-m-d",strtotime($fechaCanasta."+ $diasLimite days"));
    }

    // inserta la primera canasta asociada a la etapa seleccionada para la derivacion
        $insertSQL = sprintf("INSERT INTO $MM_crss_DATABASE.derivaciones_canastas_pp ( CODIGO_ETAPA_PATOLOGIA, ID_ETAPA_PATOLOGIA, ID_DERIVACION, N_DERIVACION,CODIGO_CANASTA_PATOLOGIA, FECHA_CANASTA, DIAS_LIMITE, FECHA_LIMITE, SESION, INICIAL, AUDITORIA, RUT_PRESTADOR) VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",       
        GetSQLValueString($codEtapaPatologia, "text"), 
        GetSQLValueString($idEtapaPatologia, "int"), 
        GetSQLValueString($idDerivacion, "text"), 
        GetSQLValueString($nderivacion, "text"),        
        GetSQLValueString($codCanastaPatologia, "text"), 
        GetSQLValueString($fechaCanasta, "date"), 
        GetSQLValueString($diasLimite, "int"),// tiempo limite de la canasta seleccionada lo obtengo arriba52741363
        GetSQLValueString($fechaLimite, "date"), // fecha limite la traigo calculada de archivo frmDerivacion
        GetSQLValueString($usuario, "text"),
        GetSQLValueString("si", "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($prestador, "int"));
    $Result1 = $crss->Execute($insertSQL) or die($crss->ErrorMsg());
// }//fin if si es GES


    //inserto bitacora
    $comentarioBitacora = 'Se asigna derivacion numero '.$nderivacion.' desde sistema UGC';
    $asunto= 'Derivacion UGC';
    $hora= date('G:i');

    //obtengo el ultimo id de bitacora del mensaje recien creado al crear nueva canasta seleccionando a crss como prestador
    $query_qrUltimaBitacora = ("SELECT max(ID_BITACORA) as ID_BITACORA FROM $MM_oirs_DATABASE.bitacora");
    $qrUltimaBitacora = $oirs->SelectLimit($query_qrUltimaBitacora) or die($oirs->ErrorMsg());
    $totalRows_qrUltimaBitacora = $qrUltimaBitacora->RecordCount();

    $ultimaBitacora = $qrUltimaBitacora->Fields('ID_BITACORA');

    $insertSQL = sprintf("INSERT INTO $MM_crss_DATABASE.bitacora_pp (ID_DERIVACION, SESION, ID_DERIVACION_PRESTADOR,ID_CANASTA_PATOLOGIA, BITACORA, ASUNTO, AUDITORIA, HORA,ID_BITACORA_REMOTO) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($idDerivacion, "int"), 
        GetSQLValueString('ICRS', "text"), 
        GetSQLValueString($idDerivacionPp, "int"), 
        GetSQLValueString($codCanastaPatologia, "int"), 
        GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
        GetSQLValueString($asunto, "text"),
        GetSQLValueString($auditoria, "date"),
        GetSQLValueString($hora, "date"),
         GetSQLValueString($ultimaBitacora, "text"));
    $Result1 = $crss->Execute($insertSQL) or die($crss->ErrorMsg());


    //Inserto notificación
    $asunto = 'Derivacion Asignada UGC ('.$nderivacion.')';
    $estadoNoti = 'nuevo';

    //busco los perfiles de coordinador para asignarles la notificacion de nueva derivacion
    $query_qrBuscaCoordinador = "SELECT * FROM $MM_crss_DATABASE.login WHERE (TIPO='2' or TIPO='3')";
    $qrBuscaCoordinador = $crss->SelectLimit($query_qrBuscaCoordinador) or die($crss->ErrorMsg());
    $totalRows_qrBuscaCoordinador = $qrBuscaCoordinador->RecordCount(); 

    while (!$qrBuscaCoordinador->EOF) {
        $receptor = $qrBuscaCoordinador->Fields('USUARIO');
        $insertSQL2 = sprintf("INSERT INTO $MM_crss_DATABASE.notificaciones_pp (USUARIO,ID_PRESTADOR, ASUNTO, MENSAJE, FECHA, HORA, ESTADO) VALUES (%s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($receptor, "text"),
            GetSQLValueString($prestador, "int"), 
            GetSQLValueString($asunto, "text"),
            GetSQLValueString(utf8_decode($comentarioBitacora), "text"),
            GetSQLValueString($auditoria, "date"),
            GetSQLValueString($hora, "date"),
            GetSQLValueString($estadoNoti, "text"));
        $Result2 = $crss->Execute($insertSQL2) or die($crss->ErrorMsg());
    $qrBuscaCoordinador->MoveNext(); }
    //***********************************************************************************************
    



echo 1;
        
?>