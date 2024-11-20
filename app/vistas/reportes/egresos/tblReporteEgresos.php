<?php 
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';


 $fecha= $_REQUEST['fecha'];
 $fecha2= $_REQUEST['fecha2'];

if ($fecha == '' and $fecha2 == '') {
  $query_qrReporteDiario= "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE ASUNTO ='Egreso hospitalario'";
  $qrReporteDiario = $oirs->SelectLimit($query_qrReporteDiario) or die($oirs->ErrorMsg());
  $totalRows_qrReporteDiario = $qrReporteDiario->RecordCount();
}

if ($fecha != '' and $fecha2 == '') {
  $query_qrReporteDiario= "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE AUDITORIA ='$fecha' AND ASUNTO ='Egreso hospitalario'";
  $qrReporteDiario = $oirs->SelectLimit($query_qrReporteDiario) or die($oirs->ErrorMsg());
  $totalRows_qrReporteDiario = $qrReporteDiario->RecordCount();
}

if ($fecha == '' and $fecha2 != '') {
  $query_qrReporteDiario= "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE AUDITORIA ='$fecha' AND ASUNTO ='Egreso hospitalario'";
  $qrReporteDiario = $oirs->SelectLimit($query_qrReporteDiario) or die($oirs->ErrorMsg());
  $totalRows_qrReporteDiario = $qrReporteDiario->RecordCount();
}

if ($fecha != '' and $fecha2 != '') {
  $query_qrReporteDiario= "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE AUDITORIA >='$fecha' and AUDITORIA <='$fecha2' AND ASUNTO ='Egreso hospitalario'";
  $qrReporteDiario = $oirs->SelectLimit($query_qrReporteDiario) or die($oirs->ErrorMsg());
  $totalRows_qrReporteDiario = $qrReporteDiario->RecordCount();
}





 ?>
    <table id="tblReporteBitacora"  class="table table-bordered table-striped table-hover table-sm">
      <thead class="table-info">
        <tr>
          <th>Folio</th> 
          <th>Estado RN</th> 
          <th>Fecha Egreso</th> 
          <th>Rut Pcte.</th>
          <th>Nombre</th>
          <th>Patología</th>
          <th>Id admisión</th>
          <th>Fecha ingreso</th>
          <th>Dias Ing.</th>
          <th>Cod. Prestación</th>
          <th>Diagnóstico</th>
          <th>Convenio</th>
          <th>Ley Urg.</th>
           <th>Gestora</th>

   
        </tr>
      </thead>
      <tbody>
        <?php
         while (!$qrReporteDiario->EOF) { 
          $bitacora = utf8_encode($qrReporteDiario->Fields('BITACORA'));

          // Expresión regular para encontrar el ID de admisión
          $pattern = '/id de admision (\d+-[A-Z0-9]+)/';

          // Buscar la coincidencia en el string
          if (preg_match($pattern, utf8_encode( $qrReporteDiario->Fields('BITACORA')), $matches)) {
              // El ID de admisión está en la primera captura de grupo
              $id_admision = $matches[1];

              $query_qrCenso= "SELECT * FROM $MM_oirs_DATABASE.api_censo WHERE id_admision ='$id_admision'";
              $qrCenso = $oirs->SelectLimit($query_qrCenso) or die($oirs->ErrorMsg());
              $totalRows_qrCenso = $qrCenso->RecordCount();

              $folio = $qrCenso->Fields('FOLIO');

              $query_qrDerivacion= "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE FOLIO ='$folio'";
              $qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
              $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

              $idPaciente = $qrDerivacion->Fields('ID_PACIENTE');

              $query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID ='$idPaciente'";
              $qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
              $totalRows_qrPaciente = $qrPaciente->RecordCount();

              $idPatologia = $qrDerivacion->Fields('ID_PATOLOGIA');

              $query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE ID_PATOLOGIA ='$idPatologia'";
              $qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
              $totalRows_qrPatologia = $qrPatologia->RecordCount();

              $idGestora = $qrDerivacion->Fields('ENFERMERA');

              $query_qrLogin= "SELECT * FROM $MM_oirs_DATABASE.login WHERE ID ='$idGestora'";
              $qrLogin = $oirs->SelectLimit($query_qrLogin) or die($oirs->ErrorMsg());
              $totalRows_qrLogin = $qrLogin->RecordCount();


          }
         

         ?>
        <tr>
          <td> <span><font size="3"><?php echo $folio; ?></font></span></td>
          <td> <span><font size="3"><?php echo utf8_encode($qrDerivacion->Fields('ESTADO_RN')); ?></font></span></td>
          <td><?php echo date("d-m-Y",strtotime($qrReporteDiario->Fields('AUDITORIA'))); ?></td>
          <td><?php echo utf8_encode($qrPaciente->Fields('COD_RUTPAC')); ?></td>
          <td><?php echo utf8_encode($qrPaciente->Fields('NOMBRE')); ?></td>
          <td><?php echo utf8_encode($qrPatologia->Fields('DESC_PATOLOGIA')); ?></td>
          <td><?php echo $id_admision; ?></td>
          <td><?php echo $qrCenso->Fields('fecha_ingreso'); ?></td>
          <td><?php echo $qrCenso->Fields('dias_ingresado'); ?></td>
          <td><?php echo $qrCenso->Fields('codigo_prestacion'); ?></td>
          <td><?php echo utf8_encode($qrCenso->Fields('diagnostico')); ?></td>
          <td><?php echo utf8_encode($qrCenso->Fields('nombre_convenio')); ?></td>
          <td><?php echo utf8_encode($qrCenso->Fields('ley_urgencia')); ?></td>
          <td><?php echo utf8_encode($qrLogin->Fields('NOMBRE')); ?></td>
          
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


