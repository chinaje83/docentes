<?php /**
 * @var accesoBDLocal $conexion
 * @var array         $datosRegistro
 * @var int           $IdMotivo
 * @var bool          $editable
 * @var bool          $tienePermisoModifArticulo
 *

 */

if (isset($_POST['loadAjax']) && $_POST['loadAjax']) {
    require('./config/include.php');
    require(DIR_LIBRERIAS . 'CurlBigtree.php');
    require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cServiciosLicencias.class.php');
    require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cArticulos.class.php');

    $conexion = new accesoBDLocal(SERVIDORBD, USUARIOBD, CLAVEBD);
    $conexion->SeleccionBD(BASEDATOS);
    FuncionesPHPLocal::CargarConstantes($conexion, array('roles' => 'si', 'sistema' => SISTEMA, 'multimedia' => 'si'));
    $conexion->SetearAdmiGeneral(ADMISITE);
    $sesion = new Sesion($conexion, false);
    $sesion->TienePermisos($conexion,$_SESSION['usuariocod'],$_SESSION['rolcod'],$_SERVER['PHP_SELF']);
    $oSistemaBloqueo = new SistemaBloqueo();
    $oSistemaBloqueo->VerificarBloqueo($conexion);
    $oObjeto = new cServiciosLicencias($conexion);

    $datos['Id'] = $_POST['id'];
    $datosRegistro = $oObjeto->ObtenerLicenciaxId($datos, true);
    if ($datosRegistro === false) {
        echo  '<tr><td colspan="10" class="table-danger">' . utf8_decode($oObjeto->getError('error_description')). '</td></tr>';
        die();
    }

    $IdMotivo = $datosRegistro['Motivo']['Id'];

    $tienePermisoEliminar = false;


    if (!$oObjeto->obtenerAcciones($datos, $acciones)) {
        echo  '<tr><td colspan="10" class="table-danger">' . utf8_decode($oObjeto->getError('error_description')). '</td></tr>';
        die();
    }

    $editable = isset($acciones['accionesABM']['000002']);

    $tienePermisoEliminar = false;
    if (isset($acciones['accionesABM']['000031']))
        $tienePermisoEliminar = true;

    $tienePermisoModifArticulo = false;
    if (isset($acciones['accionesABM']['000032']))
        $tienePermisoModifArticulo = true;

}



$oArticulos = new cArticulos($conexion);
$datosBuscar['IdMotivo'] = $IdMotivo;
if (!$oArticulos->BuscarCombo($datosBuscar, $articulos)) {
    echo  '<tr><td colspan="10" class="table-danger">' . utf8_decode($oArticulos->getError('error_description')). '</td></tr>';
    die();
}

$oCargosTipos = new cCargosTipos($conexion);


