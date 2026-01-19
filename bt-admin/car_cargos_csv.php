<?php
require("./config/include.php");
require_once(DIR_CLASES_LOGICA.'cCargos.class.php');

$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes($conexion,array("roles"=>"si","sistema"=>SISTEMA));
$conexion->SetearAdmiGeneral(ADMISITE);

$sesion = new Sesion($conexion,false);
$sesion->TienePermisos($conexion,$_SESSION['usuariocod'],$_SESSION['rolcod'],$_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

$oObjeto = new cCargos($conexion,"");
header('Content-Type: text/html; charset=iso-8859-1');
$csv ="";
$csv_end = "\n";
$csv_sep = ";";
$nombre_archivo="car_cargos_".date("Y-m-d").".csv";
//encabezado
$csv .="IdCargo".$csv_sep."IdTipoCargo".$csv_sep."Codigo".$csv_sep."Descripcion".$csv_sep."Esdeno".$csv_sep."EquivalenciaHs".$csv_sep."Escalafon".$csv_sep."LugarDesempeño".$csv_sep."Estado".$csv_sep."AltaFecha".$csv_sep."AltaUsuario".$csv_sep."UltimaModificacionUsuario".$csv_sep."UltimaModificacionFecha".$csv_end;


$datos = $_SESSION['BusquedaAvanzada'];

if(!$oObjeto->BusquedaAvanzada ($datos,$resultado,$numfilas))
	die();

while ($fila = $conexion->ObtenerSiguienteRegistro($resultado))
{
    $Esdeno ="NO";
    if($fila['Esdeno']=="1")
        $Esdeno ="SI";

	$csv .= $fila["IdCargo"].$csv_sep.$fila['IdTipoCargodesc'].$csv_sep.$fila["Codigo"].$csv_sep.$fila["Descripcion"].$csv_sep.$Esdeno.$csv_sep.$fila["EquivalenciaHs"].$csv_sep.$fila["EscalafonDescripcion"].$csv_sep.$fila["DesempenoLugarNombre"].$csv_sep.$fila["Estado"].$csv_sep.FuncionesPHPLocal::ConvertirFecha( $fila['AltaFecha'],'aaaa-mm-dd','dd/mm/aaaa').$csv_sep.$fila["AltaUsuario"].$csv_sep.$fila["UltimaModificacionUsuario"].$csv_sep.FuncionesPHPLocal::ConvertirFecha( $fila['UltimaModificacionFecha'],'aaaa-mm-dd','dd/mm/aaaa').$csv_end;
}

header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header("Content-disposition: filename=" . $nombre_archivo);
print $csv;
?>
