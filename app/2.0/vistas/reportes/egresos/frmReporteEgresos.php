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
            <div class="input-group mb-3 col-sm-6">
               <div class="input-group-prepend">
                 <span class="input-group-text">Fecha Inicio</span>
               </div>
                  <input type="date" class="form-control float-right" id="inpRangoFechasEgreso1" name="inpRangoFechasEgreso1" onchange="fnReporteDiarioEgresos(this.value)">
            </div>

            <div class="input-group mb-3 col-sm-6">
                <div class="input-group-prepend">
                  <span class="input-group-text">Fecha Termino</span>
                </div>
                <input type="date" class="form-control float-right" id="inpRangoFechasEgreso2" name="inpRangoFechasEgreso2" onchange="fnReporteDiarioEgresos(this.value)">
            </div>
             <!-- <div class="input-group mb-3 col-sm-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Asunto</span>
                </div>
                <button class="btn btn-success btn-lg btn-block" onclick="fnReporteDiario()">Buscar</button>
                <select name="slAsuntoBitacora" id="slAsuntoBitacora" class="form-control input-sm select2bs4" onchange="fnReporteDiarioBitacora(this.value)">
                  <option value="">Seleccione...</option>
                  <?php while (!$qrAsunto->EOF) {?>
                    <option value="<?php echo $qrAsunto->Fields('ASUNTO') ?>"><?php echo utf8_encode($qrAsunto->Fields('ASUNTO')) ?></option>
                  <?php $qrAsunto->MoveNext(); } ?>
                </select>
            </div> -->
            
        </div>
        <div id="dvMuestraTblReporteEgresos" ></div>
    </div>
  </div>
</form>
<script type="text/javascript">

  // pone fecha actual en input de fecha
  var fechaHoy = new Date();
  document.getElementById("inpRangoFechasEgreso1").value = fechaHoy.toJSON().slice(0,10);

  fecha=$('#inpRangoFechasEgreso1').val();


$('#dvMuestraTblReporteEgresos').load('2.0/vistas/reportes/egresos/tblReporteEgresos.php?fecha='+fecha);

  function fnReporteDiarioEgresos(){
    fecha=$('#inpRangoFechasEgreso1').val();
    fecha2=$('#inpRangoFechasEgreso2').val();
    //slEmisorBitacora=$('#slEmisorBitacora').val();
    //slAsuntoBitacora=$('#slAsuntoBitacora').val();
    
    cadena = 'fecha='+fecha +
             '&fecha2='+fecha2;
        $.ajax({
          type:"post",
          data:cadena,
          url:'2.0/vistas/reportes/egresos/tblReporteEgresos.php',
          success:function(r){
            $('#dvMuestraTblReporteEgresos').html(r);
          }
        })
  }

  
</script>

