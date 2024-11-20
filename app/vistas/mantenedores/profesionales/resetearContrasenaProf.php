<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

 $id = $_REQUEST['id'];

header("Content-Type: text/html;charset=utf-8");

$resetPass='trackGes';

$options = array("cost"=>4);
$hashPassword1 = password_hash($resetPass,PASSWORD_BCRYPT,$options);

$updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.login SET PASS='$hashPassword1' WHERE ID='$id'");
$Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());





echo 1;

$Result1->Close();