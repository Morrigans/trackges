<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../../../../index.php');
    exit;
}

$idUsuario = $_SESSION['idUsuario'];
$tipoUsuario = $_SESSION['tipoUsuario'];

$misCasos = $_REQUEST['misCasos'];
$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idPaciente = $qrDerivacion->Fields('ID_PACIENTE');

$query_qrPaciente = "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$idPaciente'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$codRutPac = $qrPaciente->Fields('COD_RUTPAC');
?>

<!DOCTYPE html>
<html>
<body>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></div>
            <div class="col-md-6"><h2>Estado de Avance</h2></div>
            <div class="col-md-6" id="dvInfoVentanasOpcionesEstAv"></div>
            <div class="col-md-6">
                <div class="input-group mb-3 col-sm-12">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Actualizar</span>
                    </div>
                    <select name="slAgregarEstadoAvance" id="slAgregarEstadoAvance" class="form-control input-sm" onchange="checkRechazado()">

                        <option value="">Seleccione</option>
                        <option value="1. En proceso de contactabilidad">1. En proceso de contactabilidad</option>
                        <option value="2. Evaluación Pre-Quirúrgica">2. Evaluación Pre-Quirúrgica</option>
                        <option value="3. Cirugía Agendada">3. Cirugía Agendada</option>
                        <option value="4. Cirugía Realizada">4. Cirugía Realizada</option>
                        <option value="5. Proceso de atención oncológica">5. Proceso de atención oncológica</option>
                        <option value="6. En QMT-RDT">6. En QMT-RDT</option>
                        <option value="7. QMT-RDT finalizado">7. QMT-RDT finalizado</option>
                        <option value="8. Proceso finalizado - Reinsertado en la red">8. Proceso finalizado - Reinsertado en la red</option>
                        <option value="9. Rechazado">9. Rechazado</option>
                    </select>
                </div>

                <div class="input-group mb-3 col-sm-12">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Motivo</span>
                    </div>
                    <select name="slAgregarRechazo" id="slAgregarRechazo" class="form-control input-sm" disabled>
                        <option value="">Seleccione</option>
                        <option value="1. CASO RESUELTO EN HOSPITAL PÚBLICO">1. CASO RESUELTO EN HOSPITAL PÚBLICO</option>
                        <option value="2. CASO RESUELTO EN PRESTADOR PRIVADO">2. CASO RESUELTO EN PRESTADOR PRIVADO</option>
                        <option value="3. CONTACTO NO CORRESPONDE">3. CONTACTO NO CORRESPONDE</option>
                        <option value="4. DERIVACIÓN ERRÓNEA FONASA">4. DERIVACIÓN ERRÓNEA FONASA</option>
                        <option value="5. FALLECIMIENTO">5. FALLECIMIENTO</option>
                        <option value="6. INASISTENCIAS">6. INASISTENCIAS</option>
                        <option value="7. PATOLOGÍAS CONCOMITANTES QUE IMPIDEN LA CIRUGÍA (O LA ATENCIÓN)">7. PATOLOGÍAS CONCOMITANTES QUE IMPIDEN LA CIRUGÍA (O LA ATENCIÓN)</option>
                        <option value="8. RECHAZA DERIVACIÓN AL PRESTADOR PRIVADO">8. RECHAZA DERIVACIÓN AL PRESTADOR PRIVADO</option>
                        <option value="9. RECHAZA LA REALIZACIÓN DE ATENCIÓN EN SALUD">9. RECHAZA LA REALIZACIÓN DE ATENCIÓN EN SALUD</option>
                        <option value="10. RECHAZO POR LEJANÍA">10. RECHAZO POR LEJANÍA</option>
                        <option value="11. RESOLUTIVIDAD CLÍNICA">11. RESOLUTIVIDAD CLÍNICA</option>
                        <option value="12. SIN INDICACIÓN ACTUAL DE CIRUGÍA">12. SIN INDICACIÓN ACTUAL DE CIRUGÍA</option>
                        <option value="13. CUPOS MENSUALES COMPLETOS">13. CUPOS MENSUALES COMPLETOS</option>
                    </select>
                </div>

                <div class="input-group mb-3 col-sm-12">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Tipo contacto</span>
                    </div>
                    <select name="slTipoContacto" id="slTipoContacto" class="form-control input-sm" disabled>
                        <option value="">Seleccione</option>
                        <option value="Contesta y corta">Contesta y corta</option>
                        <option value="No contesta">No contesta</option>
                        <option value="Numero equivocado">Numero equivocado</option>
                        <option value="Numero no existe">Numero no existe</option>
                        <option value="Telefono apagado">Telefono apagado</option> 
                    </select>
                </div>

                <span class="label label-default">Comentario<br></span>
                <textarea name="comentarioEstadoAvance" id="comentarioEstadoAvance" cols="11" rows="10" class="form-control input-sm"></textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer" align="right">    
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" data-dismiss="modal" onclick="fnFrmEstadoAvance('<?php echo $idDerivacion ?>', '<?php echo $tipoUsuario ?>', '<?php echo $misCasos ?>')">Actualizar estado de avance</button>
    </div>

    <input type="hidden" id="idDerivacionEstAv" value="<?php echo $idDerivacion ?>">
