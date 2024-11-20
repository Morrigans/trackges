<?php 
//Connection statement
require_once '../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }


date_default_timezone_set('America/Santiago');
$diaHoy= date('Y-m-d');

$query_qrEmisor = "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO <> '5'";
$qrEmisor = $oirs->SelectLimit($query_qrEmisor) or die($oirs->ErrorMsg());
$totalRows_qrEmisor = $qrEmisor->RecordCount();

$query_qrAsunto = "SELECT DISTINCT ASUNTO FROM $MM_oirs_DATABASE.bitacora";
$qrAsunto = $oirs->SelectLimit($query_qrAsunto) or die($oirs->ErrorMsg());
$totalRows_qrAsunto = $qrAsunto->RecordCount();

?>
<form id="formReporteDiario">
  <div class="card card-info">
        <div class="card-header">
          <h3 class="card-title">Registro de bitacora</h3>
          <div class="card-tools">
            <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="card-body col-sm-12">
    <!-- <form class="form-horizontal" method="POST" action="addEvent.php"> -->
      <div class="row">
            <div class="input-group mb-3 col-sm-2">
               <div class="input-group-prepend">
                 <span class="input-group-text">Fecha</span>
               </div>
                  <input type="date" class="form-control float-right" id="inpRangoFechasBitacora" name="inpRangoFechasBitacora" onchange="fnReporteDiarioBitacora(this.value)">
            </div>

            <div class="input-group mb-3 col-sm-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Emisor</span>
                </div>
                <select name="slEmisorBitacora" id="slEmisorBitacora" class="form-control input-sm select2bs4" onchange="fnReporteDiarioBitacora(this.value)">
                  <option value="">Seleccione...</option>
                  <?php while (!$qrEmisor->EOF) {?>
                    <option value="<?php echo $qrEmisor->Fields('USUARIO') ?>"><?php echo utf8_encode($qrEmisor->Fields('NOMBRE')) ?></option>
                  <?php $qrEmisor->MoveNext(); } ?>
                </select>
            </div>

            <div class="input-group mb-3 col-sm-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Asunto</span>
                </div>
                <select name="slAsuntoBitacora" id="slAsuntoBitacora" class="form-control input-sm select2bs4" onchange="fnReporteDiarioBitacora(this.value)">
                  <option value="">Seleccione...</option>
                  <?php while (!$qrAsunto->EOF) {?>
                    <option value="<?php echo $qrAsunto->Fields('ASUNTO') ?>"><?php echo utf8_encode($qrAsunto->Fields('ASUNTO')) ?></option>
                  <?php $qrAsunto->MoveNext(); } ?>
                </select>
            </div>
              
        </div>
        <div id="dvMuestraTblReporteBitacora" ></div>
    </div>
  </div>
</form>
<script type="text/javascript">

  // pone fecha actual en input de fecha
  var fechaHoy = new Date();
  document.getElementById("inpRangoFechasBitacora").value = fechaHoy.toJSON().slice(0,10);

  //Initialize Select2 Elements
  $('.select2bs4').select2({
    theme: 'bootstrap4'
  })

 fecha=$('#inpRangoFechasBitacora').val();


$('#dvMuestraTblReporteBitacora').load('vistas/reportes/bitacora/tblReporteBitacora.php?fecha='+fecha);

  function fnReporteDiarioBitacora(){
    fecha=$('#inpRangoFechasBitacora').val();
    slEmisorBitacora=$('#slEmisorBitacora').val();
    slAsuntoBitacora=$('#slAsuntoBitacora').val();
    
    // $('#dvMuestraTblReporteBitacora').load('vistas/reportes/bitacora/tblReporteBitacora.php?fecha='+fecha + '&slEmisorBitacora='+slEmisorBitacora + '&slAsuntoBitacora='+slAsuntoBitacora);
    cadena = 'fecha=' + fecha +
          '&slEmisorBitacora=' + slEmisorBitacora+
          '&slAsuntoBitacora=' + slAsuntoBitacora;
        $.ajax({
          type:"post",
          data:cadena,
          url:'2.0/vistas/reportes/bitacora/tblReporteBitacora.php',
          success:function(r){
            $('#dvMuestraTblReporteBitacora').html(r);
          }
        })
  }

  
</script>

