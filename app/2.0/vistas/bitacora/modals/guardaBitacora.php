<?php
//Connection statement
require_once '../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
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

$query_verFolio = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones where ID_DERIVACION='$idDerivacion'";
$verFolio = $oirs->SelectLimit($query_verFolio) or die($oirs->ErrorMsg());
$totalRows_verFolio = $verFolio->RecordCount();

$folio=$verFolio->Fields('FOLIO');


$insertSQL = sprintf("INSERT INTO $MM_oirs_DATABASE.2_bitacora (ID_DERIVACION, FOLIO, SESION, BITACORA, ASUNTO, AUDITORIA, HORA, COMPARTIDO_EXT) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($idDerivacion, "text"), 
    GetSQLValueString($folio, "text"), 
    GetSQLValueString($usuario, "text"),
    GetSQLValueString($comentarioBitacora, "text"),
    GetSQLValueString($asunto, "text"),
    GetSQLValueString($auditoria, "date"),
    GetSQLValueString($hora, "date"),
    GetSQLValueString($comparte, "text"));
$Result1 = $oirs->Execute($insertSQL) or die($oirs->ErrorMsg());

$query_select = ("SELECT max(ID_BITACORA) as ID_BITACORA FROM $MM_oirs_DATABASE.2_bitacora");
$select = $oirs->SelectLimit($query_select) or die($oirs->ErrorMsg());
$totalRows_select = $select->RecordCount();

$idBitacora = $select->Fields('ID_BITACORA');


