<?php

use Bigtree\ExcepcionLogica;

ob_start();
require("./config/include.php");
require("./config/include_elastic.php");
require(DIR_LIBRERIAS . 'CurlBigtree.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cServiciosLicencias.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cServiciosUsuarios.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cServiciosFamiliares.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cArticulos.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cEspecialidadesAutorizantes.class.php');

$conexion = new accesoBDLocal(SERVIDORBD, USUARIOBD, CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes($conexion, ["roles" => "si", "sistema" => SISTEMA, "multimedia" => "si"]);
$conexion->SetearAdmiGeneral(ADMISITE);

$sesion = new Sesion($conexion, false);
$sesion->TienePermisos($conexion, $_SESSION['usuariocod'], $_SESSION['rolcod'], $_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

header('Content-Type: application/json');
$_POST = FuncionesPHPLocal::DecodificarUtf8($_POST);
$msg = [];
$msg['IsSucceed'] = false;
$datos = $_POST;


if (!isset($datos['accion']) || $datos['accion'] == "") {
    $msg['Msg'] = "Error al procesar";
    echo json_encode($msg);
    ob_end_flush();
    die();
}

$oObjeto = new cServiciosLicencias($conexion);
switch ($datos['accion']) {
    case 1:
        $IdTipo = $datos['IdTipo'];
        if ($oObjeto->BuscarPersona($datos, $resultado, $numfilas)) {
            if (count($resultado) != 1) {
                $msg['IsSucceed'] = false;
                $msg['Msg'] = "No se encontraron resultados.";
            } else {
                $msg['IsSucceed'] = true;
                $IdPersona = $resultado[0]['_source']['Id'];
                $Dni = $resultado[0]['_source']['Documento']['Numero'];
                $Cuil = $resultado[0]['_source']['CUIL'];
                $NombreCompleto = (FuncionesPHPLocal::HtmlspecialcharsSistema($resultado[0]['_source']['NombreCompleto'], ENT_QUOTES));
                $msg['Resultado'] = <<<HTML
				<div id="cardResultadoPersona">
                    <div class="form">
                        <form class="form-material" action="/licencias/nueva" method="post" name="formResultadoPersona" id="formResultadoPersona">
                            <div class="row">
HTML;
                if ($Cuil != "") {
                    $msg['Resultado'] .= <<<HTML
                                <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div class="form-group clearfix">
                                        <label for="Cuil">CUIL</label>
                                        <input type="text" class="form-control input-md " maxlength="11" name="Cuil" id="Cuil" value="{$Cuil}" readonly/>
                                    </div>
                                </div>
HTML;
                }

                $msg['Resultado'] .= <<<HTML

                                <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div class="form-group clearfix">
                                        <label for="Nombre">Nombre completo</label>
                                        <input type="text" class="form-control input-md " maxlength="11" name="Nombre" id="Nombre" value="{$NombreCompleto}" readonly/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
HTML;

                $msg['Resultado'] .= <<<HTML
                                </div>
                                <input type="hidden" id="IdPersona" name="IdPersona" value="{$IdPersona}">
                                <input type="hidden" name="IdTipo" id="IdTipo" value="{$IdTipo}">
                                <input type="hidden" name="Dni" id="Dni" value="{$Dni}">
                            </form>
                        </div>
                </div>
HTML;
            }
        }
        break;
    case 2:
        $readonly = $resultado['Id'] = $Nombre = $Apellido = '';
        if ($oObjeto->BuscarAutorizantexId($datos, $resultado, $numfilas)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Se ha agregado correctamente a las " . date("H") . ":" . date("i") . "Hs";
            $Nombre = utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($resultado['Nombre'], ENT_QUOTES));
            $Apellido = utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($resultado['Apellido'], ENT_QUOTES));
            $readonly = "readonly";
        }

        if (FuncionesPHPLocal::isEmpty($resultado['Id'])) {
            $msg['Msg'] = utf8_encode('Matricula ingresada inexistente. Seleccione un tipo de autorizante y matricula valida para avanzar.');
        }

        $cols = 'col-md-6';
        if (!FuncionesPHPLocal::isEmpty($datos['Revision']) && $datos['Revision'])
            $cols = '';

        $msg['Resultado'] = '
        <div id="DatosMatricula">
            <div class="row">
                <div class="col-12" id="NroMatricula">
                    <div class="form-group clearfix">
                        <label for="Matricula">Matricula</label>
                        <br><small>Ingrese la matricula o el nombre del autorizante de la licencia</small>
                        <input type="text" class="form-control input-md " name="Matricula" id="Matricula" value="' . $resultado['Matricula'] . '" autocomplete="off"/>
                    </div>
                </div>
            </div>';

        if (!FuncionesPHPLocal::isEmpty($resultado['Id'])) {

            $msg['Resultado'] .= '
            <div class="row">
                <div class="col-12 ' . $cols . '">
                    <div class="form-group clearfix">
                        <label for="TipoAutorizante">Tipo de autorizante</label>
                        <select class="form-control input-md " name="TipoAutorizante" id="TipoAutorizante">
                            <option value="' . $resultado['IdTipoAutorizante'] . '">' . utf8_encode($resultado['NombreTipoAutorizante']) . '</option>
                        </select>
                    </div>
                </div>


                <div class="col-12 ' . $cols . '">
                    <div class="form-group clearfix">
                        <label for="IdEspecialidad">Especialidad</label>
                        <select name="IdEspecialidad" id="IdEspecialidad" class="form-control input-md">
                            <option value="">Seleccione</option>';

            if (!FuncionesPHPLocal::isEmpty($resultado['Id'])) {

                $datosBusqueda['IdAutorizante'] = $resultado['Id'];
                $oEspecialidadAutorizante = new cEspecialidadesAutorizantes($conexion);

                if (!$oEspecialidadAutorizante->BuscarxCodigoAutorizante($datosBusqueda, $resultadoEspecialidad, $numfilasEspecialidad))
                    return false;

                if ($numfilasEspecialidad > 0) {

                    if ($resultadoEspecialidad['total'] == 0 && $resultadoEspecialidad['Id'] == "") {
                        $msg['Resultado'] .= '<option value="0" selected>Sin asignar</option>';
                    } else {
                        foreach ($resultadoEspecialidad['filas'] as $r) {
                            $msg['Resultado'] .= '<option value="' . $r['IdEspecialidad'] . '" ' . (count($resultadoEspecialidad['filas']) == 1 ? 'selected' : '') . '>' . utf8_encode($r['Nombre']) . '</option>';
                        }
                    }
                }
            }

            $msg['Resultado'] .= '
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group clearfix">
                        <label for="NombreAutorizante">Nombre completo</label>
                        <input type="text" class="form-control input-md " maxlength="20" name="NombreAutorizante" id="NombreAutorizante" value="' . $Nombre . ' ' . $Apellido . '" ' . $readonly . ' autocomplete="off" />
                    </div>
                </div>
                <input type="hidden" id="IdAutorizante" name="IdAutorizante" value="' . $resultado['Id'] . '">
                <input type="hidden" id="EncontroAutorizante" name="EncontroAutorizante" value="1">
            </div>';
        }
        $msg['Resultado'] .= '</div>';

        break;
    case 3:
        $Nombre = $Apellido = '';
        $resultado['Id'] = '';
        $oObjeto = new cServiciosFamiliares($conexion);
        if ($oObjeto->BuscarFamiliarxDni($datos, $resultado, $numfilas)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Se ha agregado correctamente a las " . date("H") . ":" . date("i") . "Hs";
            $Nombre = utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($resultado['Nombre'], ENT_QUOTES));
            $Apellido = utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($resultado['Apellido'], ENT_QUOTES));

        }
        $msg['Resultado'] = <<<HTML
            <div class="row" id="DatosFamilia">
                <div class="col-md-6 col-xs-12 col-sm-6">
                    <div class="form-group clearfix">
                        <label for="FamiliarNombre">Nombre</label>
                        <input type="text" class="form-control input-md " maxlength="9" name="FamiliarNombre" id="FamiliarNombre" value="{$Nombre}" autocomplete="off" />
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 col-sm-6">
                    <div class="form-group clearfix">
                        <div class="form-group clearfix">
                            <label for="FamiliarApellido">Apellido</label>
                            <input type="text" class="form-control input-md " maxlength="9" name="FamiliarApellido" id="FamiliarApellido" value="{$Apellido}" autocomplete="off" />
                        </div>
                    </div>
                </div>
                <input type="hidden" id="IdFamiliar" name="IdFamiliar" value="{$resultado['Id']}">
            </div>
