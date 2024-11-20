<?php 
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';



 $fecha= $_REQUEST['fecha'];
 $slEmisorBitacora= $_REQUEST['slEmisorBitacora'];
 $slAsuntoBitacora= utf8_decode($_REQUEST['slAsuntoBitacora']);


if ($fecha != '' and $slEmisorBitacora == '' and $slAsuntoBitacora == '') {
  $query_qrReporteDiario= "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE AUDITORIA ='$fecha'";
  $qrReporteDiario = $oirs->SelectLimit($query_qrReporteDiario) or die($oirs->ErrorMsg());
  $totalRows_qrReporteDiario = $qrReporteDiario->RecordCount();
}

if ($fecha != '' and $slEmisorBitacora != '' and $slAsuntoBitacora == '') {
  $query_qrReporteDiario= "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE AUDITORIA ='$fecha' AND SESION ='$slEmisorBitacora'";
  $qrReporteDiario = $oirs->SelectLimit($query_qrReporteDiario) or die($oirs->ErrorMsg());
  $totalRows_qrReporteDiario = $qrReporteDiario->RecordCount();
}

if ($fecha != '' and $slEmisorBitacora == '' and $slAsuntoBitacora != '') {
  $query_qrReporteDiario= "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE AUDITORIA ='$fecha' AND ASUNTO ='$slAsuntoBitacora'";
  $qrReporteDiario = $oirs->SelectLimit($query_qrReporteDiario) or die($oirs->ErrorMsg());
  $totalRows_qrReporteDiario = $qrReporteDiario->RecordCount();
}

if ($fecha != '' and $slEmisorBitacora != '' and $slAsuntoBitacora != '') {
  $query_qrReporteDiario= "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE AUDITORIA ='$fecha' AND ASUNTO ='$slAsuntoBitacora' AND SESION ='$slEmisorBitacora'";
  $qrReporteDiario = $oirs->SelectLimit($query_qrReporteDiario) or die($oirs->ErrorMsg());
  $totalRows_qrReporteDiario = $qrReporteDiario->RecordCount();
}





 ?>
    <table id="tblReporteBitacora"  class="table table-bordered table-striped table-hover table-sm">
      <thead class="table-info">
        <tr>
          <th>ID</th> 
          <th>Fecha</th> 
          <th>Derivacion</th> 
          <th>Asunto</th> 
          <th>Bitacora</th>

   
        </tr>
      </thead>
      <tbody>
        <?php
         while (!$qrReporteDiario->EOF) { 

         ?>
        <tr>
          <td><?php echo $qrReporteDiario->Fields('ID_BITACORA'); ?></td>
          <td><?php echo date("d-m-Y",strtotime($qrReporteDiario->Fields('AUDITORIA'))); ?></td>
          <td> <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacoraDiaria('<?php echo $qrReporteDiario->Fields('ID_DERIVACION'); ?>')"><span class="badge badge-warning"><font size="3"><?php echo $qrReporteDiario->Fields('ID_DERIVACION'); ?></font></span></a></td>
          <td><?php echo utf8_encode($qrReporteDiario->Fields('ASUNTO')); ?></td>
          <td><?php echo utf8_encode( $qrReporteDiario->Fields('BITACORA')); ?></td>
          
        </tr>
        <?php
            $qrReporteDiario->MoveNext(); 
        }
              ?>
        </tbody>
    </table>


<script>

  function fnfrmBitacoraDiaria(idDerivacion){
    $('#dvfrmBitacora').load('vistas/bitacora/modals/frmBitacora.php?idDerivacion=' + idDerivacion);
  }

    $(function () {
        $('#tblReporteBitacora').DataTable({
          "paging": true,
          "lengthChange": false,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": true,
          "responsive": true,
          "order": [[ 0, 'desc' ]],
          dom: 'lBfrtip',
          buttons: [ 'copy', 'excel'],
          "language": {
              "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
              }
        });

      });

  </script>