if ($asunto == 'Solicita gestión' or $asunto == 'Solicita Información') {
   
    //Inserto notificación
    $estadoNoti = 'nuevo';

    if ($tipoUsuario == '3') { // si es tipo gestora, notificacion llega a su team de gestion (administrativa y tens)
        //busco los perfiles de team para asignarles la notificacion de nueva derivacion
        $query_qrBuscaTeam = "SELECT * FROM `2_derivaciones` WHERE 2_derivaciones.ID_DERIVACION = '$idDerivacion'";
        $qrBuscaTeam = $oirs->SelectLimit($query_qrBuscaTeam) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaTeam = $qrBuscaTeam->RecordCount(); 

            $tens = $qrBuscaTeam->Fields('TENS');
            $adm = $qrBuscaTeam->Fields('ADMINISTRATIVA');

            if ($tens != '') {
                $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, FOLIO, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"),
                    GetSQLValueString($folio, "text"),
                    GetSQLValueString($tens, "text"),
                    GetSQLValueString($asunto, "text"),
                    GetSQLValueString($comentarioBitacora, "text"),
                    GetSQLValueString($auditoria, "date"),
                    GetSQLValueString($hora, "date"),
                    GetSQLValueString($estadoNoti, "text"),
                    GetSQLValueString($usuario, "text"),
                    GetSQLValueString($idBitacora, "int"));
                $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
            }
            if ($adm != '') {
                $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, FOLIO, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($idDerivacion, "int"),
                    GetSQLValueString($folio, "text"),
                    GetSQLValueString($adm, "text"),
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

    if ($tipoUsuario == '1' or $tipoUsuario == '2') { // si tipo es administrador o supervisor notificacion llega a gestora y team gestion
        //busco los perfiles de team para asignarles la notificacion de nueva derivacion
        $query_qrBuscaTeam = "SELECT * FROM `2_derivaciones` WHERE 2_derivaciones.ID_DERIVACION = '$idDerivacion'";
        $qrBuscaTeam = $oirs->SelectLimit($query_qrBuscaTeam) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaTeam = $qrBuscaTeam->RecordCount(); 

       $tens = $qrBuscaTeam->Fields('TENS');
       $adm = $qrBuscaTeam->Fields('ADMINISTRATIVA');
       $gestora = $qrBuscaTeam->Fields('ENFERMERA'); 

      if ($tens != '') {
          $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, FOLIO, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
              GetSQLValueString($idDerivacion, "int"),
              GetSQLValueString($folio, "text"),
              GetSQLValueString($tens, "text"),
              GetSQLValueString($asunto, "text"),
              GetSQLValueString($comentarioBitacora, "text"),
              GetSQLValueString($auditoria, "date"),
              GetSQLValueString($hora, "date"),
              GetSQLValueString($estadoNoti, "text"),
              GetSQLValueString($usuario, "text"),
              GetSQLValueString($idBitacora, "int"));
          $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
      }
      if ($adm != '') {
          $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, FOLIO, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
              GetSQLValueString($idDerivacion, "int"),
              GetSQLValueString($folio, "text"),
              GetSQLValueString($adm, "text"),
              GetSQLValueString($asunto, "text"),
              GetSQLValueString($comentarioBitacora, "text"),
              GetSQLValueString($auditoria, "date"),
              GetSQLValueString($hora, "date"),
              GetSQLValueString($estadoNoti, "text"),
              GetSQLValueString($usuario, "text"),
              GetSQLValueString($idBitacora, "int"));
          $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
      }
      if ($gestora != '') {
          $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, FOLIO, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
              GetSQLValueString($idDerivacion, "int"),
              GetSQLValueString($folio, "text"),
              GetSQLValueString($gestora, "text"),
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
        $query_qrBuscaTeam = "SELECT * FROM `2_derivaciones` WHERE 2_derivaciones.ID_DERIVACION = '$idDerivacion'";
               $qrBuscaTeam = $oirs->SelectLimit($query_qrBuscaTeam) or die($oirs->ErrorMsg());
               $totalRows_qrBuscaTeam = $qrBuscaTeam->RecordCount(); 

              $tens = $qrBuscaTeam->Fields('TENS');
              $gestora = $qrBuscaTeam->Fields('ENFERMERA');

             if ($tens != '') {
                 $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, FOLIO, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                     GetSQLValueString($idDerivacion, "int"),
                     GetSQLValueString($folio, "text"),
                     GetSQLValueString($tens, "text"),
                     GetSQLValueString($asunto, "text"),
                     GetSQLValueString($comentarioBitacora, "text"),
                     GetSQLValueString($auditoria, "date"),
                     GetSQLValueString($hora, "date"),
                     GetSQLValueString($estadoNoti, "text"),
                     GetSQLValueString($usuario, "text"),
                     GetSQLValueString($idBitacora, "int"));
                 $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
             }

             if ($gestora != '') {
                 $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, FOLIO, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                     GetSQLValueString($idDerivacion, "int"),
                     GetSQLValueString($folio, "text"),
                     GetSQLValueString($gestora, "text"),
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
         $query_qrBuscaTeam = "SELECT * FROM `2_derivaciones` WHERE 2_derivaciones.ID_DERIVACION = '$idDerivacion'";
        $qrBuscaTeam = $oirs->SelectLimit($query_qrBuscaTeam) or die($oirs->ErrorMsg());
        $totalRows_qrBuscaTeam = $qrBuscaTeam->RecordCount(); 

       $adm = $qrBuscaTeam->Fields('ADMINISTRATIVA');
       $gestora = $qrBuscaTeam->Fields('ENFERMERA');
      
      if ($adm != '') {
          $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, FOLIO, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
              GetSQLValueString($idDerivacion, "int"),
              GetSQLValueString($folio, "text"),
              GetSQLValueString($adm, "text"),
              GetSQLValueString($asunto, "text"),
              GetSQLValueString($comentarioBitacora, "text"),
              GetSQLValueString($auditoria, "date"),
              GetSQLValueString($hora, "date"),
              GetSQLValueString($estadoNoti, "text"),
              GetSQLValueString($usuario, "text"),
              GetSQLValueString($idBitacora, "int"));
          $Result2 = $oirs->Execute($insertSQL2) or die($oirs->ErrorMsg());
      }
      if ($gestora != '') {
          $insertSQL2 = sprintf("INSERT INTO $MM_oirs_DATABASE.2_notificaciones (ID_DERIVACION, FOLIO, USUARIO, ASUNTO, MENSAJE, FECHA, HORA, ESTADO, USUARIO_EMISOR, ID_BITACORA) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
              GetSQLValueString($idDerivacion, "int"),
              GetSQLValueString($folio, "text"),
              GetSQLValueString($gestora, "text"),
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