HTML;
        break;
    case 4:
        if ($datos['Id'] == 1) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Se ha agregado correctamente a las " . date("H") . ":" . date("i") . "Hs";
            $msg['Resultado'] = <<<HTML
            <div class="form-group mb-1" id="DetalleMotivo">
                <label for="Descripcion">S&iacute;ntomas manifestados</label>
                <textarea class="form-control" maxlength="255" rows="4" id="Descripcion" name="Descripcion"></textarea>
                <span class="bar"></span>
            </div>
HTML;
        } elseif ($oObjeto->ObtenerDiagnosticoDetalle($datos, $resultado, $numfilas)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Se ha agregado correctamente a las " . date("H") . ":" . date("i") . "Hs";

            $msg['Resultado'] = <<<HTML
            <div class="form-group clearfix" id="DetalleDiagnostico">
                <label for="IdDiagnosticoDetalle">CIE10 (4)</label>
                <select name="IdDiagnosticoDetalle" id="IdDiagnosticoDetalle" class="form-control input-md chzn-select">
                    <option value="">Seleccione</option>
HTML;
            foreach ($resultado['filas'] as $r) {
                $msg['Resultado'] .= <<<HTML
                    <option value="{$r['Id']}" > {$r['Nombre']} - {$r['Descripcion']} </option>
HTML;
            }
            $msg['Resultado'] .= <<<HTML
                </select>
            </div>
HTML;
        }
        break;
    case 5:
        if ($oObjeto->ObtenerObservacionesxIdLicencia($datos, $resultado, $numfilas)) {
            $msg['IsSucceed'] = true;
            $msg['Resultado'] = <<<HTML
            <div class="chat-rbox" style="height: auto; max-height: 280px; overflow: auto;">
                <ul class="chat-list">
HTML;
            if (isset($resultado['Comentarios']) && !empty($resultado['Comentarios'])) {
                $oUsuarios = new cServiciosUsuarios($conexion);

                foreach ($resultado['Comentarios'] as $r) {
                    /*     try {
                             $avatar = $oUsuarios->obtenerThumbnailUsuarioxId($r['IdUsuario']);
                         } catch (ExcepcionLogica $e) {
                             $avatar = '';
                         }*/
                    // $Avatar = '<img src="data:image/png;base64, ' . $avatar . '"" alt="user" />';
                    $Nombre = utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($r['NombreCompleto'], ENT_QUOTES));
                    $Observacion = utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($r['Observacion'], ENT_QUOTES));
                    $Hora = date("d/m/y G:i a", strtotime($r['Fecha']));

                    if ($r['IdUsuario'] == $_SESSION['usuariocod']) {
                        $msg['Resultado'] .= <<<HTML
                        <li class="reverse">
                            <div class="chat-content">
                                <h5>{$Nombre}</h5>
                                <div class="box bg-light-inverse">{$Observacion}</div>
                            </div>
                            <div class="chat-time">{$Hora}</div>
                        </li>
HTML;
                    } else {
                        $msg['Resultado'] .= <<<HTML
                        <li>
                            <div class="chat-content">
                                <h5>{$Nombre}</h5>
                                <div class="box bg-light-info">{$Observacion}</div>
                            </div>
                            <div class="chat-time p-r-20">{$Hora}</div>
                        </li>
HTML;
                    }
                }
            }

            $msg['Resultado'] .= <<<HTML
                </ul>
            </div>
