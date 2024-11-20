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



?>
<form id="formReporteDiario">
  <div class="card card-info">
        <div class="card-header">
          <h3 class="card-title">Registro de actividad diaria</h3>
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
            <div class="form-group mb-3 col-sm-6">
                <label>Fecha:</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="far fa-calendar-alt"></i>
                    </span>
                  </div>
                  <input type="date" class="form-control float-right" id="inpRangoFechas" name="inpRangoFechas" onchange="fnReporteDiario(this.value)">
                </div>
            </div>  
        </div>
        <div id="dvMuestraTblReporteDiario" ></div>
    </div>
  </div>
</form>
<input type="hidden" id="inpVacio" name="inpVacio" value="<?php echo $diaHoy ?>" >
<script type="text/javascript">

 hoy=$('#inpVacio').val();
 fecha=$('#inpRangoFechas').val();

if (fecha=='') {
$('#dvMuestraTblReporteDiario').load('2.0/vistas/reportes/reporteDiario/tblReporteDiario.php?fecha='+hoy);

}

  function fnReporteDiario(fecha){

    $('#dvMuestraTblReporteDiario').load('2.0/vistas/reportes/reporteDiario/tblReporteDiario.php?fecha='+fecha);

  }

  
</script>