</body>
</html>

<script>
function checkRechazado() {
    const estadoAvance = document.getElementById('slAgregarEstadoAvance').value;
    const rechazoSelect = document.getElementById('slAgregarRechazo');
    const tipoContactoSelect = document.getElementById('slTipoContacto');

    if (estadoAvance.includes('Rechazado')) {
        rechazoSelect.disabled = false;
    } else {
        rechazoSelect.disabled = true;
        rechazoSelect.value = ''; // Reinicia el valor del select si se desactiva
        tipoContactoSelect.disabled = true;
        tipoContactoSelect.value = ''; // Reinicia el select de contacto
    }

    // Habilitar slTipoContacto si el motivo de rechazo es "CONTACTO NO CORRESPONDE"
    rechazoSelect.addEventListener('change', function() {
        if (rechazoSelect.value === 'CONTACTO NO CORRESPONDE') {
            tipoContactoSelect.disabled = false;
        } else {
            tipoContactoSelect.disabled = true;
            tipoContactoSelect.value = ''; // Reinicia el valor si se desactiva
        }
    });
}

function fnFrmEstadoAvance(idDerivacion, tipoUsuario, misCasos) {
    const comentarioEstadoAvance = $('#comentarioEstadoAvance').val();
    const slAgregarEstadoAvance = $('#slAgregarEstadoAvance').val();
    const slAgregarRechazo = $('#slAgregarRechazo').val();
    const slTipoContacto = $('#slTipoContacto').val();

    // Validación si slAgregarEstadoAvance está vacío
    if (slAgregarEstadoAvance === '') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'No se guardó, debe seleccionar al menos un estado de avance!',
        });
        return;
    }

    // Validación si el estado es "Rechazado" y el motivo de rechazo está vacío
    if (slAgregarEstadoAvance.includes('Rechazado') && slAgregarRechazo === '') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Debe seleccionar un motivo de rechazo cuando el estado incluye "Rechazado".',
        });
        return;
    }

    // Validación si el motivo de rechazo es "CONTACTO NO CORRESPONDE" y slTipoContacto está vacío
    if (slAgregarRechazo === 'CONTACTO NO CORRESPONDE' && slTipoContacto === '') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Debe seleccionar un tipo de contacto cuando el motivo de rechazo es "CONTACTO NO CORRESPONDE".',
        });
        return;
    }

    // Construir la cadena de datos para el envío
    let cadena = `idDerivacion=${idDerivacion}&slAgregarEstadoAvance=${slAgregarEstadoAvance}&comentarioEstadoAvance=${comentarioEstadoAvance}&slAgregarRechazo=${slAgregarRechazo}`;

    // Agregar el tipo de contacto si corresponde
    if (slAgregarRechazo === 'CONTACTO NO CORRESPONDE') {
        cadena += `&slTipoContacto=${slTipoContacto}`;
    }

    // Enviar datos por AJAX
    $.ajax({
        type: "post",
        data: cadena,
        url: '2.0/vistas/modulos/estadoAvance/estadoAvance.php',
        success: function (r) {
            if (r == 1) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Se guardó el estado con éxito',
                    showConfirmButton: false,
                    timer: 500
                });
                    setTimeout(function () {
                        if (misCasos == '') {
                            $('#dvTablaEstadoAvance').load('2.0/vistas/reportes/estadoAvance/estadoAvance.php');
                        }else{
                            $('#dvTablaEstadoAvance').load('2.0/vistas/reportes/estadoAvance/tablaEstadoAvanceMisCasos.php'); 
                        }
                        
                        
                    }, 1000);
            }
        }
    });
}

idDerivacion = $('#idDerivacionEstAv').val();
$('#dvInfoVentanasOpcionesEstAv').load(`2.0/vistas/modulos/infoVentanasOpciones.php?idDerivacion=${idDerivacion}`);
</script>


