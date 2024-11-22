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

$idUsuario = $_SESSION['idUsuario'];
$tipoUsuario = $_SESSION['tipoUsuario'];

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idPaciente = $qrDerivacion->Fields('ID_PACIENTE');
$enfermera = $qrDerivacion->Fields('ENFERMERA');
$marca = $qrDerivacion->Fields('MARCA');
$decreto = $qrDerivacion->Fields('DECRETO');
$estadoRn = utf8_encode($qrDerivacion->Fields('ESTADO_RN'));

$query_qrMontos= "SELECT * FROM $MM_oirs_DATABASE.montos WHERE ID_DERIVACION = '$idDerivacion'";
$qrMontos = $oirs->SelectLimit($query_qrMontos) or die($oirs->ErrorMsg());
$totalRows_qrMontos = $qrMontos->RecordCount();

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$idPaciente'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$codRutPac = $qrPaciente->Fields('COD_RUTPAC');

$codTipoPatologia = $qrDerivacion->Fields('CODIGO_TIPO_PATOLOGIA');

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia WHERE ID_TIPO_PATOLOGIA = '$codTipoPatologia'";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

$codPatologia = $qrDerivacion->Fields('ID_PATOLOGIA');

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia_pp WHERE ID_PATOLOGIA = '$codPatologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$codEtapaPatologia = $qrDerivacion->Fields('CODIGO_ETAPA_PATOLOGIA');

 $query_qrEtapaPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA = '$codEtapaPatologia'";
$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

$codCanastaPatologia = $qrDerivacion->Fields('CODIGO_CANASTA_PATOLOGIA');

$query_qrCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanastaPatologia'";
$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

$query_qrAsignarEnfermeria= "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO = '3' order by NOMBRE asc";
$qrAsignarEnfermeria = $oirs->SelectLimit($query_qrAsignarEnfermeria) or die($oirs->ErrorMsg());
$totalRows_qrAsignarEnfermeria = $qrAsignarEnfermeria->RecordCount();

$codConvenio = $qrDerivacion->Fields('ID_CONVENIO');

 $query_qrConvenio= "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$codConvenio'";
$qrConvenio = $oirs->SelectLimit($query_qrConvenio) or die($oirs->ErrorMsg());
$totalRows_qrConvenio = $qrConvenio->RecordCount();

$query_qrDerivacionCanasta= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
$qrDerivacionCanasta = $oirs->SelectLimit($query_qrDerivacionCanasta) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanasta = $qrDerivacionCanasta->RecordCount();

$query_qrDerivacionEtapa= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_etapas_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacionEtapa = $oirs->SelectLimit($query_qrDerivacionEtapa) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionEtapa = $qrDerivacionEtapa->RecordCount();

