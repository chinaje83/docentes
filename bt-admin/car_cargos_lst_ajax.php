<?php
require("./config/include.php");
require_once(DIR_CLASES_LOGICA.'cCargos.class.php');$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes($conexion,array("roles"=>"si","sistema"=>SISTEMA));
$sesion = new Sesion($conexion,false);
$sesion->TienePermisos($conexion,$_SESSION['usuariocod'],$_SESSION['rolcod'],$_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

$oObjeto = new cCargos($conexion,"");
header('Content-Type: text/html; charset=iso-8859-1');
if (isset ($_POST['page']))
	$page = $_POST['page'];
else
	$page = 1;

if (isset ($_POST['rows']))
	$limit = $_POST['rows'];
else
	$limit = 1;

$sidx = "IdCargo";
$sord = "DESC";

$datos = $_SESSION['BusquedaAvanzada'] = $_POST;

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
//var_dump($fila = $conexion->ObtenerSiguienteRegistro($resultado));
//die();
	$i = 0;
	$responce =new StdClass;
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$responce->rows = array();
while ($fila = $conexion->ObtenerSiguienteRegistro($resultado))
{
	$linkedit = '<a class="btn btn-sm btn-info" href="car_cargos_am.php?IdCargo='.$fila["IdCargo"].'" title="Editar" id="editar_'.$fila['IdCargo'].'"><i class="fas fa-edit" aria-hidden="true"></i>&nbsp;Editar</a>';
	$tipoactivacion = 5;
	$class = "btn-default";
	$classInactivo = "btn-danger disabled";
	$classmul = "";
	$style= "";
	$checked= "";
	if ($fila['Estado']==ACTIVO)
	{
		$style= 'style="color:#FFF"';
	$checked= "checked='checked'";
		$tipoactivacion = 4;
		$class = "btn-success disabled";
		$classInactivo = "btn-default";
	}

	$linkestado = '<div class="onoffswitch">';
	$linkestado .= '<input type="checkbox" '.$checked.' onclick="ActivarDesactivar('.$fila['IdCargo'].','.$tipoactivacion.')" name="opcion_'.$fila['IdCargo'].'" class="onoffswitch-checkbox"   id="opcion_'.$fila['IdCargo'].'">';
	$linkestado .= '<label class="onoffswitch-label" for="opcion_'.$fila['IdCargo'].'">';
	$linkestado .= '<span class="onoffswitch-inner"></span>';
	$linkestado .= '<span class="onoffswitch-switch"></span>';
	$linkestado .= '</label>';
	$linkestado .= '</div>';

    $AdmiteSuplente = ($fila['AdmiteSuplente'] == 1 ? 'S&iacute;' : 'No');


    $datosmostrar = array(
		$fila['IdCargo'],
		utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Codigo'],ENT_QUOTES)),
        $fila['IdExterno'],
        utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['IdTipoCargodesc'],ENT_QUOTES)),
		utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Descripcion'],ENT_QUOTES)),
		$fila['Regimen'],
        $AdmiteSuplente,
        utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['EscalafonDescripcion'],ENT_QUOTES)),
        utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['DesempenoLugarNombre'],ENT_QUOTES)),
		$linkestado,
		$linkedit
	);
	$responce->rows[$i]['IdCargo'] = $fila['IdCargo'];
	$responce->rows[$i]['id'] = $fila['IdCargo'];
	$responce->rows[$i]['cell'] = $datosmostrar;
	$i++;
}

echo json_encode($responce);
?>
