<?php
ob_start();
require("./config/include.php");
require_once(DIR_CLASES_LOGICA.'cEscalafones.class.php');

$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes($conexion,array("roles"=>"si","sistema"=>SISTEMA));
$conexion->SetearAdmiGeneral(ADMISITE);

$sesion = new Sesion($conexion,false);
$sesion->TienePermisos($conexion,$_SESSION['usuariocod'],$_SESSION['rolcod'],$_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

header('Content-Type: text/html; charset=iso-8859-1');
$_POST=FuncionesPHPLocal::DecodificarUtf8 ($_POST);
$msg = array();
$msg['IsSucceed'] = false;
$datos = $_POST;
$conexion->ManejoTransacciones("B");

if (!isset($datos['accion']) || $datos['accion']=="")
{
    $msg['Msg'] = "Error al procesar";
    echo json_encode($msg);
    ob_end_flush();
    die();
}
$oObjeto = new cEscalafones($conexion,"");

switch($datos['accion'])
{
    case 1:
        if($oObjeto->Insertar($datos,$codigoInsertado))
        {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Se ha agregado correctamente a las ".date("H").":".date("i")."Hs";
            $msg['IdEscalafon'] = $codigoInsertado;
            $msg['header'] = "esc_escalafones_am.php?IdEscalafon=".$codigoInsertado;
        }
    break;
    case 2:
        if($oObjeto->Modificar($datos))
        {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Se ha modificado correctamente a las ".date("H").":".date("i")."Hs";
        }
    break;
    case 3:
        if($oObjeto->Eliminar($datos))
        {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Se ha eliminado correctamente a las ".date("H").":".date("i")."Hs";
            $msg['header'] = "esc_escalafones.php";
        }
    break;    case 4:
        if($oObjeto->DesActivar($datos))
        {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Se ha desactivado correctamente";
        }
    break;
    case 5:
        if($oObjeto->Activar($datos))
        {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Se ha activado correctamente";
        }
    break;}

if ($msg['IsSucceed'])
    $conexion->ManejoTransacciones("C");
else
{
    $msg['Msg'] = utf8_encode(ob_get_contents());
    $conexion->ManejoTransacciones("R");
}
ob_clean();
echo json_encode($msg);
ob_end_flush();