foreach ($datosRegistro['Cargos'] as $cargo) {

    $datosBuscarSolicitud['IdPuesto'] = $cargo['Puesto']['Id'];
    $class = 'outline-dark hide';
    $fa = 'fas fa-user-plus';
    $data_id = '';
    $class = 'success hide';
    $fa = 'fas fa-eye';
    if(isset($_SESSION['IdEscuela']) && is_array($cargo))
    {
        if(is_array($_SESSION['IdEscuela'])){
            if (!in_array($cargo['Escuela']['Id'],$_SESSION['IdEscuela']))
                continue;
        } else {
            if ($cargo['Escuela']['Id'] != $_SESSION['IdEscuela'])
                continue;
         }
    }
    else
    {
        if (!FuncionesPHPLocal::isEmpty($_SESSION['IdEscuela']) && $_SESSION['IdEscuela'] != $cargo['Escuela']['Id'])
            continue;
    }

    if (isset($cargo['Puesto']['Estado']) && $cargo['Puesto']['Estado'] <> ACTIVO) { ?>

        <tr class="fila_puesto_<?= $cargo['Puesto']['Id']; ?>">
            <td>
                <input type="hidden" name="IdPuestoAfectado[]" id="IdPuesto_<?= $cargo['Puesto']['Id']; ?>" value="<?= $cargo['Puesto']['Id']; ?>">
                <input type="hidden" name="IdPuestoAfectadoEstado[]" id="IdPuestoEstado_<?= $cargo['Puesto']['Id']; ?>" value="<?= ELIMINADO; ?>">
                <div id="ArticulosPuestos_<?= $cargo['Puesto']['Id']; ?>">
                    <input type="hidden" id="IdArticuloPuesto_<?= $cargo['Puesto']['Id']; ?>" value="0" name="IdArticuloPuesto[<?= $cargo['Puesto']['Id']; ?>]">
                </div>
            </td>
        </tr>

        <?php
        continue;
    }


    $oObjetoAcciones = new cRolesModulosAcciones($conexion);

    $accionesModulo = $oObjetoAcciones->BuscarAccionesxRol(['IdRol' => reset($_SESSION['rolcod'])])[AC_ID_MODULO_LICENCIAS] ?? [];
    ?>
    <tr class="fila_puesto_<?= $cargo['Puesto']['Id']; ?>">
        <td>
            <?php /*
            if ((array_key_exists(ROL_ADMIN,$_SESSION['rolcod']) || array_key_exists(ROL_EQUIPO_CONDUCCION,$_SESSION['rolcod']) || array_key_exists(ROL_MESA_AYUDA,$_SESSION['rolcod']))){?>
            <a href="/establecimientos/<?php echo $cargo['Escuela']['Id'] ?>/pofa/puesto/<?php echo $cargo['Puesto']['Id']; ?>" target="_blank">
                <?php echo $cargo['Puesto']['IdPofa'] ?>
            </a>
            <?php }else{ */?>
            <?php echo $cargo['Puesto']['IdPofa'] ?>
            <?php /* }*/
            // $cargo['Puesto']['IdPofa'];
            ?>
        </td>
        <td>
            <?php
            if (
                (isset($_SESSION['IdEscuela']) && $cargo['Escuela']['Id'] == $_SESSION['IdEscuela'])
                || in_array(AC_009974, $accionesModulo)
            ) {
                echo FuncionesPHPLocal::EnlacePuestoPofa($cargo['Escuela']['Id'], $cargo['Puesto']["IdPuestoRaiz"], $cargo['Puesto']["IdPuestoRaiz"]);
            } else {
                echo $cargo['Puesto']["IdPuestoRaiz"];
            }
            ?>
        </td>
        <td>
            <strong>Escuela c&oacute;d. <?= $cargo['Escuela']['Codigo']; ?>&ensp;</strong> <?= $cargo['Escuela']['Nivel']; ?>
            <br> <?php
            echo $cargo['Puesto']['Cupof']; ?>
            <?= (isset($cargo['Escuela']['NombreTurnoCorto']) && $cargo['Escuela']['NombreTurnoCorto'] <> '' ? '<br>Turno &ensp; '.$cargo['Escuela']['NombreTurnoCorto'].' ' : '') ?>
                <?= (isset($cargo['Puesto']['AnioNombreCorto']) &&
                isset($cargo['Puesto']['Seccion']) ? $cargo['Puesto']['AnioNombreCorto'] . ' ' . $cargo['Puesto']['Seccion'] : ''); ?>
            <br> Revista <?= $cargo['Puesto']['Revista']['Codigo']; ?>
            &ensp;|&ensp;<?= $cargo['Puesto']['Nombre']; ?>
        </td>
        <td><?= $cargo['Puesto']['Catedra']['Cantidad'] . ' ' . $cargo['Puesto']['Catedra']['Unidad']['NombreCorto'] ?? ''; ?></td>
        <td><?= number_format($cargo['HorasAfectadas'], 2, ',', '.'); ?></td>
        <td>
            <select name="IdArticulo" id="IdArticulo_<?= $cargo['Puesto']['Id']; ?>" data-id="<?= $cargo['Puesto']['Id']; ?>"
                    class="form-control form-control-sm input-md articulos" <?php echo ($tienePermisoModifArticulo) ? '':'readonly' ?> style="<?php echo($tienePermisoModifArticulo)?'':'pointer-events: none;'?>">
                <option value="">Seleccione</option>
                <?php
                $filas = [];
                $filas = $articulos['filasDocente'];
                /*
                if (!FuncionesPHPLocal::isEmpty($cargo['Puesto']['IdTipoCargo']) && in_array($cargo['Puesto']['IdTipoCargo'] ,CARGO_TIPO_AUXILIAR))
                    $filas = $articulos['filasAuxiliar'];
                */
                if (!FuncionesPHPLocal::isEmpty($cargo['Puesto']['IdTipoCargo'])){
                    if(!$oCargosTipos->BuscarxCodigo(['IdTipoCargo' => $cargo['Puesto']['IdTipoCargo']],$resultadoCargTipo,$numfilasCargTipo)){
                        die();
                    }

                    if ($numfilasCargTipo == 1){
                        $filaCargTipo = $conexion->ObtenerSiguienteRegistro($resultadoCargTipo);
                        if ($filaCargTipo["IdCargoCategoria"] == 2) { // cuando es categoria auxiliar
                            $filas = $articulos['filasAuxiliar'];
                        }
                    }
                }

                $numfilasArticulos = count($filas);
                if ($numfilasArticulos > 0) {
                    foreach ($filas as $r) {
                        $selected = '';
                        if (!FuncionesPHPLocal::isEmpty($cargo['Articulo']['Id']) && $cargo['Articulo']['Id'] == $r['IdArticulo'] || $numfilasArticulos == 1)
                            $selected = 'selected'; ?>
                        <option value="<?= $r['IdArticulo']; ?>" <?= $selected; ?> ><?= $r['Codigo'].' - '.$r['Descripcion']; ?></option>
                    <?php }
                } else if (isset($cargo['Articulo']) && !empty($cargo['Articulo'])) { ?>
                    <option value="<?= $cargo['Articulo']['Id']; ?>" selected><?= $cargo['Articulo']['Descripcion'].' - '.$cargo['Articulo']['Codigo'] ?>
                <?php } ?>
            </select>
        </td>
        <td>
            <a href="javascript:void(0);" class="btn btn-<?= $class; ?> btnSolicitud" data-escuela="<?= $cargo['Escuela']['Id']; ?>"
               data-puesto="<?= $cargo['Puesto']['Id']; ?>" data-id="<?= $data_id; ?>"><i class="<?= $fa; ?>" aria-hidden="true"></i></a>

            <?php if($tienePermisoEliminar) { ?>
                <a href="javascript:void (0);" class="btn btn-sm btn-outline-danger btnEliminarCargoAfectado" data-puesto="<?= $cargo['Puesto']['Id']; ?>">
                    <i class="far fa-trash-alt" aria-hidden="true"></i>
                </a>
            <?php }  ?>

            <input type="hidden" name="IdPuestoAfectado[]" id="IdPuesto_<?= $cargo['Puesto']['Id']; ?>" value="<?= $cargo['Puesto']['Id']; ?>">
            <input type="hidden" name="IdPuestoAfectadoEstado[]" id="IdPuestoEstado_<?= $cargo['Puesto']['Id']; ?>" value="<?= ACTIVO; ?>">
            <div id="ArticulosPuestos_<?=$cargo['Puesto']['Id']?>">
                <?php
                //FuncionesPHPLocal::print_pre($cargo['Articulo'], false);
                if ($numfilasArticulos > 0) {
                    if (!FuncionesPHPLocal::isEmpty($cargo['Articulo']['Id'])) { #no tiene articulo asignado?>
                        <input type="hidden" id="IdArticuloPuesto_<?= $cargo['Puesto']['Id']; ?>" value="<?= $cargo['Articulo']['Id']; ?>"
                               name="IdArticuloPuesto[<?= $cargo['Puesto']['Id']; ?>]">
                <?php } else if ($numfilasArticulos == 1) { #tiene y hay un solo articulo disponible
                        foreach ($filas as $r) { #armo unhidden por puesto?>
                            <input type="hidden" id="IdArticuloPuesto_<?= $cargo['Puesto']['Id']; ?>" value="<?= $r['IdArticulo']; ?>"
                                   name="IdArticuloPuesto[<?= $cargo['Puesto']['Id']; ?>]">
                        <?php }
                    }
                } ?>
            </div>
        </td>
    </tr>
    <tr class="no_border fila_puesto_<?= $cargo['Puesto']['Id']; ?>">
        <td colspan="3">
            <div><strong>Cargo seleccionado por: </strong><?=  $cargo['Alta']['Usuario']['Nombre']; ?></div>
        </td>
    </tr>
<?php }
