<?php
//Connection statement
require_once '../../../../Connections/oirs.php';

//Aditional Functions
require_once '../../../../includes/functions.inc.php';

$idBitacora = $_POST['idBitacora'];

$estado='eliminado';


    $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_bitacora SET ESTADO=%s WHERE ID_BITACORA= '$idBitacora'",
            GetSQLValueString($estado, "text"));
    $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());




echo 1;

$Result1->Close(); 