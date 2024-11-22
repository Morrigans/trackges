<?php
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

$idBitacora = $_REQUEST['idBitacora'];

$query_qrAlarmas = ("SELECT * FROM $MM_oirs_DATABASE.alarmas_pp where ID_BITACORA = '$idBitacora' AND ESTADO = 'activa'"); 
$qrAlarmas = $oirs->SelectLimit($query_qrAlarmas) or die($oirs->ErrorMsg());
$totalRows_qrAlarmas = $qrAlarmas->RecordCount(); ?>

<!DOCTYPE html>
<html>
    <body>
       
        <div class="table-responsive-sm">
            <?php if ($totalRows_qrAlarmas == 0) {?>
                <h6>No hay alarmas programadas para este comentario de bitacora</h6>
            <?php }else{ ?>
            <table  align="center" id="tblAlarmas" class="table  table-hover">
                <thead>
                    <tr align="center" class="alert alert-default">
                        <th><strong>Fecha alarma</strong></th>
                        <th><strong>Nombre</strong></th>
                        <th><strong>Profesión</strong></th>
                        <th><strong>Quitar alarma</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                while (!$qrAlarmas->EOF) {
                    $rutProfesional = $qrAlarmas->Fields('USUARIO_RECEPTOR');

                    $query_slProfesional = ("SELECT * FROM $MM_oirs_DATABASE.login where USUARIO = '$rutProfesional'");
                    $slProfesional = $oirs->SelectLimit($query_slProfesional) or die($oirs->ErrorMsg());
                    $totalRows_slProfesional = $slProfesional->RecordCount(); ?>
                
                    <tr>
                        <td><p><span class="glyphicon glyphicon-user"></span> <font size="3"><?php echo date("d-m-Y",strtotime($qrAlarmas->Fields('FECHA_ALARMA'))) ?></font></p></td>
                        <td><p><span class="glyphicon glyphicon-user"></span> <font size="3"><?php echo $slProfesional->Fields('NOMBRE'); ?></font></p></td>
                        <td>
                            <p><span class="glyphicon glyphicon-user"></span>
                                <font size="3">
                                    <?php 
                                        $tipoPro = $slProfesional->Fields('TIPO'); 
                                        
                                        $query_qrTipoPro = ("SELECT * FROM $MM_oirs_DATABASE.profesion where ID = '$tipoPro'");
                                        $qrTipoPro = $oirs->SelectLimit($query_qrTipoPro) or die($oirs->ErrorMsg());
                                        $totalRows_qrTipoPro = $qrTipoPro->RecordCount();

                                        echo $qrTipoPro->Fields('PROFESION');
                                    ?>
                                </font>
                            </p>
                        </td>
                        <td align="center"><button class="btn btn-danger btn-sm" onclick="fnQuitarAlarmaProgramada('<?php echo $qrAlarmas->Fields('ID_ALARMA') ?>','<?php echo $idBitacora ?>','<?php echo $slProfesional->Fields('USUARIO') ?>')">Quitar Alarma</button>
                        </td>
                    </tr>
                    <?php
                $qrAlarmas->MoveNext();
                }
                ?>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </body>
</html>

<script>
    function fnQuitarAlarmaProgramada(idAlarma,idBitacora) {
        cadena = "idAlarma=" + idAlarma +
                 "&idBitacora=" + idBitacora;
        $.ajax({
            type: "POST",
            url: "vistas/modulos/pacientesPp/bitacora/modals/quitarAlarmaProgramada.php",
            data: cadena,
            success: function(r) {
                if (r == 1) {
                   $("#tablaDestinatariosAlarmas").load('vistas/modulos/pacientesPp/bitacora/modals/tablaDestinatariosAlarmas.php?idBitacora='+idBitacora);
                    swal("Genial!", "Alarma Eliminada", "success");
                } else {
                    // alertify.error("Fallo el servidor :(");
                }
            }
        });
    }

</script>


