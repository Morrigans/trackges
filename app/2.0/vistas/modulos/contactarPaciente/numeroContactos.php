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

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');

$idDerivacion = $_POST['idDerivacion'];
$tipoContacto = $_POST['tipoContacto'];

$query_qrContactos = "SELECT * FROM $MM_oirs_DATABASE.contacto_paciente WHERE ID_DERIVACION = '$idDerivacion' and TIPO_CONTACTO = '$tipoContacto' order by ID_CONTACTO desc";
$qrContactos = $oirs->SelectLimit($query_qrContactos) or die($oirs->ErrorMsg());
$totalRows_qrContactos = $qrContactos->RecordCount();


?>

<div class="table-responsive-sm">
    <?php if ($totalRows_qrContactos == 0) {?>
        <h6>No hay registros de contactos de este tipo de contacto</h6>
    <?php }else{ ?>
    <table  align="center" id="tPrestadoresExternos" class="table">
        <thead>
            <tr align="center">
                <th width="30%"><strong>Medio Contacto</strong></th>
                <th><strong>Nota Contacto</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php
        while (!$qrContactos->EOF) { 
            $rutaDoc = $qrContactos->Fields('RUTA_DOCUMENTO');
            $linkAudio = $qrContactos->Fields('RUTA_AUDIO');
            $linkAudio = (explode("/",$linkAudio));
            $linkAudio = $linkAudio[2];
            $idContacto =$qrContactos->Fields('ID_CONTACTO');
            ?>
            <!-- en este hidden se guardan los nombres de los archivos de audio que se pasan por el load para darle play a cada audio -->
            <input type="hidden" id="<?php echo $idContacto ?>rutaAudioContactos" value="<?php echo $linkAudio ?>">
            <tr>
                <td>
                    <span class="glyphicon glyphicon-user"></span> <font size="3"><?php echo $qrContactos->Fields('MEDIO_CONTACTO'); ?></font><br>
                    <span class="glyphicon glyphicon-user"></span> <font size="3"><?php echo $qrContactos->Fields('FECHA_REGISTRO'); ?></font><br>
                    <span class="glyphicon glyphicon-user"></span> <font size="3"><?php echo $qrContactos->Fields('HORA_REGISTRO'); ?></font>
                </td>
                <td><span class="glyphicon glyphicon-user"></span> <font size="3"><?php echo $qrContactos->Fields('NOTA_CONTACTO'); ?></font><br>

                    <?php 
                    if ($linkAudio != null) {?>
                        [<a href="#" onclick="$('#dvFrmPlayAudios').load('vistas/modulos/contactarPaciente/frmPlayAudios.php?linkAudio='+$('#'+<?php echo $idContacto ?>+'rutaAudioContactos').val())"><span class="badge badge-warning"><i class="fas fa-play"></i></span></a>
                        <a href="#" onclick="document.getElementById('audioContacto').pause()"><span class="badge badge-warning"><i class="fas fa-stop"></i></span></a>]
                    <?php } 

                    if ($rutaDoc != null) {?>
                        <span class=""><a target="_blank" class="btn btn-xs btn-success" href="vistas/bitacora/adjuntaDoc/<?php echo $rutaDoc; ?>" ><i class="far fa-file-pdf"></i></a></span>
                    <?php } ?>
                        
                </td>
            </tr>
            <?php
        $qrContactos->MoveNext();
        }
        ?>
        </tbody>
    </table>
    <?php } ?>
</div>



