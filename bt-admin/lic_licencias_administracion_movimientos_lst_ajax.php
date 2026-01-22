<?php

use Bigtree\Logica\Movimientos;

require("./config/include.php");
require(DIR_LIBRERIAS . 'CurlBigtree.php');
require_once(DIR_CLASES_LOGICA . 'cMovimientos.class.php');

$conexion = new accesoBDLocal(SERVIDORBD, USUARIOBD, CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes($conexion, ["roles" => "si", "sistema" => SISTEMA]);

$sesion = new Sesion($conexion, false);
//$sesion->TienePermisos($conexion, $_SESSION['usuariocod'], $_SESSION['rolcod'], $_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

$oObjeto = new Movimientos($conexion);

$datos = $_SESSION['BusquedaAvanzada'] = $_POST;

header('Content-Type: application/json');
if (isset ($_POST['page']))
    $page = $_POST['page'];
else
    $page = 1;

if (isset ($_POST['rows']))
    $limit = $_POST['rows'];
else
    $limit = 1;

$sidx = 'IdMovimiento';
$sord = 'DESC';

if (!$oObjeto->BuscarMovimientosxLicencia($datos, $resultado, $numfilas)) {
    die();
}

if (isset ($_POST['sord']))
    $sord = $_POST['sord'];
if (isset ($_POST['sidx']))
    $sidx = $_POST['sidx'];
$count = $numfilas;
$count = $numfilas;
if ($count > 0)
    $total_pages = ceil($count / $limit);
else
    $total_pages = 0;

if ($page > $total_pages)
    $page = $total_pages;

if ($limit < 0)
    $limit = 0;

$start = $limit * $page - $limit;
if ($start < 0)
    $start = 0;

$datos['orderby'] = $sidx . ' ' . $sord;
$datos['limit'] = 'LIMIT ' . $start . ' , ' . $limit;

if (!$oObjeto->BuscarMovimientosxLicencia($datos, $resultado, $numfilas)) {
    die();
}
$i = 0;
$responce = new StdClass;
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$responce->rows = [];
foreach ($resultado as $i => $fila) {

    $datosmostrar = [
        $fila['IdMovimiento'],
        $fila['IdPuesto'],
        utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['NombreMov'], ENT_QUOTES)),
        $fila['FechaMovimiento'],
        utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($fila['NombreEstado'], ENT_QUOTES)),
    ];

    $responce->rows[$i]['IdMovimiento'] = $fila['IdMovimiento'];
    $responce->rows[$i]['id'] = $fila['IdMovimiento'];
    $responce->rows[$i]['cell'] = $datosmostrar;
    $i++;
}

echo json_encode($responce);
