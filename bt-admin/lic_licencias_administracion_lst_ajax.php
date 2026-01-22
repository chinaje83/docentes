<?php

use Bigtree\ExcepcionDB;

require("./config/include.php");
require("./config/include_elastic.php");
require(DIR_LIBRERIAS.'CurlBigtree.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS.'cServiciosLicencias.class.php');
require_once(DIR_CLASES_LOGICA.'cRolesModulosAcciones.class.php');
//require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cPersonas.class.php');

$conexion = new accesoBDLocal(SERVIDORBD, USUARIOBD, CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);
$conexionES = new Elastic\Conexion();

FuncionesPHPLocal::CargarConstantes($conexion,array("roles"=>"si","sistema"=>SISTEMA));
$sesion = new Sesion($conexion,false);
$sesion->TienePermisos($conexion,$_SESSION['usuariocod'],$_SESSION['rolcod'],$_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);


$oServicio = new cServiciosLicencias($conexion);
$oDashboards = new cDashboards($conexion);


$oObjeto = new Elastic\Licencias($conexionES);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

$datos = $_SESSION['BusquedaAvanzadaMedicas'] = $oDashboards->buscarxTipoAcceso($_POST);
$datos['IdTipo'] = 1;

if (isset($datos['Inicio']) && $datos['Inicio'] != "")
    $datos['Inicio'] = FuncionesPHPLocal::ConvertirFecha($datos["Inicio"],"dd/mm/aaaa","aaaa-mm-dd");

if (isset($datos['Fin']) && $datos['Fin'] != "")
    $datos['Fin'] = FuncionesPHPLocal::ConvertirFecha($datos["Fin"],"dd/mm/aaaa","aaaa-mm-dd");


header('Content-Type: application/json');

$response = new StdClass;
$response->page = 1;
$response->total = 1;
$response->records = 0;
$response->rows = array();

if(!$oServicio->BuscarTipoLicenciasxCodigo($datos, $datosTipoLicencia)) {
	$response->error = FuncionesPHPLocal::ConvertiraUtf8($oServicio->getError());
	die(json_encode($response));
}

$oObjetoAcciones = new cRolesModulosAcciones($conexion);
$acciones = $oObjetoAcciones->BuscarAccionesxRol(['IdRol' => array_values($_SESSION['rolcod'])[0]]);

$ahora = new DateTime();
$intervaloAmarillo = new DateInterval("PT{$datosTipoLicencia['AmarilloDesde']}H");
$intervaloRojo = new DateInterval("PT{$datosTipoLicencia['RojoDesde']}H");

$page = $_POST['page'] ?? $page = 1;
$limit = $_POST['rows'] ?? $limit = 1;

$sidx = $_POST['sidx'] ?? 'Id';
$sord = $_POST['sord'] ?? 'DESC';

if( $limit<0 )
	$limit = 0;

$start = $limit*$page - $limit;

if ( $start < 0 )
	$start = 0;

$datos['from'] = $start;
$datos['size'] = $limit;
$datos['sort'] = [['field'=>$sidx, 'order'=>$sord]];
if($sidx== 'Cargos.Escuelas.Id')
	$datos['sort'][0]['path']='Cargos';

$datos['excluirCampos'] = ['UltimaModificacion'];
$datos['estadosExcluir'] = [1];
$datos['esControl'] = true;


if (isset($_SESSION['IdEscuela']) && $_SESSION['IdEscuela'] != "")
    $datos['IdEscuela'] = $_SESSION['IdEscuela'];



$sql = sprintf(
    "SELECT * FROM Feriados WHERE Estado = 10"
);
try {
    $conexion->_EjecutarQuery($sql, __FILE__, $resultado_feriados, $errno);
} catch (ExcepcionDB $e) {
    die($e->getMessage() . PHP_EOL . PHP_EOL);
}
$diasFeriados = [];
if ($conexion->ObtenerCantidadDeRegistros($resultado_feriados) > 0) {
    while ($fila = $conexion->ObtenerSiguienteRegistro($resultado_feriados)) {
        $diasFeriados[] = $fila['Dia'];
    }
}

$datos['DiasHabilesAnteriores']=FuncionesPHPLocal::obtenerDiasAnteriores([1,2],$diasFeriados);




$oDashboards = new cDashboards($conexion, FMT_ARRAY);
$datos = $oDashboards->buscarxTipoAcceso($datos);
if(!$oObjeto->busquedaAvanzada($datos, $resultado, $numfilas, $total)) {
	$response->error = $oObjeto->getError();
	die(json_encode($response));
}

if( $total >0 )
	$total_pages = ceil($total/$limit);
else
	$total_pages = 0;

if ($total_pages > 500)
    $total_pages = 10000/$limit;

if( $page > $total_pages )
	$page = $total_pages;

$i = 0;
$response->page = $page;
$response->total = $total_pages;
$response->records = $total;
$response->rows = array();

//busco feriados
/** Busco feriados existentes */

if (!empty($resultado) && is_array($resultado)) {

    foreach($resultado as ['_source'=>$fila])
    {
        $Aprobante = '-';
        if (!FuncionesPHPLocal::isEmpty($fila['Aprobante'])) {
            $Aprobante = '<div>'.$fila['Aprobante']['Usuario']['Nombre'].'<br><small>'.$fila['Aprobante']['Fecha'].'</small></div>';
        }

        $linkAud="";
        if (isset($acciones['542']) && in_array(AC_009940, $acciones['542'])){
            $linkAud = '<a class="btn btn-info btn-sm font-bold" href="/licencias/todas/'.$fila['Id'].'" title="Ver auditorias" id="editar_'.$fila['Id'].'"><i class="fas fa-sign-in-alt"></i></a>';//Ver
        }


        FuncionesPHPLocal::ArmarLinkMD5('lic_licencias_administracion_am.php', ['Id' => $fila['Id']], $get, $md5);
        $linkedit = '<a class="btn btn-sm btn-outline-info" href="/licencias/medicas/revision/'.$fila['Id'].'/'.$md5.'" title="Editar" id="editar_'.$fila['Id'].'"><i class="fas fa-edit" aria-hidden="true"></i>&nbsp;Revisar</a>';
        $Inicio = FuncionesPHPLocal::ConvertirFecha($fila['Inicio'],"aaaa-mm-dd","dd/mm/aaaa");

        if(FuncionesPHPLocal::isEmpty($fila["FechaFinAbierta"]) || $fila["FechaFinAbierta"] != "1") {
            $Fin    = isset($fila['Fin']) ? FuncionesPHPLocal::ConvertirFecha($fila['Fin'],"aaaa-mm-dd","dd/mm/aaaa") : 'N/A';
            $Duracion = $fila['Duracion'].' d&iacute;as';
        } else {
            $Fin = "-";
            $Duracion = "Continua";
        }

        $Diagnostico    = (isset($fila['Diagnostico']['Nombre']) ? FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Diagnostico']['Nombre'].' '.$fila['Diagnostico']['Descripcion'],ENT_QUOTES) : 'N/A');
        $NombreCompleto = FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Persona']['NombreCompleto'],ENT_QUOTES);
        $estadoMostrar  = isset($fila['Estado']['MostrarTmpHasta']) && time() > $fila['Estado']['MostrarTmpHasta'] ? $fila['Estado']['NombrePublico'] : $fila['Estado']['NombrePublicoTmp'];


		$Escuela = [];
		foreach($fila['Cargos'] as $cargo ){
			if (!FuncionesPHPLocal::isEmpty($cargo['Escuela']['Codigo'])) {
				if (empty($Escuela) || !in_array($cargo['Escuela']['Codigo'], $Escuela)) {
					$Escuela[] = $cargo['Escuela']['Codigo'];

				}

			}
		}
		(!empty($Escuela) ? implode(', ', $Escuela) : '-');

        try {
	        $fechaEnvio = new DateTimeImmutable($fila['FechaEnvio']??$fila['Alta']['Fecha']);
        } catch (Exception $e) {
        	$response->error = ['error'=>$e->getCode(), 'error_description'=>$e->getMessage()];
        	$response->rows = [];
        	die(json_encode($response));
        }
        $primerDiaDesdeHabil=FuncionesPHPLocal::_obtenerNesimoDiaHabil($fila['FechaEnvio']??$fila['Alta']['Fecha'], 1, $diasFeriados, false);
        $segundoDiaDesdeHabil=FuncionesPHPLocal::_obtenerNesimoDiaHabil($fila['FechaEnvio']??$fila['Alta']['Fecha'], 2, $diasFeriados, false);
        $amarilloDesde = new DateTimeImmutable($primerDiaDesdeHabil);
        $rojoDesde = new DateTimeImmutable($segundoDiaDesdeHabil);

	    $class = 'table-';
        switch (true) {
	        case !in_array($fila['Estado']['Class'], ['warning', 'danger']):
		        $class .= (time() > $fila['Estado']['MostrarTmpHasta'] ? $fila['Estado']['Class'] : $fila['Estado']['ClassTmp']);
		        break;
	        case FuncionesPHPLocal::isDateBetweenDates($ahora, $amarilloDesde, $rojoDesde, -1):
		        $class .= 'warning';
		        break;
	        case FuncionesPHPLocal::isDateBetweenDates($ahora, $rojoDesde, null, -1):
		        $class .= 'danger';
		        break;
	        default:
		        $class .= 'default';

        }

        $Articulos = ''; $idArticulos = [];
        foreach ($fila['Cargos'] as $r) {
            if (!FuncionesPHPLocal::isEmpty($r['Articulo']['Id'])) {
                if (empty($idArticulos) || !in_array($r['Articulo']['Id'], $idArticulos)) {
                    $idArticulos[] = $r['Articulo']['Id'];
                    $Articulos .= $r['Articulo']['Codigo'] . ' ' . $r['Articulo']['Descripcion']. ($r['Articulo']['EsAuxiliar'] ? ' (Aux)' : '').'<br>';
                }
            }
        }

        $datosBuscar['IdLicencia'] = $fila['Id'];
        if (!$oServicio->buscarJuntasxLicencias($datosBuscar, $resultadoJunta, $numfilasJunta))
            return false;

        $junta = '-';
        if ($numfilasJunta > 0) {
            foreach ($resultadoJunta as $rj) {
                $fecha = new DateTime($rj['Fecha']);
                $junta = $rj['Id'].' - '.utf8_encode($rj['NombreRegion']).'<br>'.utf8_encode($rj['EstadoNombre']).'<br><small>'.$fecha->format('d/m/Y').' - '.$fecha->format('H:i').'</small>';
                break;
            }
        }

        $datosmostrar = array (
            $fila['Id'],
            $NombreCompleto.'<br>'.$fila['Persona']['Dni'],
			$Escuela,
            $Inicio,
            $Fin,
            $Duracion,
            $Articulos ?: '-',
            $Diagnostico,
            $estadoMostrar,
            $fechaEnvio->format('d/m/Y H:i:s'),
            $Aprobante,
            $junta,
            $linkedit,
            $linkAud
        );

	    $response->rows[$i]['cell'] = $datosmostrar;
	    $response->rows[$i]['class'] = $class;
        $i++;
    }
}

echo json_encode($response);

