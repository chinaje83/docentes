<?php
require("./config/include.php");
require_once(DIR_CLASES_LOGICA.'cEscalafones.class.php');
$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes($conexion,array("roles"=>"si","sistema"=>SISTEMA));
$sesion = new Sesion($conexion,false);
$sesion->TienePermisos($conexion,$_SESSION['usuariocod'],$_SESSION['rolcod'],$_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

$oObjeto = new cEscalafones($conexion,"");
header('Content-Type: text/html; charset=iso-8859-1');
if (isset ($_POST['page']))
    $page = $_POST['page'];
else
    $page = 1;

if (isset ($_POST['rows']))
    $limit = $_POST['rows'];
else
    $limit = 1;

$sidx = "IdEscalafon";
$sord = "DESC";

$datos = $_SESSION['BusquedaAvanzada'] = $_POST;

if(!$oObjeto->BusquedaAvanzada ($datos,$resultado,$numfilas))
    die();

if (isset ($_POST['sord']))
    $sord = $_POST['sord'];
if (isset ($_POST['sidx']))
    $sidx = $_POST['sidx'];
$count = $numfilas;
if( $count >0 )
    $total_pages = ceil($count/$limit);
else
    $total_pages = 0;

if( $page > $total_pages )
    $page = $total_pages;

if( $limit<0 )
    $limit = 0;

$start = $limit*$page - $limit; if( $start<0 )
    $start = 0;

$datos['orderby'] = $sidx." ".$sord;
$datos['limit'] = "LIMIT ".$start." , ".$limit;

if(!$oObjeto->BusquedaAvanzada ($datos,$resultado,$numfilas))
    die();

$i = 0;
$responce = new StdClass;
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$responce->rows = array();
while ($fila = $conexion->ObtenerSiguienteRegistro($resultado))
{
    $linkedit = '<a class="btn btn-sm btn-info" href="esc_escalafones_am.php?IdEscalafon='.$fila["IdEscalafon"].'" title="Editar" id="editar_'.$fila['IdEscalafon'].'"><i class="fas fa-edit" aria-hidden="true"></i>&nbsp;Editar</a>';    $tipoactivacion = 5;
    $class = "btn-default";
    $classInactivo = "btn-danger disabled";
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
    $linkestado .= '<input type="checkbox" '.$checked.' onclick="ActivarDesactivar('.$fila['IdEscalafon'].',' .$tipoactivacion. ')" name="opcion_'.$fila['IdEscalafon'].'" class="onoffswitch-checkbox" id="opcion_'.$fila['IdEscalafon'].'">';
    $linkestado .= '<label class="onoffswitch-label" for="opcion_'.$fila['IdEscalafon'].'">';
    $linkestado .= '<span class="onoffswitch-inner"></span>';
    $linkestado .= '<span class="onoffswitch-switch"></span>';
    $linkestado .= '</label>';
    $linkestado .= '</div>';
    $datosmostrar = array(
			utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['IdEscalafon'],ENT_QUOTES)),
			utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['IdEscalafonExterno'],ENT_QUOTES)),
			utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Nombre'],ENT_QUOTES)),
			utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Descripcion'],ENT_QUOTES)),
			utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['IdRegimenSalarial'],ENT_QUOTES)),
			$linkestado,
			$linkedit

    );
    $responce->rows[$i]['IdEscalafon'] = $fila['IdEscalafon'];
    $responce->rows[$i]['id'] = $fila['IdEscalafon'];
    $responce->rows[$i]['cell'] = $datosmostrar;
    $i++;
}

echo json_encode($responce);
?>