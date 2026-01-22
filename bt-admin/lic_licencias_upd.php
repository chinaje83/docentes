<?php
ob_start();
require("./config/include.php");
require("./config/include_elastic.php");
require(DIR_LIBRERIAS . 'CurlBigtree.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cServiciosLicencias.class.php');

use Bigtree\Logica\Movimientos;

$conexion = new accesoBDLocal(SERVIDORBD, USUARIOBD, CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes($conexion, ["roles" => "si", "sistema" => SISTEMA, "multimedia" => "si"]);
$conexion->SetearAdmiGeneral(ADMISITE);

$sesion = new Sesion($conexion, false);
$sesion->TienePermisos($conexion, $_SESSION['usuariocod'], $_SESSION['rolcod'], $_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

header('Content-Type: text/html; charset=iso-8859-1');

if (!FuncionesPHPLocal::isEmpty($_POST['Comentarios']) && in_array($_POST['accion'], [8, 9, 10, 13, 14]))
    $_POST['Comentarios'] = utf8_encode($_POST['Comentarios']);

$_POST = FuncionesPHPLocal::DecodificarUtf8($_POST);

$msg = [];
$msg['IsSucceed'] = false;
$datos = $_POST;


$conexion->ManejoTransacciones("B");

if (!isset($datos['accion']) || $datos['accion'] == "") {
    $msg['Msg'] = "Error al procesar";
    echo json_encode($msg);
    ob_end_flush();
    die();
}

$oObjeto = new cServiciosLicencias($conexion);

switch ($datos['accion']) {
    case 1:
        if ($oObjeto->CrearLicencia($datos, $codigoInsertado)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Se ha agregado correctamente a las " . date("H") . ":" . date("i") . "Hs";
            FuncionesPHPLocal::ArmarLinkMD5('lic_licencias_am.php', ['Id' => $codigoInsertado], $get, $md5);
            $msg['header'] = sprintf("/licencias/%s/%s", $codigoInsertado, $md5);
        } else {
            $error = $oObjeto->getError();
            echo $error['error_description'];
        }
        break;

    case 2:

        if ($oObjeto->ModificarLicencia($datos)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Se ha modificado correctamente a las " . date("H") . ":" . date("i") . "Hs";
            $msg['header'] = $_SERVER['HTTP_REFERER'];
        } else {
            $error = $oObjeto->getError();
            echo $error['error_description'];
        }
        break;

    case 3:
        if ($oObjeto->InsertarObservacion($datos, $codigoInsertado)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Comentario enviado correctamente a las " . date("H") . ":" . date("i") . "Hs";
            FuncionesPHPLocal::ArmarLinkMD5('lic_licencias_am.php', ['Id' => $codigoInsertado], $get, $md5);
            $msg['header'] = "lic_licencias_am.php?Id=" . $codigoInsertado . '' . $md5;
        } else {
            $error = $oObjeto->getError();
            echo $error['error_description'];
        }
        break;

    case 4:
        if ($oObjeto->cambiarEstado($datos)) {
            //VERIFICO SI INSERTO LA LICENCIA A LIQUIDAR
            $oMovimiento = new Movimientos($conexion);
            if (isset($datos["bajaLiquidacion"]) && $datos["bajaLiquidacion"] == 1) {
                $datos["IdLicencia"] = $datos["Id"];
                if (!$oMovimiento->insertarMovimientoLicencia($datos, $IdLogNovedad)) {
                    $errormsg = $oMovimiento->getError();
                    echo utf8_decode($errormsg['error_description']);
                    break;
                }
            }elseif (isset($datos["anularLiquidacion"]) && $datos["anularLiquidacion"] == 1) {
                    $datos["IdLicencia"] = $datos["Id"];
                    if (!$oMovimiento->anularMovimientoLicencia($datos)) {
                        $errormsg = $oMovimiento->getError();
                        echo utf8_decode($errormsg['error_description']);
                        break;
                    }
            }
            $msg['IsSucceed'] = true;
            $estado = 'enviada';
            switch ($datos['IdEstadoFinal']) {
                case 4:
                case 5:
                    $estado = 'aprobada';
                    break;
                case 7:
                    $estado = 'denegada';
                    break;
                case 8:
                    $estado = 'anulada';
                    break;
                case 9:
                    $estado = 'enviada a rectificacion';
                    break;
            }
            $msg['Msg'] = "Licencia $estado correctamente a las " . date('H:i \H\s');
            break;
        }
        echo utf8_decode($oObjeto->getError()['error_description']);
        break;
    case 5:
        $datos['IdEstado'] = 8;
        if ($oObjeto->cambiarEstado($datos)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Licencia anulada correctamente a las " . date('H:i \H\s');
            break;
        }
        echo $oObjeto->getError()['error_description'];
        break;

    case 6:
        $datos['IdEstado'] = 7;
        if ($oObjeto->cambiarEstado($datos)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Licencia denegada correctamente a las " . date('H:i \H\s');
            break;
        }
        echo $oObjeto->getError()['error_description'];
        break;

    case 7:
        if ($oObjeto->insertarJunta($datos, $codigoInsertado)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = "Junta/Visita insertada correctamente a las " . date('H:i \H\s');
            break;
        }
        echo $oObjeto->getError()['error_description'];
        break;

    case 8: // convalidar
        $datos['IdEstado'] = 5;
        if ($oObjeto->cambiarEstadoJunta($datos)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = 'Junta finalizada correctamente a las ' . date("H:i \H\s.");
        } else {
            $error = $oObjeto->getError();
            echo $error['error_description'];
        }
        break;
    case 9: // ausente
        $datos['IdEstado'] = 2;
        if ($oObjeto->cambiarEstadoJunta($datos)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = 'Ausencia guardada correctamente a las ' . date("H:i \H\s.");
        } else {
            $error = $oObjeto->getError();
            echo $error['error_description'];
        }
        break;
    case 10: //cancelar
        $datos['IdEstado'] = 4;
        if ($oObjeto->cambiarEstadoJunta($datos)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = 'Junta cancelada correctamente a las ' . date("H:i \H\s.");
        } else {
            $error = $oObjeto->getError();
            echo $error['error_description'];
        }
        break;
    case 11: # modificar reabierta
        if ($oObjeto->ModificarLicenciaReabierta($datos)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = 'Se ha modificado correctamente a las ' . date('H') . ':' . date('i') . 'Hs';
            $msg['header'] = $_SERVER['HTTP_REFERER'];
        } else {
            $error = $oObjeto->getError();
            echo $error['error_description'];
        }
        break;
    case 12:
        if ($oObjeto->Eliminar($datos)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = 'Se ha eliminado correctamente a las ' . date('H') . ':' . date('i') . 'Hs';
            FuncionesPHPLocal::ArmarLinkMD5('lic_licencias_am.php', ['Id' => $datos['Id']], $get, $md5);
            $msg['header'] = sprintf("/licencias/%s/%s", $datos['Id'], $md5);

        } else {
            $error = $oObjeto->getError();
            echo $error['error_description'];
        }
        break;
    case 13:
        $datos['IdEstado'] = 1;
        if ($oObjeto->modificarJunta($datos)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = utf8_encode('Informaci�n de junta guardada correctamente a las ') . date("H:i \H\s.");
        } else {
            $error = $oObjeto->getError();
            echo $error['error_description'];
        }
        break;
    case 14:
        #denegar
        $datos['IdEstado'] = 6;
        if ($oObjeto->cambiarEstadoJunta($datos)) {
            $msg['IsSucceed'] = true;
            $msg['Msg'] = utf8_encode('Informaci�n de junta guardada correctamente a las ') . date("H:i \H\s.");
        } else {
            $error = $oObjeto->getError();
            echo $error['error_description'];
        }
        break;
    case 15:
        #Cargado en SIGA y liquida mov pendientes
        if ($oObjeto->cambiarEstado($datos)) {
            $oMovimiento = new Movimientos($conexion);
            $datos['IdEstado'] = 3; //Liquidado
            $datos['IdLicencia'] = $datos['Id'];
            if (!$oMovimiento->ActualizarEstadoxIdLicencia($datos, $resultado, $numfilas)) {
                $errormsg = $oMovimiento->getError();
                echo $errormsg['error_description'];
                break;
            }

            $msg['Msg'] = 'Licencia enviada a Cargada en SIGA correctamente a las ' . date('H:i \H\s');
            $msg['IsSucceed'] = true;

        } else {
            $error = $oObjeto->getError();
            echo $error['error_description'];
        }
        break;
    default:
        $msg['Msg'] = "Error al procesar";
}

if ($msg['IsSucceed'])
    $conexion->ManejoTransacciones("C");
else {
    $msg['Msg'] = utf8_encode(ob_get_contents());
    $conexion->ManejoTransacciones("R");
}
ob_clean();
echo json_encode($msg);
ob_end_flush();
