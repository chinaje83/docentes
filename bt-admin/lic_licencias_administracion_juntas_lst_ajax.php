<?php
require("./config/include.php");
require(DIR_LIBRERIAS.'CurlBigtree.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS.'cServiciosLicencias.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS.'cJuntasTipos.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS.'cJuntasMotivos.class.php');

$conexion = new accesoBDLocal(SERVIDORBD, USUARIOBD, CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes($conexion,array("roles"=>"si","sistema"=>SISTEMA));

$sesion = new Sesion($conexion,false);
$sesion->TienePermisos($conexion,$_SESSION['usuariocod'],$_SESSION['rolcod'],$_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

$oObjeto = new cServiciosLicencias($conexion);

$datos = $_SESSION['BusquedaAvanzada'] = $_POST;

header('Content-Type: application/json');

if(!$oObjeto->setUtf8(true)->buscarJuntasxLicencias($datos, $resultado, $total)) {
	$err = $oObjeto->getError();
	die(json_encode($response));
}

$response = new StdClass;
$response->page = 1;
$response->total = 1;
$response->records = $total;
$response->rows = array();

foreach($resultado as $i => $fila) {

	$link = '';
	$ahora = new DateTime();
	$fecha = new DateTime($fila['Fecha']);
	$fechaComparacion = new DateTime(sprintf("%s + %d minutes", $fila['Fecha'], TOLERANCIA));
	switch ($fila['IdEstado']) {
		case 1:
			$class = 'table-default';
			if($ahora >= $fechaComparacion)
				$class = 'table-warning';
    		break;
		case 2:
            $class = 'table-warning';
            break;
        case 6:
			$class = 'table-danger';
			break;
		case 3:
        case 5:
			$class = 'table-success';
			break;
		case 4:
			$class = 'table-dark';
			break;
		default:
			$class = 'table-default';
	}

    $link .= "<a href=\"javascript:void(0);\" class=\"btn btn-xs btn-info btnEditarJunta\" data-id='{$fila['Id']}' id=\"btnEditarJunta_{$fila['Id']}\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"Editar datos de Junta/visita\" data-estado=\"{$fila['IdEstado']}\"> <i class=\"fas fa-edit\"></i></a>&nbsp;";

    if(!empty($fila['Comentarios']))
		$link .= "<a class='btn btn-xs btn-info btnInfo' id='btnInfo_{$fila['Id']}' href='javascript:void(0);' title='Comentarios' data-content='{$fila['Comentarios']}' data-toggle='popover' data-container='body'  data-placement='right'  ><i class='fas fa-comment-dots' aria-hidden='true'></i></a>";
	
	
	$datosmostrar = array (
		$fila['Id'],
		$fecha->format('d/m/Y'),
		$fecha->format('H:i'),
        ($fila['NombreRegion'] ?? '-'),
        $fila['Tipo'],
        $fila['Motivo'],
		$fila['EstadoNombre'],
		$link
	);
	
	$response->rows[$i]['cell'] = $datosmostrar;
	$response->rows[$i]['class'] = $class;
	$response->rows[$i]['tooltip'] = $fila['Comentarios'];
}

echo json_encode($response);