HTML;
        }

        break;
    case 6:

        $msg['Resultado'] =
            '<div class="form-group clearfix mb-1" id="ComboArticulos">
                <label for="">Art&iacute;culo</label>
                <select name="IdArticulo" id="IdArticulo" class="form-control input-md chzn-select" ' . (isset($datos['Editable']) && $datos['Editable'] ? '' : 'disabled="disabled"') . '>
                    <option value="">Seleccione</option>';
        $oArticulos = new cArticulos($conexion);
        if (!$oArticulos->BuscarCombo($datos, $resultadoArticulos, $numfilasArticulos))
            return false;

        if ($numfilasArticulos > 0) {
            foreach ($resultadoArticulos['filas'] as $r) {
                $msg['Resultado'] .= '<option value="' . $r['IdArticulo'] . '" ' . ($IdArticulo == $r['IdArticulo'] ? 'selected' : '') . '>' . $r['Codigo'] . ' - ' . utf8_encode($r['Descripcion']) . '</option>';
            }
            $msg['IsSucceed'] = true;
        }
        $msg['Resultado'] .=
            '</select>
            </div>';
        break;
    case 7:
        $conexionES = new Elastic\Conexion();
        $oPersonas = new Elastic\Personas($conexionES);

        $datos['Dni'] = $datos['DniBusqueda'];
        if (!$oPersonas->buscarxDni($datos, $resultadoPersona, $numfilasPersona)) {
            $error = $oObjeto->getError();
            echo utf8_decode($error['error_description']);
            die;
        }

        if ($numfilasPersona == 0) {
            $msg['Msg'] = 'No se encontraron resultados.';
            break;
        }

        $datos['IdPersona'] = $resultadoPersona[0]['_source']['Id'];

        $oLicencias = new Elastic\Licencias($conexionES);

        if (!$oLicencias->buscarxPersona($datos, $resultadoLicencias, $numfilasLicencias, $totalLicencias)) {
            $error = $oObjeto->getError();
            echo utf8_decode($error['error_description']);
            die;
        }

        $oPuestos = new Elastic\Puestos($conexionES);

        if (!$oPuestos->buscarxPersona($datos, $resultadoPuestos, $numfilasPuestos, $totalPuestos)) {
            $error = $oObjeto->getError();
            echo utf8_decode($error['error_description']);
            die;
        }

        $PuestoPersona = $resultadoPuestos['hits']['hits'];
        $PuestoDetalle = $resultadoPuestos['aggregations']['PuestoPersona']['buckets'];
        $Puesto = [];

        foreach ($PuestoDetalle as $bucket)
            $Puesto[$bucket['key']] = current($bucket['Puestos']['Datos']['hits']['hits'])['_source'];

        foreach ($PuestoPersona as ['_source' => $resultadoPuestoPersona])
            $Puesto[$resultadoPuestoPersona['Id']]['DatosPersona'] = $resultadoPuestoPersona;

        /*        print '<pre>';
                print_r($resultadoPersona);
                print '</pre>';
                die;*/

        $msg['IsSucceed'] = true;
        $msg['Resultado'] = '
        <div class="row" id="cardResultado">
            <div class="col-lg-7 col-md-7">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card borde-top-recto h-100">
                            <div class="card-body pb-0">
                                <div class="d-flex flex-row">
                                    <div class="col-md-12 p-0">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="FotoAvatar">
                                                    <div id="avatarImg">
                                                        <img style="max-width:120px;" src="' . CARPETA_SERVIDOR_MULTIMEDIA . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR_L . 'default.png" id="avatarImg"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h4 class="card-title mt-0 mb-4">Datos Personales</h4>
                                                <h5><b>' . mb_strtoupper($resultadoPersona[0]['_source']['NombreCompleto']) . '</b></h5>
                                                <h5><b>Documento</b> ' . $resultadoPersona[0]['_source']['Documento']['Numero'] . '</h5>';

        if (isset($resultadoPersona[0]['_source']['CUIL']))
            $msg['Resultado'] .= '<h5><b>Cuil</b> ' . FuncionesPHPLocal::ConvertirFormatoCuit($resultadoPersona[0]['_source']['CUIL']) . '</h5>';

        if (isset($resultadoPersona[0]['_source']['Sexo']['Nombre']))
            $msg['Resultado'] .= '<h5><b>G&eacute;nero</b> ' . $resultadoPersona[0]['_source']['Sexo']['Nombre'] . '</h5>';

        $msg['Resultado'] .= '
                                            </div>
                                            <div class="col-md-6">
                                                <h4 class="card-title mt-0 mb-4">Datos Contacto</h4>';

        if (isset($resultadoPersona[0]['_source']['Email']))
            $msg['Resultado'] .= '<h5><b>Email</b> ' . $resultadoPersona[0]['_source']['Email'] . '</h5>';

        if (isset($resultadoPersona[0]['_source']['Telefono']))
            $msg['Resultado'] .= '<h5><b>Tel&eacute;fono</b> ' . $resultadoPersona[0]['_source']['Telefono'] . '</h5>';

        $msg['Resultado'] .= '
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12  pt-4">
                        <div class="card h-100 mb-0">
                            <div class="card-body">
                                <h4 class="card-title">Puestos</h4>
                                <div class="table-responsive mt-2">
                                    <table class="table stylish-table">
                                        <thead>
                                        <tr>
                                            <th class="p-0">&nbsp;</th>
                                            <th class="p-0">&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody>';

        /* foreach ($Puesto as $r) {

               print '<pre>';
               print_r($r);
               print '</pre>';
           }
           die;*/

        foreach ($Puesto as $r) {

            $msg['Resultado'] .= '
                                            <tr>
                                                <td style="width: 50%">
                                                    <div><h5># ' . $r['Codigo'] . '</h5></div>
                                                    <div><h5>' . $r['Escuela']['CUE'] . ' ' . $r['Escuela']['Nombre'] . ' - ' . $r['Escuela']['Codigo'] . '</h5></div>
                                                    <div><h6>' . $r['Escuela']['Region']['Nombre'] . '</h6></div>
                                                    <div class="badge badge-info">' . $r['DatosPersona']['Revista']['Codigo'] . ' (' . $r['DatosPersona']['Revista']['Descripcion'] . ')</div>';
            $msg['Resultado'] .= '
                                                </td>
                                                <td style="width: 50%">

                                                <div><h5>Ciclo lectivo ' . $r['CicloLectivo'] . '</h5></div>';

            $Turno = ucwords(strtolower($r['Turno']['Descripcion']));

            if (isset($r['Materia'])) {
                $msg['Resultado'] .= '
                                                        <div><h5>Turno ' . $Turno . ' ' . $r['GradoAnio']['Descripcion'] . ' - ' . $r['SeccionDivision']['Descripcion'] . '</h5></div>
                                                        <div><h5>' . ucwords(strtolower($r['Materia']['Descripcion'])) . ' (' . $r['Materia']['Codigo'] . ')</h5></div>';

            } elseif (isset($r['Cargo'])) {
                $msg['Resultado'] .= '
                                                        <div><h5>Turno ' . $Turno . '</h5></div>
                                                        <div><h5>' . ucwords(strtolower($r['Cargo']['Descripcion'])) . ' (' . $r['Cargo']['Codigo'] . ')</h5></div>';
            }

            if (isset($r['DatosPersona']['EstadoPersona']['Fechas'])) {
                foreach ($r['DatosPersona']['EstadoPersona']['Fechas'] as $k => $fecha) {
                    try {
                        $enRango = FuncionesPHPLocal::isDateStringBetweenDates(date('Y-m-d'), $fecha['gte'], $fecha['lte'] ?? '', false);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                        break;
                    }

                    if ($enRango) {
                        $msg['Resultado'] .= '<div class="badge badge-warning">Licencia</div>';
                        break;
                    } else {
                        $msg['Resultado'] .= '<div class="badge badge-success">' . $r['DatosPersona']['EstadoPersona']['Descripcion'] . '</div>';
                    }
                }
            } else {
                $msg['Resultado'] .= '<div class="badge badge-success">' . $r['DatosPersona']['EstadoPersona']['Descripcion'] . '</div>';
            }

            $msg['Resultado'] .= '
                                                </td>
                                            </tr>';
        }
        $msg['Resultado'] .= '
                                        </tbody>
                                    </table>
                                </div>';
        $msg['Resultado'] .= '
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-5">
                <div class="card borde-top-recto h-100">
                    <div class="card-body">
                        <h4 class="card-title">Licencias</h4>
                        <div class="table-responsive mt-2">
                            <table class="table stylish-table">
                                <thead>
                                <tr>
                                    <th style="width: 30%">Estado</th>
                                    <th style="width: 15%">Desde</th>
                                    <th style="width: 15%">Hasta</th>
                                    <th style="width: 5%">&nbsp;</th>
                                    <th style="width: 5%">&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>';

        foreach ($resultadoLicencias as $data) {

            $fila = $data['_source'];

            switch ($fila['Estado']['Id']) {
                case 4:
                case 5:
                    $Estado = "label-light-success";
                    break;
                default:
                    $Estado = "label-light-danger";
            }

            $msg['Resultado'] .= '
                                    <tr>
                                        <td style="width: 30%"><span style="width: 90px; text-align: center;" class="label ' . $Estado . '">' . $fila['Estado']['NombrePublico'] . '</span><br><small>Lic. ' . $fila['Tipo']['Nombre'] . ' ' . (isset($fila['Articulo']['Codigo']) ? 'Art. ' . $fila['Articulo']['Codigo'] : '') . '</small></td>
                                        <td style="width: 15%"><small>' . FuncionesPHPLocal::ConvertirFecha($fila['Inicio'], 'aaaa-mm-dd', 'dd-mm-aaaa') . '</small></td>
                                        <td style="width: 15%"><small>' . (isset($fila['Fin']) ? FuncionesPHPLocal::ConvertirFecha($fila['Fin'], 'aaaa-mm-dd', 'dd-mm-aaaa') : '-') . '</small></td>
                                        <td style="width: 5%"><a class="text-info font-bold" target="_blank" href="/licencias/' . $fila['Id'] . '" title="Editar" id="editar_3110">Ver</a></td>
                                        <td style="width: 5%">&nbsp;</td>
                                    </tr>';
        }

        $msg['Resultado'] .= '
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
        break;
    case 8:

        $oObjetoAcciones = new cRolesModulosAcciones($conexion);

        $accionesModulo = $oObjetoAcciones->BuscarAccionesxRol(['IdRol' => reset($_SESSION['rolcod'])])[AC_ID_MODULO_LICENCIAS] ?? [];

        $msg['IsSucceed'] = true;
        $msg['Resultado'] = "";
        if (empty($datos['Duracion']) && !empty($datos['Horas'])) {
            $datos['Duracion'] = $datos['Horas'];
            $datos['Unidad'] = '0';
        }

        if (!$oObjeto->buscarCargosAfectados($datos, $resultado, $numfilas, $total)) {
            printf('<div class="alert alert-warning"><p><i class="fas fa-exclamation-circle" aria-hidden="true"></i>&nbsp;%s</p></div>', $oObjeto->getError('error_description'));
            die();
        }

        $Puestos = [];

        # SUMO AL ARRAY LOS AFECTADOS EXISTENTES
        if (!FuncionesPHPLocal::isEmpty($datos['IdPuestoAfectado']))
            foreach ($datos['IdPuestoAfectado'] as $r)
                $Puestos[] = $r;

        #  O RECIEN AGREGADOS
        if (!FuncionesPHPLocal::isEmpty($datos['IdPuesto']))
            $Puestos[] = $datos['IdPuesto'];

        if ($numfilas > 0) {

            $msg['Resultado'] =
                '<table class="table table-sm" style="font-size: 14px;" id="TableCargosAfectados">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 7%">Id Pofa</th>
                            <th style="width: 7%">Puesto</th>
                            <th style="width: 45%"></th>
                            <th style="width: 15%">Horas/M&oacute;dulos</th>
                            <th style="width: 15%">Horas afectadas</th>
                            <th style="width: 10%">&nbsp;</th>
                        </tr>
                    </thead>
                <tbody>';

            foreach ($resultado as $r) {

                if (!is_null($r['DatosPuesto']['Id']) && in_array($r['DatosPuesto']['Id'], $Puestos)) {

                    $horas = '';
                    if (isset($r['DatosPuesto']['Puesto']['Catedra']['Cantidad'])) {
                        $horas = $r['DatosPuesto']['Puesto']['Catedra']['Cantidad'] . ' ' . $r['DatosPuesto']['Puesto']['Catedra']['Unidad']['NombreCorto'];
                    } else {
                        if ($r['DatosPuesto']['IdTipo'] == 1) {
                            $horas = $r['DatosPuesto']['Horas'] . ' horas';
                        } elseif ($r['DatosPuesto']['IdTipo'] == 2) {
                            $horas = $r['DatosPuesto']['Modulos'] . ' h. c&aacute;tedras';
                        } else {
                            $horas = $r['DatosPuesto']['Horas'] . ' horas';
                        }
                    }

                    $msg['Resultado'] .= '<tr>
                                            <td>' . $r['IdPofa'] . '</td>
                                            <td>';
                    $msg['Resultado'] .= (
                        (isset($_SESSION['IdEscuela']) && $r['DatosPuesto']['Escuela']['Id'] == $_SESSION['IdEscuela'])
                        || in_array(AC_009974, $accionesModulo)
                    )
                        ? FuncionesPHPLocal::EnlacePuestoPofa($r['DatosPuesto']['Escuela']['Id'], $r['DatosPuesto']["IdPuestoRaiz"], $r['DatosPuesto']["IdPuestoRaiz"])
                        : $r['DatosPuesto']["IdPuestoRaiz"];
                    $msg['Resultado'] .= '</td>
                                            <td>
                                                <strong>Escuela c&oacute;d. ' . $r['DatosPuesto']['Escuela']['Codigo'] . '&ensp;</strong>
                                                ' . (isset($r['DatosPuesto']['Nivel']['Descripcion']) ? $r['DatosPuesto']['Nivel']['Descripcion'] : '') . '
                                                <br>' . ($r['DatosPuesto']['CodigoPuesto'] ?? '') . '
                                                ' . (isset($r['DatosPuesto']['Turno']['NombreCorto']) && $r['DatosPuesto']['Turno']['NombreCorto'] <> '' ? '<br>Turno &ensp;' . $r['DatosPuesto']['Turno']['NombreCorto'] . ' ' : '') .
                        (isset($r['DatosPuesto']['GradoAnio']['NombreCorto']) ? $r['DatosPuesto']['GradoAnio']['NombreCorto'] : '') . ' ' .
                        (isset($r['DatosPuesto']['SeccionDivision']['Descripcion']) ? $r['DatosPuesto']['SeccionDivision']['Descripcion'] : '') .
                        '<br> Revista ' . ($r['Revista']['Codigo'] ?? '') . '
                                                &ensp;|&ensp; ' . (isset($r['DatosPuesto']['Cargo']['Descripcion']) ? $r['DatosPuesto']['Cargo']['Descripcion'] : '') . '
                                            </td>
                                            <td>' . $horas . '</td>
                                            <td>' . ($r['HorasAfectadas'] ?? '') . '</td>
                                            <td>
                                                <a class="btn btn-sm btn-outline-danger" href="javascript:void(0)" onclick="EliminarCargoAfectado(' . $r['DatosPuesto']['Id'] . ')" title="Eliminar" >
                                                <i class="far fa-trash-alt" aria-hidden="true"></i></a>
                                            </td>';
                    $msg['Resultado'] .= '</tr>';
                }
            }
            $msg['Resultado'] .=
                '</tbody>
            </table>';
        }

        break;
    case 9:
        if (!$oObjeto->calcularFechaFin($datos, $resultado))
            return false;

        $msg['Resultado'] = FuncionesPHPLocal::ConvertirFecha($resultado['FechaFin'], 'aaaa-mm-dd', 'dd/mm/aaaa');

        break;
    case 10:
        $readonly = $resultado['Id'] = $Nombre = $Apellido = '';
        if ($oObjeto->BuscarAutorizantexId($datos, $resultado, $numfilas)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Se ha agregado correctamente a las " . date("H") . ":" . date("i") . "Hs";
            $Nombre = utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($resultado['Nombre'], ENT_QUOTES));
            $Apellido = utf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema($resultado['Apellido'], ENT_QUOTES));
            $readonly = "readonly";
        }

        if (FuncionesPHPLocal::isEmpty($resultado['Id'])) {
            $msg['Msg'] = utf8_encode('Matricula ingresada inexistente. Seleccione un tipo de autorizante y matricula valida para avanzar.');
            break;
        }

        $msg['Resultado'] = '
        <div id="DatosMatriculaJunta">
            <div class="row">
                <div class="col-6">
                    <div class="form-group clearfix">
                        <label for="MatriculaJunta">Matricula</label>
                        <br><small>Ingrese la matricula o el nombre del autorizante</small>
                        <input type="text" class="form-control input-md" name="MatriculaJunta" id="MatriculaJunta" value="' . $resultado['Matricula'] . '" autocomplete="off"/>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group clearfix">
                        <br><label>' . $Nombre . '<br><small>' . utf8_encode($resultado['NombreTipoAutorizante']) . '</small></label>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="IdAutorizanteJunta" name="IdAutorizanteJunta" value="' . $resultado['Id'] . '">';
        break;
}

ob_clean();
echo json_encode($msg);
ob_end_flush();
