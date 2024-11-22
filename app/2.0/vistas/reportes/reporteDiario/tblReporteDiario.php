<?php 
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';



 $fecha= $_REQUEST['fecha'];

 $query_qrReporteDiario= "SELECT * FROM $MM_oirs_DATABASE.2_bitacora WHERE AUDITORIA ='$fecha'";
$qrReporteDiario = $oirs->SelectLimit($query_qrReporteDiario) or die($oirs->ErrorMsg());
$totalRows_qrReporteDiario = $qrReporteDiario->RecordCount();

// $query_pagosPro= "SELECT * FROM $MM_oirs_DATABASE.rrhh_pagos WHERE ESTADO_PAGO = 'boleta_validada'";
// $pagosPro = $oirs->SelectLimit($query_pagosPro) or die($oirs->ErrorMsg());
// $totalRows_pagosPro = $pagosPro->RecordCount();



 ?>
    <table id="tblReporteDiario"  class="table table-bordered table-striped table-hover table-sm">
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
          <td> <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacoraDiaria('<?php echo $qrReporteDiario->Fields('ID_DERIVACION'); ?>')"><span class="badge badge-warning"><font size="3"><?php echo $qrReporteDiario->Fields('FOLIO'); ?></font></span></a></td>
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
    $('#dvfrmBitacora').load('2.0/vistas/bitacora/modals/frmBitacora.php?idDerivacion=' + idDerivacion);
  }

    $(function () {
        $('#tblReporteDiario').DataTable({
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


