<div class="modal fade" id="modalContactosHospital" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: scroll;">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 align="left" class="modal-title" id="myModalLabel">Nuevo Contacto<br /></h4>
        <button type="button" class="close" data-dismiss="modal"  aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="frmContactosHospital"></div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
   function fnCerrar(){


      $('#dvMenuContactosHospital').load('vistas/menuLateral/menuContactosHospital.php');
    }

</script>