<?php 
require("./config/include.php");
require_once(DIR_CLASES_AUDITORIAS_LOGICA.'cAuditoriasCargos.class.php');
$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);
FuncionesPHPLocal::CargarConstantes($conexion,array("roles"=>"si","sistema"=>SISTEMA));
$sesion = new Sesion($conexion,false);
$sesion->TienePermisos($conexion,$_SESSION['usuariocod'],$_SESSION['rolcod'],$_SERVER['PHP_SELF']);
$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header("Content-disposition: filename=car_cargos_".date("Y-m-d").".csv");
function fix_chars(&$value, $key){ $value = utf8_decode($value);}
$data[0] = "IdTipoCargo";
$data[1] = "Codigo";
$data[2] = "Descripcion";
$data[3] = "Esdeno";
$data[4] = "EquivalenciaHs";
$data[5] = "Estado";
$data[6] = "Usuario";
$data[7] = "Fecha";
$data[8] = "Acción";
array_walk($data,"fix_chars");
$buffer = fopen('php://output', 'r+');
fputcsv($buffer, $data,";");
$datos = $_SESSION['BusquedaAvanzadaAuditorias'];
if (!isset($datos['IdCargo']) || $datos['IdCargo']=="") die();
$oObjeto = new cAuditoriasCargos($conexion,"");
if(!$oObjeto->BusquedaAvanzada ($datos,$resultado,$numfilas))
	die();
while ($fila = $conexion->ObtenerSiguienteRegistro($resultado))
{
	$Fecha = " ".FuncionesPHPLocal::ConvertirFecha($fila['UltimaModificacionFecha'],"aaaa-mm-dd","dd/mm/aaaa")." ".substr($fila['UltimaModificacionFecha'],11,5)."Hs";
	$data[0] = $fila['IdTipoCargo'];
	$data[1] = $fila['Codigo'];
	$data[2] = $fila['Descripcion'];
	$data[3] = $fila['Esdeno'];
	$data[4] = $fila['EquivalenciaHs'];
	$data[5] = $fila['Estado'];
	$data[6] = $fila['NombreCompleto'];
	$data[7] = $Fecha;
	$data[8] = $fila['Accion'];
	fputcsv($buffer, $data,";");
}
$csv = fgets($buffer);
fclose($buffer);
?>