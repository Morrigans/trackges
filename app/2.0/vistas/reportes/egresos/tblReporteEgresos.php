<?php 
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';


 $fecha= $_REQUEST['fecha'];
 $fecha2= $_REQUEST['fecha2'];

if ($fecha == '' and $fecha2 == '') {
  $query_qrReporteDiario= "SELECT * FROM $MM_oirs_DATABASE.2_bitacora WHERE ASUNTO ='Egreso hospitalario'";
  $qrReporteDiario = $oirs->SelectLimit($query_qrReporteDiario) or die($oirs->ErrorMsg());
  $totalRows_qrReporteDiario = $qrReporteDiario->RecordCount();
}

if ($fecha != '' and $fecha2 == '') {
  $query_qrReporteDiario= "SELECT * FROM $MM_oirs_DATABASE.2_bitacora WHERE AUDITORIA ='$fecha' AND ASUNTO ='Egreso hospitalario'";
  $qrReporteDiario = $oirs->SelectLimit($query_qrReporteDiario) or die($oirs->ErrorMsg());
  $totalRows_qrReporteDiario = $qrReporteDiario->RecordCount();
}

if ($fecha == '' and $fecha2 != '') {
  $query_qrReporteDiario= "SELECT * FROM $MM_oirs_DATABASE.2_bitacora WHERE AUDITORIA ='$fecha' AND ASUNTO ='Egreso hospitalario'";
  $qrReporteDiario = $oirs->SelectLimit($query_qrReporteDiario) or die($oirs->ErrorMsg());
  $totalRows_qrReporteDiario = $qrReporteDiario->RecordCount();
}

if ($fecha != '' and $fecha2 != '') {
  $query_qrReporteDiario= "SELECT * FROM $MM_oirs_DATABASE.2_bitacora WHERE AUDITORIA >='$fecha' and AUDITORIA <='$fecha2' AND ASUNTO ='Egreso hospitalario'";
  $qrReporteDiario = $oirs->SelectLimit($query_qrReporteDiario) or die($oirs->ErrorMsg());
  $totalRows_qrReporteDiario = $qrReporteDiario->RecordCount();
}





 ?>
    <table id="tblEgresos"  class="table table-bordered table-striped table-hover table-sm">
      <thead class="table-info">
        <tr>
          <th>Folio</th> 
          <th>Estado RN</th> 
          <th>Fecha Egreso</th> 
          <th>Rut Pcte.</th>
          <th>Nombre</th>
          <th>Patologia</th>
          
          <th>Id admisión</th>
          <th>Fecha ingreso</th>
          <th>Dias Ing.</th>
          <th>Cod. Prestación</th>
          <th>Diagnóstico</th>
          
          
          <th>Gestora</th>
          <th>avg estancia</th>
          <th>limite inferior peso grd</th>
          <th>avg peso grd</th>
          <th>avg monto grd total</th>
          <th>peso grd real</th>
          <th>monto grd total real</th>
          <th>monto x linea</th>
          <th>diferencia montos</th>

   
        </tr>
      </thead>
      <tbody>
        <?php
         while (!$qrReporteDiario->EOF) { 
          $bitacora = $qrReporteDiario->Fields('BITACORA');

          // Expresión regular para encontrar el ID de admisión
          $pattern = '/id de admision (\d+-[A-Z0-9]+)/';

          // Buscar la coincidencia en el string
          if (preg_match($pattern, $qrReporteDiario->Fields('BITACORA'), $matches)) {
              // El ID de admisión está en la primera captura de grupo
              $id_admision = $matches[1];

              $query_qrCenso= "SELECT * FROM $MM_oirs_DATABASE.2_api_censo WHERE id_admision ='$id_admision'";
              $qrCenso = $oirs->SelectLimit($query_qrCenso) or die($oirs->ErrorMsg());
              $totalRows_qrCenso = $qrCenso->RecordCount();

              $folio = $qrCenso->Fields('FOLIO');

              $query_qrDerivacion= "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE FOLIO ='$folio'";
              $qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
              $totalRows_qrDerivacion = $qrDerivacion->RecordCount();

              $codRutPac = $qrDerivacion->Fields('COD_RUTPAC');
              $patologia = $qrDerivacion->Fields('PROBLEMA_SALUD');

              $query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC ='$codRutPac'";
              $qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
              $totalRows_qrPaciente = $qrPaciente->RecordCount();

              $idGestora = $qrDerivacion->Fields('ENFERMERA');

              $query_qrLogin= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO ='$idGestora'";
              $qrLogin = $oirs->SelectLimit($query_qrLogin) or die($oirs->ErrorMsg());
              $totalRows_qrLogin = $qrLogin->RecordCount();

              $query_qrGrd= "SELECT * FROM $MM_oirs_DATABASE.2_grd_referencia WHERE descripcion_patologia ='$patologia'";
              $qrGrd = $oirs->SelectLimit($query_qrGrd) or die($oirs->ErrorMsg());
              $totalRows_qrGrd = $qrGrd->RecordCount();


          }
         

         ?>
        <tr>
          <td><font size="3"><?php echo $folio; ?></font></td>
          <td> <span><font size="2"><?php echo $qrDerivacion->Fields('ESTADO_RN'); ?></font></span></td>
          <td><?php echo date("d-m-Y",strtotime($qrReporteDiario->Fields('AUDITORIA'))); ?></td>
          <td><?php echo $qrDerivacion->Fields('COD_RUTPAC'); ?></td>
          <td><font size="2"><?php echo $qrPaciente->Fields('NOMBRE'); ?></font></td>
          <td><font size="2"><?php echo $qrDerivacion->Fields('PROBLEMA_SALUD'); ?></font></td>
          
          <td><?php echo $id_admision; ?></td>
          <td><?php echo $qrCenso->Fields('fecha_ingreso'); ?></td>
          <td><?php echo $qrCenso->Fields('dias_ingresado'); ?></td>
          <td><?php echo $qrCenso->Fields('codigo_prestacion'); ?></td>
          <td><?php echo $qrCenso->Fields('diagnostico'); ?></td>
          
          
          <td><?php echo $qrLogin->Fields('NOMBRE'); ?></td>
          <td><?php echo $qrGrd->Fields('avg_estancia'); ?></td>
          <td><?php echo $qrGrd->Fields('limite_inferior_peso_grd'); ?></td>
          <td><?php echo $qrGrd->Fields('avg_peso_grd'); ?></td>
          <td><?php echo $qrGrd->Fields('avg_monto_grd_total'); ?></td>
          <td><?php echo $qrGrd->Fields('peso_grd_real'); ?></td>
          <td><?php echo $qrGrd->Fields('monto_grd_total_real'); ?></td>
          <td><?php echo $qrGrd->Fields('monto_x_linea'); ?></td>
          <td><?php echo $qrGrd->Fields('diferencia_montos'); ?></td>
          

          
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
        $('#tblEgresos').DataTable({
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