?>
<div class="col-md-12">
	<div class="table-responsive">
			<table class="table">
			<tr>
		<!-- 		<th style="">Folio Right Now:</th>
				<td>
					<?php echo utf8_encode($qrDerivacion->Fields('FOLIO'));
						if ($marca == 'para_cierre') { ?>
							<span class="badge"><font size="3">[Marcada para cierre]</font></span>
						<?php }else{
								if ($estadoRn == 'Solicita autorización') {?>
									<button class="btn btn-info btn-xs" onclick="fnAgregarMarca('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>')">Marcar para cierre</button> 
							<?php	}
						 ?>
							
						<?php }
					 ?>
				</td> -->
			</tr>
			<tr>
				<th style="width:50%">Paciente:</th>
				<td><?php 
					$codRutPac = explode(".", $codRutPac);
					$rut0 = $codRutPac[0]; // porción1
					$rut1 = $codRutPac[1]; // porción2
					$rut2 = $codRutPac[2]; // porción2
					$codRutPac = $rut0.$rut1.$rut2;
					echo $codRutPac;
				?> <?php echo utf8_encode($qrPaciente->Fields('NOMBRE')); ?>
			</td>
			</tr>
			<tr>
				<th style="">Convenio:</th>
				<td><?php echo utf8_encode($qrConvenio->Fields('PREVISION')); ?></td>
			</tr>
			<!-- <tr>
				<th style="">Monto Inicial:</th>
				<td>$<?php echo utf8_encode(number_format($qrMontos->Fields('MONTO'))); ?>.-</td>
			</tr> -->
			<tr>
				<th style="">Fecha Derivación:</th>
				<td><?php echo date("d-m-Y",strtotime($qrDerivacion->Fields('FECHA_DERIVACION'))); ?></td>
			</tr>
			<tr>
				<th>Tipo patología:</th>
				<td><?php echo utf8_encode($qrTipoPatologia->Fields('DESC_TIPO_PATOLOGIA')); ?></td>
			</tr>
			<tr>
				<th>Patología:</th>
				<td><?php echo utf8_encode($qrPatologia->Fields('DESC_PATOLOGIA')); ?></td>
			</tr>
			<tr>
				<th>Etapa patología:</th>
				<td>
					<?php 
						if ($totalRows_qrDerivacionCanasta == 0) {
							echo 'No hay etapas activas';
						}else{
							$i=1;
							while (!$qrDerivacionEtapa->EOF) {
								$codEtapaPatologia = $qrDerivacionEtapa->Fields('CODIGO_ETAPA_PATOLOGIA');
								$idEtapaPatologia = $qrDerivacionEtapa->Fields('ID_ETAPA_PATOLOGIA');

								$query_qrEtapaPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA = '$codEtapaPatologia'";
								$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
								$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

								

							    $query_qrBuscaCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE CODIGO_ETAPA_PATOLOGIA = '$codEtapaPatologia' AND ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
								$qrBuscaCanastaPatologia = $oirs->SelectLimit($query_qrBuscaCanastaPatologia) or die($oirs->ErrorMsg());
								$totalRows_qrBuscaCanastaPatologia = $qrBuscaCanastaPatologia->RecordCount();

								if ($totalRows_qrBuscaCanastaPatologia == 0) {
									// code...
								}else{
									echo $i.'.- '.utf8_encode($qrEtapaPatologia->Fields('DESC_ETAPA_PATOLOGIA')).'.</br>';
									$i++;
								}

								

								
							$qrDerivacionEtapa->MoveNext();
							}
						}	
					?>
				</td>
			</tr>
			<tr>
				<th>Canasta patología:</th>
				<td>
					<?php 
						if ($totalRows_qrDerivacionCanasta == 0) {
							echo 'No hay canastas activas';
						}else{
							$i=1;
							while (!$qrDerivacionCanasta->EOF) {
								$idCanastaPatologia = $qrDerivacionCanasta->Fields('CODIGO_CANASTA_PATOLOGIA');

								 $query_qrCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$idCanastaPatologia' and DECRETO='$decreto'";
								$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
								$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

								echo $i.'.- '.utf8_encode($qrCanastaPatologia->Fields('DESC_CANASTA_PATOLOGIA')).'.</br>';

								$i++;
							$qrDerivacionCanasta->MoveNext();
							}
						}
					?>
				</td>
			</tr>
			</table>
		</div>
</div>
<script>
// 	function fnAgregarMarca(idDerivacion, tipoUsuario){
// 		slAgregarMarcaDerivacion = 'para_cierre';

// 		cadena = 'idDerivacion=' + idDerivacion +
// 						 '&slAgregarMarcaDerivacion=' + slAgregarMarcaDerivacion;
// 			$.ajax({
// 				type:"post",
// 				data:cadena,
// 				url:'vistas/modulos/agregarMarca/agregarMarca.php',
// 				success:function(r){
// 					if (r == 1) {
// 						Swal.fire({
// 						  position: 'top-end',
// 						  icon: 'success',
// 						  title: 'La marca ha sido agregada con exito',
// 						  showConfirmButton: false,
// 						  timer: 1200
// 						})
// 						if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
// 							setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); $('#dvInfoVentanasOpcionesBitacora').load('vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion); }, 1);
// 						}
// 						if (tipoUsuario == 4) {// administrativa
// 							setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioAdministrativa/inicioSAdministrativa.php'); }, 1);
// 						}
// 						// if (tipoUsuario == 5) {//medico
// 						// 	setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
// 						// }
// 						if (tipoUsuario == 6) {//tens
// 							setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioTens/inicioTens.php'); }, 1);
// 						}
// 		    		}
					
// 				}
// 			});
// 		}
// </script>