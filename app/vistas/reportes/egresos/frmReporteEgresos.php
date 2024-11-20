<?php 
//Connection statement
require_once '../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }


date_default_timezone_set('America/Santiago');
$diaHoy= date('Y-m-d');

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


$('#dvMuestraTblReporteEgresos').load('vistas/reportes/egresos/tblReporteEgresos.php?fecha='+fecha);

  function fnReporteDiarioEgresos(){
    fecha=$('#inpRangoFechasEgreso1').val();
    fecha2=$('#inpRangoFechasEgreso2').val();

    
    cadena = 'fecha='+fecha +
             '&fecha2='+fecha2;
        $.ajax({
          type:"post",
          data:cadena,
          url:'vistas/reportes/egresos/tblReporteEgresos.php',
          success:function(r){
            $('#dvMuestraTblReporteEgresos').html(r);
          }
        })
  }

  
</script>

