<?php 
require_once 'Connections/oirs.php';
require_once 'includes/functions.inc.php';





?>

<div class="modal fade" id="modalAlarmasInicio" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #E76D54">
        <h4 align="left" class="modal-title" id="myModalLabel">Alarmas programadas<br/></h4>
        <button type="button" id="cerrar" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <div class="card card-danger card-outline card-tabs">
          <div class="card-header p-0 pt-1 border-bottom-0">
            <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">Activas</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" onclick="fnCargaAlarmasHistorial()" aria-selected="false">Historial</a>
              </li>

            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content" id="custom-tabs-three-tabContent">
              <div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                 <div id="dvFrmAlarmasInicio"></div>
              </div>
              <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                 <div id="dvFrmAlarmasInicioHistorial"></div>
              </div>

            </div>
          </div>
        </div>

        
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function fnCargaAlarmasHistorial(){

      $('#dvFrmAlarmasInicioHistorial').load('2.0/vistas/alarmas/modals/frmAlarmasInicioHistorial.php?');
      //$('#modalAlarmasInicio').show();
  }
</script>