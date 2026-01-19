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

$oObjeto = new cAuditoriasCargos($conexion,"");
header('Content-Type: text/html; charset=iso-8859-1');
if (isset ($_POST['page']))
	$page = $_POST['page'];
else
	$page = 1; 

if (isset ($_POST['rows']))
	$limit = $_POST['rows'];
else
	$limit = 1;

$sidx = "IdFilaLog";
$sord = "DESC";

$datos =  $_SESSION['BusquedaAvanzadaAuditorias'] = $_POST;

if (!isset($datos['IdCargo']) || $datos['IdCargo']=="") die();
if(!$oObjeto->BusquedaAvanzada ($datos,$resultado,$numfilas))
	die();

if (isset ($_POST['sord']))
	$sord = $_POST['sord'];
if (isset ($_POST['sidx']))
	$sidx = $_POST['sidx'];
$count = $numfilas;
$count = $numfilas;
if( $count >0 )
	$total_pages = ceil($count/$limit); 
else
	$total_pages = 0; 

if( $page > $total_pages )
	$page = $total_pages;

if( $limit<0 )
	$limit = 0;

$start = $limit*$page - $limit;if( $start<0 )
	$start = 0;

$datos['orderby'] = $sidx." ".$sord;
$datos['limit'] = "LIMIT ".$start." , ".$limit;

if(!$oObjeto->BusquedaAvanzada ($datos,$resultado,$numfilas))
	die();

	$i = 0;
	$responce =new StdClass; 
	$responce->page = $page; 
	$responce->total = $total_pages; 
	$responce->records = $count;
	$responce->rows = array();
while ($fila = $conexion->ObtenerSiguienteRegistro($resultado))
{
	$linkedit = '<a class="btn btn-sm btn-info" href="javascript:void(0)" onclick="VisualizarLog('.$fila['IdFilaLog'].')" title="Visualizar" ><i class="fa fa-eye"></i>&nbsp;Ampliar</a>';

	$Fecha = FuncionesPHPLocal::ConvertirFecha($fila['UltimaModificacionFecha'],"aaaa-mm-dd","dd/mm/aaaa")." ".substr($fila['UltimaModificacionFecha'],11,5)."Hs";

    $AdmiteSuplente = ($fila['AdmiteSuplente'] == 1 ? 'S&iacute;' : 'No');

	$datosmostrar = array(
		utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['IdTipoCargodesc'],ENT_QUOTES)),
		utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Codigo'],ENT_QUOTES)),
        $AdmiteSuplente,
        utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Descripcion'],ENT_QUOTES)),
		utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Esdeno'],ENT_QUOTES)),
		utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['EquivalenciaHs'],ENT_QUOTES)),
		utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Accion'],ENT_QUOTES)),
		$Fecha,
		$linkedit

	);
	$responce->rows[$i]['IdFilaLog'] = $fila['IdFilaLog'];
	$responce->rows[$i]['id'] = $fila['IdFilaLog'];
	$responce->rows[$i]['cell'] = $datosmostrar;
	$i++;
}

echo json_encode($responce);
?>