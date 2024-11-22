<?php
//Connection statement
require_once '../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../includes/functions.inc.php';

$idDerivacion = $_REQUEST['idDerivacion'];

$query_slTeamAtencion = ("SELECT * FROM $MM_oirs_DATABASE.2_derivaciones where ID_DERIVACION = '$idDerivacion'"); 
$slTeamAtencion = $oirs->SelectLimit($query_slTeamAtencion) or die($oirs->ErrorMsg());
$totalRows_slTeamAtencion = $slTeamAtencion->RecordCount(); 

$tens = $slTeamAtencion->Fields('TENS');
$adm = $slTeamAtencion->Fields('ADMINISTRATIVA');

?>

<!DOCTYPE html>
<html>
    <body>
        <div class="table-responsive-sm">
            <table  align="center" id="tblTeamGestion" class="table table-bordered table-striped table-hover table-sm">
                <thead>
                    <tr align="center">
                        <th><strong>Rut Profesional</strong></th>
                        <th><strong>Nombre</strong></th>
                        <th><strong>Profesión</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <?php


                    $query_slTens = ("SELECT * FROM $MM_oirs_DATABASE.login where USUARIO = '$tens'");
                    $slTens = $oirs->SelectLimit($query_slTens) or die($oirs->ErrorMsg());
                    $totalRows_slTens = $slTens->RecordCount(); 

                    $query_slAdm = ("SELECT * FROM $MM_oirs_DATABASE.login where USUARIO = '$adm'");
                    $slAdm = $oirs->SelectLimit($query_slAdm) or die($oirs->ErrorMsg());
                    $totalRows_slAdm = $slAdm->RecordCount();
                    ?>
                
                    <tr>
                        <td><p><span class="glyphicon glyphicon-credit-card"></span> <font size="2"><?php echo $slTens->Fields('USUARIO'); ?></font></p></td>

                        <td><p><span class="glyphicon glyphicon-user"></span> <font size="2"><?php echo $slTens->Fields('NOMBRE'); ?></font></p></td>
                        <td>
                            <p><span class="glyphicon glyphicon-user"></span><font size="2">Tens</font></p>
                        </td>

                    </tr>
                    <tr>
                        <td><p><span class="glyphicon glyphicon-credit-card"></span> <font size="2"><?php echo $slAdm->Fields('USUARIO'); ?></font></p></td>

                        <td><p><span class="glyphicon glyphicon-user"></span> <font size="2"><?php echo $slAdm->Fields('NOMBRE'); ?></font></p></td>
                        <td>
                            <p><span class="glyphicon glyphicon-user"></span><font size="2">Administrativa</font></p>
                        </td>

                    </tr>

                </tbody>
            </table>
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
            url: "2.0/vistas/modulos/asignarTeamGestion/desasociarTeamGestion.php",
            data: cadena,
            success: function(r) {
                if (r == 1) {
                    $("#dvTablaTeamGestion").load('2.0/vistas/modulos/asignarTeamGestion/tablaTeamGestion.php?idDerivacion='+idDerivacion);
                    swal("Genial!", "Profesional Desasociado", "success");
                } else {
                    // alertify.error("Fallo el servidor :(");
                }
            }
        });
    }

</script>


