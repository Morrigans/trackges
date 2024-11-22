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

$usuario = $_SESSION['dni'];
$tipoUsuario = $_SESSION['tipoUsuario'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');
$hora= date('G:i');

$idDerivacion = $_POST['idDerivacion'];
$comentarioBitacora = $_POST['comentarioBitacora'];
$asunto= $_POST['slTipoRegistroBitacora'];
$comparte= 'no';


$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.bitacora (ID_DERIVACION, SESION, BITACORA, ASUNTO, AUDITORIA, HORA, COMPARTIDO_EXT) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($comparte, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$query_select = ("SELECT max(ID_BITACORA) as ID_BITACORA FROM $MM_oirs_DATABASE.bitacora");
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();

$idBitacora = $select->Fields('ID_BITACORA');


if ($asunto == 'Solicita gestión' or $asunto == 'Solicita Información') {
   
    //Inserto notificación
    $estadoNoti = 'nuevo';

    if ($tipoUsuario == '3') { // si es tipo gestora, notificacion llega a su team de gestion (administrativa y tens)
        //busco los perfiles de team para asignarles la notificacion de nueva derivacion
        $query_qrBuscaTeam = "

            SELECT 
            login.USUARIO

            FROM `derivaciones` 

            LEFT JOIN team_gestion
                ON derivaciones.ID_DERIVACION = team_gestion.ID_DERIVACION
                
            LEFT JOIN login
                ON team_gestion.ID_PROFESIONAL = login.ID

            WHERE 
            derivaciones.ID_DERIVACION = '$idDerivacion'

        ";
        $qrBuscaTeam = $oirs->SelectLimit($query_qrBuscaTeam) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaTeam = $qrBuscaTeam->RecordCount(); 

        while (!$qrBuscaTeam->EOF) {
            $receptor = $qrBuscaTeam->Fields('USUARIO');

            if ($receptor != '') {
                $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"),
                    GetSQLValueString($receptor, "text"),
                    GetSQLValueString($asunto, "text"),
                    GetSQLValueString($comentarioBitacora, "text"),
                    GetSQLValueString($auditoria, "date"),
                    GetSQLValueString($hora, "date"),
                    GetSQLValueString($estadoNoti, "text"),
                    GetSQLValueString($usuario, "text"),
                    GetSQLValueString($idBitacora, "int"));
                $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
            }
        $qrBuscaTeam->MoveNext(); }
        //***********************************************************************************************
    }

    if ($tipoUsuario == '1' or $tipoUsuario == '2') { // si tipo es administrador o supervisor notificacion llega a gestora y team gestion
        //busco los perfiles de team para asignarles la notificacion de nueva derivacion
        $query_qrBuscaTeam = "

            SELECT 
            login.USUARIO

            FROM `derivaciones` 

            LEFT JOIN team_gestion
                ON derivaciones.ID_DERIVACION = team_gestion.ID_DERIVACION
                
            LEFT JOIN login
                ON team_gestion.ID_PROFESIONAL = login.ID

            WHERE 
            derivaciones.ID_DERIVACION = '$idDerivacion'

        ";
        $qrBuscaTeam = $oirs->SelectLimit($query_qrBuscaTeam) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaTeam = $qrBuscaTeam->RecordCount(); 

        while (!$qrBuscaTeam->EOF) {
            $receptor = $qrBuscaTeam->Fields('USUARIO');

            if ($receptor != '') {
                $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"),
                    GetSQLValueString($receptor, "text"),
                    GetSQLValueString($asunto, "text"),
                    GetSQLValueString($comentarioBitacora, "text"),
                    GetSQLValueString($auditoria, "date"),
                    GetSQLValueString($hora, "date"),
                    GetSQLValueString($estadoNoti, "text"),
                    GetSQLValueString($usuario, "text"),
                    GetSQLValueString($idBitacora, "int"));
                $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
            }
            
        $qrBuscaTeam->MoveNext(); }


        //busco el perfil de gestora para asignarle la notificacion de derivacion
        $query_qrBuscaGestora = "

            SELECT 
            login.USUARIO

            FROM `derivaciones` 

            LEFT JOIN login
            ON derivaciones.ENFERMERA = login.ID

            WHERE 
            derivaciones.ID_DERIVACION = '$idDerivacion'

        ";
        $qrBuscaGestora = $oirs->SelectLimit($query_qrBuscaGestora) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaGestora = $qrBuscaGestora->RecordCount(); 

            $receptorGestora = $qrBuscaGestora->Fields('USUARIO');

            if ($receptorGestora != '') {
                $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"),
                    GetSQLValueString($receptorGestora, "text"),
                    GetSQLValueString($asunto, "text"),
                    GetSQLValueString($comentarioBitacora, "text"),
                    GetSQLValueString($auditoria, "date"),
                    GetSQLValueString($hora, "date"),
                    GetSQLValueString($estadoNoti, "text"),
                    GetSQLValueString($usuario, "text"),
                    GetSQLValueString($idBitacora, "int"));
                $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
            }

    }

    //################################################################################################################################################################

    if ($tipoUsuario == '4') { // si tipo es administrativa notificacion llega a gestora y A TENS (6)
        //busco los perfiles de team para asignarles la notificacion de nueva derivacion
        $query_qrBuscaTeam = "

            SELECT 
            login.USUARIO

            FROM `derivaciones` 

            LEFT JOIN team_gestion
                ON derivaciones.ID_DERIVACION = team_gestion.ID_DERIVACION
                
            LEFT JOIN login
                ON team_gestion.ID_PROFESIONAL = login.ID

            WHERE 
            derivaciones.ID_DERIVACION = '$idDerivacion' AND 
            login.TIPO = '6'

        ";
        $qrBuscaTeam = $oirs->SelectLimit($query_qrBuscaTeam) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaTeam = $qrBuscaTeam->RecordCount(); 

        while (!$qrBuscaTeam->EOF) {
            $receptor = $qrBuscaTeam->Fields('USUARIO');

            if ($receptor != '') {
                $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"),
                    GetSQLValueString($receptor, "text"),
                    GetSQLValueString($asunto, "text"),
                    GetSQLValueString($comentarioBitacora, "text"),
                    GetSQLValueString($auditoria, "date"),
                    GetSQLValueString($hora, "date"),
                    GetSQLValueString($estadoNoti, "text"),
                    GetSQLValueString($usuario, "text"),
                    GetSQLValueString($idBitacora, "int"));
                $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
            }
            
        $qrBuscaTeam->MoveNext(); }


        //busco el perfil de gestora para asignarle la notificacion de derivacion
        $query_qrBuscaGestora = "

            SELECT 
            login.USUARIO

            FROM `derivaciones` 

            LEFT JOIN login
            ON derivaciones.ENFERMERA = login.ID

            WHERE 
            derivaciones.ID_DERIVACION = '$idDerivacion'

        ";
        $qrBuscaGestora = $oirs->SelectLimit($query_qrBuscaGestora) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaGestora = $qrBuscaGestora->RecordCount(); 

            $receptorGestora = $qrBuscaGestora->Fields('USUARIO');

            if ($receptorGestora != '') {
               $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"),
                    GetSQLValueString($receptorGestora, "text"),
                    GetSQLValueString($asunto, "text"),
                    GetSQLValueString($comentarioBitacora, "text"),
                    GetSQLValueString($auditoria, "date"),
                    GetSQLValueString($hora, "date"),
                    GetSQLValueString($estadoNoti, "text"),
                    GetSQLValueString($usuario, "text"),
                    GetSQLValueString($idBitacora, "int"));
                $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
            }

    }

    //#########################################################################################################################################################

    if ($tipoUsuario == '6') { // si tipo es tens notificacion llega a gestora y A administrativa (6)
        //busco los perfiles de team para asignarles la notificacion de nueva derivacion
        $query_qrBuscaTeam = "

            SELECT 
            login.USUARIO

            FROM `derivaciones` 

            LEFT JOIN team_gestion
                ON derivaciones.ID_DERIVACION = team_gestion.ID_DERIVACION
                
            LEFT JOIN login
                ON team_gestion.ID_PROFESIONAL = login.ID

            WHERE 
            derivaciones.ID_DERIVACION = '$idDerivacion' AND 
            login.TIPO = '4'

        ";
        $qrBuscaTeam = $oirs->SelectLimit($query_qrBuscaTeam) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaTeam = $qrBuscaTeam->RecordCount(); 

        while (!$qrBuscaTeam->EOF) {
            $receptor = $qrBuscaTeam->Fields('USUARIO');

            if ($receptor != '') {
                $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"),
                    GetSQLValueString($receptor, "text"),
                    GetSQLValueString($asunto, "text"),
                    GetSQLValueString($comentarioBitacora, "text"),
                    GetSQLValueString($auditoria, "date"),
                    GetSQLValueString($hora, "date"),
                    GetSQLValueString($estadoNoti, "text"),
                    GetSQLValueString($usuario, "text"),
                    GetSQLValueString($idBitacora, "int"));
                $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
            }
            
        $qrBuscaTeam->MoveNext(); }


        //busco el perfil de gestora para asignarle la notificacion de derivacion
        $query_qrBuscaGestora = "

            SELECT 
            login.USUARIO

            FROM `derivaciones` 

            LEFT JOIN login
            ON derivaciones.ENFERMERA = login.ID 

            WHERE 
            derivaciones.ID_DERIVACION = '$idDerivacion'

        ";
        $qrBuscaGestora = $oirs->SelectLimit($query_qrBuscaGestora) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaGestora = $qrBuscaGestora->RecordCount(); 

            $receptorGestora = $qrBuscaGestora->Fields('USUARIO');

            if ($receptorGestora != '') {
                $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.notificaciones (ID_DERIVACION, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"),
                    GetSQLValueString($receptorGestora, "text"),
                    GetSQLValueString($asunto, "text"),
                    GetSQLValueString($comentarioBitacora, "text"),
                    GetSQLValueString($auditoria, "date"),
                    GetSQLValueString($hora, "date"),
                    GetSQLValueString($estadoNoti, "text"),
                    GetSQLValueString($usuario, "text"),
                    GetSQLValueString($idBitacora, "int"));
                $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
            }

    }

}


echo 1;

$Result1->Close(); 