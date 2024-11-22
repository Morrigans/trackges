<?php
//Connection statement
require_once '../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../includes/functions.inc.php';

date_default_timezone_set('America/Santiago');
$fecha= date('Y-m-d');

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

 $rutPostulante = $_SESSION['dni'];

$query_adjuntos = ("SELECT * FROM $MM_oirs_DATABASE.rrhh_postulaciones WHERE RUT_PROFESIONAL='$rutPostulante'");
$adjuntos = $oirs->SelectLimit($query_adjuntos) or die($oirs->ErrorMsg());
$totalRows_adjuntos = $adjuntos->RecordCount();

 $estado=$adjuntos->Fields('ESTADO');

?>
  
      <span class=""><i class="far fa-file-pdf fa-lg"></i></span>
      <a target="_blank" href="vistas/adjuntarCurriculum/<?php echo $adjuntos->Fields('CURRICULUM'); ?>" class="mailbox-attachment-name"> <span>Curriculum.pdf <i id="dvCheckCurriculum" class="fas fa-check"></i></p></span>
 

 <!--  <li><a target="_blank" href="vistas/adjuntarCurriculum/<?php echo $adjuntos->Fields('CURRICULUM'); ?>">Curriculum <i class="far fa-file-pdf fa-lg"></i></a></li>
                   <span class="float-right"></span> -->
    


      <!-- <table id="tblAdjuntos" class="table table-bordered table-striped">
          <thead>
              <tr bgcolor="#F6F4F3">
                  <th><font size="3">Rut</font></th>
                  <th><font size="3">Nombre</font></th>
                  <th><font size="3">Estado</font></th>
                  <th><font size="3">Curriculum</font></th>
                  <th><font size="3">Certificado</font></th>
              </tr>
          </thead>
          <tbody>
              <?php while (!$adjuntos->EOF) { ?>
              <tr>
                  <td><?php echo $adjuntos->Fields('RUT_PROFESIONAL') ?></td>
                  <td><?php echo $adjuntos->Fields('NOMBRE_PROFESIONAL') ?></td>
                  <td><?php echo $adjuntos->Fields('ESTADO') ?></td>
                  <td><?php echo $adjuntos->Fields('CURRICULUM') ?></td>
                  <td><?php echo $adjuntos->Fields('CERTIFICADO') ?></td>
                  
                     
              </tr>
              <?php $adjuntos->MoveNext();
              } ?>
          </tbody>
      </table> -->



<script>

    $(function () {
        $('#tblAdjuntos').DataTable({
          "paging": true,
          "lengthChange": false,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": true,
          "responsive": true,
          "order": [[ 1, 'asc' ]],
          dom: 'lfrtp',
            buttons: [ 'copy', 'excel', 'csv' ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                }
        });

      });
</script>