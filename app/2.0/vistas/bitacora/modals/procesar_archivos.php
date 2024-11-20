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

$usuario = $_SESSION['dni'];

date_default_timezone_set('America/Santiago');
$auditoria= date('Y-m-d');
$hora= date('G:i');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Directorio de destino para los archivos cargados
    $targetDir = "../adjuntaDoc/";

    if (!empty($_FILES['archivo']['name'])) {
        $archivoNombre = $_FILES['archivo']['name'];
        $archivoTmpName = $_FILES['archivo']['tmp_name'];
        $idBitacora = $_POST['idBitacora'];
        $idDerivacion = $_POST['idDerivacion']; 

        // Verificar si el archivo es válido
        $allowedExtensions = array('xls','xlsx','docx','doc', 'pdf', 'png', 'jpg', 'jpeg', 'gif');
        $archivoExtension = pathinfo($archivoNombre, PATHINFO_EXTENSION);
        $identificadorUnico = uniqid();
        $archivoNombre = 'docs/'.$identificadorUnico.'_'.$archivoNombre; 

        if (in_array(strtolower($archivoExtension), $allowedExtensions)) {
            $archivoDestino = $targetDir . $archivoNombre;

            // Mover el archivo al directorio de destino
            if (move_uploaded_file($archivoTmpName, $archivoDestino)) {
                // Aquí puedes realizar cualquier otra operación necesaria, como guardar el nombre del archivo en la base de datos.
                $updateSQL = sprintf("UPDATE $MM_oirs_DATABASE.2_bitacora SET RUTA_DOCUMENTO=%s WHERE ID_BITACORA= '$idBitacora'",           
                       GetSQLValueString($archivoNombre, "text"));
                $Result1 = $oirs->Execute($updateSQL) or die($oirs->ErrorMsg());
                
                // Responde con un mensaje de éxito
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al subir el archivo.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No se ha seleccionado ningún archivo.']);
    }
}
?>

