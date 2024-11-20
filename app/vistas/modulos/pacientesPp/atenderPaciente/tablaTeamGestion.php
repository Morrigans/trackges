<?php
//Connection statement
require_once '../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../includes/functions.inc.php';

$idDerivacion = $_REQUEST['idDerivacion'];

$query_slTeamAtencion = ("SELECT * FROM $MM_oirs_DATABASE.team_gestion_pp where ID_DERIVACION = '$idDerivacion'"); 
$slTeamAtencion = $oirs->SelectLimit($query_slTeamAtencion) or die($oirs->ErrorMsg());
$totalRows_slTeamAtencion = $slTeamAtencion->RecordCount(); ?>

<!DOCTYPE html>
<html>
    <body>
        <div class="table-responsive-sm">
            <?php if ($totalRows_slTeamAtencion == 0) {?>
                <h6>No hay profesionales asignados al equipo de gestión</h6>
            <?php }else{ ?>
            <table  align="center" id="tPrestadoresExternos" class="table table-bordered table-striped table-hover table-sm">
                <thead>
                    <tr align="center">
                        <th><strong>Rut Profesional</strong></th>
                        <th><strong>Nombre</strong></th>
                        <th><strong>Profesión</strong></th>
                        <th><strong>Desasociar</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                while (!$slTeamAtencion->EOF) {
                    $idProfesional = $slTeamAtencion->Fields('ID_PROFESIONAL');

                    $query_slProfesional = ("SELECT * FROM $MM_oirs_DATABASE.login where ID = '$idProfesional'");
                    $slProfesional = $oirs->SelectLimit($query_slProfesional) or die($oirs->ErrorMsg());
                    $totalRows_slProfesional = $slProfesional->RecordCount(); ?>
                
                    <tr>
                        <td><p><span class="glyphicon glyphicon-credit-card"></span> <font size="2"><?php echo $slProfesional->Fields('USUARIO'); ?></font></p></td>

                        <td><p><span class="glyphicon glyphicon-user"></span> <font size="2"><?php echo utf8_encode($slProfesional->Fields('NOMBRE')); ?></font></p></td>
                        <td>
                            <p><span class="glyphicon glyphicon-user"></span>
                                <font size="2">
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
                        <td align="center"><button class="btn btn-danger btn-xs" onclick="fnDesasociarTeamGestion('<?php echo $slTeamAtencion->Fields('ID_TEAM') ?>','<?php echo $idDerivacion ?>','<?php echo $slProfesional->Fields('USUARIO') ?>')">Quitar</button>
                        </td>
                    </tr>
                    <?php
                $slTeamAtencion->MoveNext();
                }
                ?>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </body>
</html>

<script>
    function fnDesasociarTeamGestion(id,idDerivacion,rutPro) {
        cadena = "id=" + id +
                 "&idDerivacion=" + idDerivacion +
                 "&rutPro=" + rutPro;
        $.ajax({
            type: "POST",
            url: "vistas/modulos/asignarTeamGestion/desasociarTeamGestion.php",
            data: cadena,
            success: function(r) {
                if (r == 1) {
                    $("#dvTablaTeamGestion").load('vistas/modulos/asignarTeamGestion/tablaTeamGestion.php?idDerivacion='+idDerivacion);
                    swal("Genial!", "Profesional Desasociado", "success");
                } else {
                    // alertify.error("Fallo el servidor :(");
                }
            }
        });
    }

</script>


