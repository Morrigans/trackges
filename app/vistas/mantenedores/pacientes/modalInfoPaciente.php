<div class="modal" tabindex="-1" id="modalInfoPaciente" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header"><h5 class="modal-title">Información personal del contacto</h5>
      </div>

      <div class="modal-body">
        <div id="dvCargaInfoPaciente"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="fnCierraModal()" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
	function fnCierraModal(){
		$('#modalInfoPaciente').hide(); 
	}
</script>
