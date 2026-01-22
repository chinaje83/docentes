<?php
ob_start();
require('./config/include.php');
require_once DIR_LIBRERIAS."handler.php";
$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);



// carga las constantes generales
FuncionesPHPLocal::CargarConstantes($conexion,array("roles"=>"si","sistema"=>SISTEMA,"multimedia"=>"si"));
$oEncabezados = new cEncabezados($conexion);


$sesion = new Sesion($conexion);
$sesion->TienePermisos($conexion,$_SESSION['usuariocod'],$_SESSION['rolcod'],$_SERVER['PHP_SELF']);
header('Content-Type: text/html; charset=iso-8859-1');

$result = array();
$result['success'] = false;
$datos = $_POST;


$uploader = new UploadHandler();
// Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
$uploader->allowedExtensions = array(); // all files types allowed by default
// Specify max file size in bytes.
//$uploader->sizeLimit = null;
$uploader->sizeLimit = TAMANIOARCHIVOS;
// Specify the input name set in the javascript.
$nombrearchivo = $uploader->getName();
$uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default
// If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
$uploader->chunksFolder = PATH_STORAGE."tmp";

$method = $_SERVER["REQUEST_METHOD"];



if ($method == "POST") {
    header("Content-Type: text/plain");
    // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
    // For example: /myserver/handlers/endpoint.php?done
    if (isset($_GET["done"])) {
        $nombrearchivo = $uploader->getName();
        $pathinfo = pathinfo($nombrearchivo);
        $extension = strtolower($pathinfo['extension']);
        //$name = "archivo_".$_POST['tipo']."_".date("Ymdhis")."_".rand(0,10000).".".$extension;
        $name = "archivo_".date("Ymdhis")."_".rand(0,10000).".".$extension;
        $result = $uploader->combineChunks(PATH_STORAGE."tmp",$name);
        if($result['success'])
        {
            $result['nombrearchivotmp'] = $datos['nombrearchivotmp'] = $name;
            $result['nombrearchivo'] = $datos['nombrearchivo'] = $nombrearchivo;
            $result['hasharchivo'] = hash_file("md5", PATH_STORAGE."tmp/".$name);
            $result['size'] = $datos['size'] = filesize(PATH_STORAGE."tmp/".$name);
            $result['ext'] = $extension;
            $result['base_64']=base64_encode(file_get_contents(PATH_STORAGE."tmp/".$name));
        }

    }
    // Handles upload requests
    else {
        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $nombrearchivo = $uploader->getName();
        $pathinfo = pathinfo($nombrearchivo);
        $extension = strtolower($pathinfo['extension']);
        //$name = "archivo_".$_POST['tipo']."_".date("Ymdhis")."_".rand(0,10000).".".$extension;
        $name = "archivo_".date("Ymdhis")."_".rand(0,10000).".".$extension;
        $result = $uploader->handleUpload(PATH_STORAGE."tmp",$name);
        // To return a name used for uploaded file you can use the following line.
        $result["uploadName"] = $uploader->getUploadName();

        if (isset($result['success']) && ($result['success'] && $result["uploadName"]!=""))
        {
            $result['nombrearchivotmp'] = $datos['nombrearchivotmp'] = $name;
            $result['nombrearchivo'] = $datos['nombrearchivo'] = $nombrearchivo;
            $result['hasharchivo'] = hash_file("md5", PATH_STORAGE."tmp/".$name);
            $result['size'] = $datos['size'] = filesize(PATH_STORAGE."tmp/".$name);
            $result['ext'] = $extension;
            $result['base_64']=base64_encode(file_get_contents(PATH_STORAGE."tmp/".$name));
        }
    }
    echo json_encode($result);
}
// for delete file requests
else if ($method == "DELETE") {
    $result = $uploader->handleDelete(PATH_STORAGE."tmp");
    echo json_encode($result);
}
else {
    header("HTTP/1.0 405 Method Not Allowed");
}
?>
