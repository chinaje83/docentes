<?php

use Bigtree\ExcepcionLogica;

require("./config/include.php");
require(DIR_LIBRERIAS . 'CurlBigtree.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cServiciosLicencias.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cAutorizantes.class.php');

$conexion = new accesoBDLocal(SERVIDORBD, USUARIOBD, CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);
FuncionesPHPLocal::CargarConstantes($conexion, array("roles" => "si", "sistema" => SISTEMA, "multimedia" => "si"));
$conexion->SetearAdmiGeneral(ADMISITE);
$sesion = new Sesion($conexion, false);
$sesion->TienePermisos($conexion, $_SESSION['usuariocod'], $_SESSION['rolcod'], $_SERVER['PHP_SELF']);
$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);
$oEncabezados = new cEncabezados($conexion);
$oEncabezados->CargarPreload();
$oEncabezados->setTitle("Revisi&oacute;n de licencias m&eacute;dicas");
$oEncabezados->addScript("/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.es.js?v=1.2");
$oEncabezados->addScript("/modulos/lic_licencias_administracion/js/lic_licencias_administracion.js?v=2.2");
$oEncabezados->addScript("/assets/lib/choosen/chosen.jquery.min.js");
$oEncabezados->addCss("/assets/lib/choosen/chosen.css?v=1");
$oEncabezados->addCss("/assets/css/choosen-materialize.css");


$oObjeto = new cServiciosLicencias($conexion);

$Motivos = $oObjeto->ObtenerDiagnosticos(1);
$Estados = $oObjeto->ObtenerEstados();


$arrayEstadosPublicos = $oObjeto->ObtenerEstadosPublicos();

$oObjeto = new cAutorizantes($conexion);

try {
	$tiposAutorizantes = $oObjeto->obtenerTiposAutorizantes();
} catch (ExcepcionLogica $e) {
	FuncionesPHPLocal::MostrarMensaje($conexion, MSG_ERRGRAVE, $e->getMessage(), ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => FMT_TEXTO]);
	$oEncabezados->PieMenuEmergente();
	die;
}
$oObjeto = new cEscuelas($conexion);
$oObjeto->getEscuelas($Escuelas);

if (!$oObjeto->RegionesSPResult($resultado_regiones,$numfilas_regiones))
	return false;

if (!$oObjeto->LocalidadesSPResult($resultado_localidades,$numfilas_localidades))
	return false;



$IdEscuela ="";
if(isset($_SESSION['IdEscuela']) && $_SESSION['IdEscuela']!="")
	$IdEscuela =$_SESSION['IdEscuela'];

$oEncabezados->EncabezadoMenuEmergente($_SESSION['rolcod'], $_SESSION['usuariocod']);
?>
    <ul class="nav nav-tabs customtab">
        <li class="nav-item">
            <a class="nav-link active show" id="tabObligatorios" data-toggle="tab" href="#obligatorios" role="tab" aria-controls="obligatorios" aria-selected="true" data-mostrar="0">Pendientes</a>
        </li>
        <li class='nav-item'>
            <a class='nav-link show' id='tabCertificados' data-toggle='tab' href='#certificados' role='tab' aria-controls='certificados' aria-selected='false' data-mostrar='0'>En espera de
                certificado</a>
        </li>
        <li class="nav-item">
            <a class="nav-link show" id="tabRectificados" data-toggle="tab" href="#rectificado" role="tab" aria-controls='rectificados' aria-selected="false" data-mostrar="1"> Rectificaci&oacute;n</a>
        </li>
        <li class="nav-item">
            <a class="nav-link show" id="tabApCondicional" data-toggle="tab" href="#condicional" role="tab" aria-controls='ApCondicional' aria-selected="false" data-mostrar="1">Aprobado condicional</a>
        </li>
        <li class="nav-item">
            <a class="nav-link show" id="tabOpcionales" data-toggle="tab" href="#opcionales" role="tab" aria-controls='opcionales' aria-selected="false" data-mostrar="1">Finalizadas</a>
        </li>
    </ul>
    <div class="card">
        <div class="card-body">
            <form action="lic_licencias.php" method="post" name="formbusqueda" class="floating-labels mt-3" id="formbusqueda">
                <div class="search-filters">
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group clearfix">
                                <input name="Id" placeholder="Id" id="Id" class="form-control input-md " type="text" maxlength="255" size="60"
                                       value="<?php echo (isset($_SESSION["BusquedaAvanzadaMedicas"]["Id"])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION["BusquedaAvanzadaMedicas"]["Id"], ENT_QUOTES) : ""; ?>"/>
                                <span class="bar"></span>
                                <label for='Id'>Id</label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group clearfix">
                                <input name="Dni" placeholder="Dni" id="Dni" class="form-control input-md " type="text" maxlength="255" size="60"
                                       value="<?php echo (isset($_SESSION["BusquedaAvanzadaMedicas"]["Dni"])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION["BusquedaAvanzadaMedicas"]["Dni"], ENT_QUOTES) : ""; ?>"/>
                                <span class="bar"></span>
                                <label for='Dni'>Documento</label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group clearfix">
                                <input name="Nombre" placeholder="Nombre" id="Nombre" class="form-control input-md " type="text" maxlength="255" size="60"
                                       value="<?php echo (isset($_SESSION["BusquedaAvanzadaMedicas"]["Nombre"])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION["BusquedaAvanzadaMedicas"]["Nombre"], ENT_QUOTES) : ""; ?>"/>
                                <span class="bar"></span>
                                <label for='Nombre'>Nombre</label>
                            </div>
                        </div>
						<?php if($IdEscuela==""){?>
                            <div class="col-md-3">
                                <div class="form-group clearfix">
                                    <select name="IdEscuela" id="IdEscuela" class="form-control input-md chzn-select">
                                        <option value="">Seleccione</option>
										<?php  if (count($Escuelas) > 0) {
											foreach ($Escuelas as $key=>$fila) { ?>
                                                <option value="<?php echo $fila['IdEscuela']; ?>" <?php if (isset($_SESSION['BusquedaAvanzadaNovedad']['IdEscuela']) &&  $_SESSION['BusquedaAvanzadaNovedad']['IdEscuela'] ==$fila['IdEscuela']) echo 'selected="selected"' ?>>
													<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($fila['CodigoEscuela']." - ".$fila['Nombre'],ENT_QUOTES)?>
                                                </option>
											<?php }
										} ?>
                                    </select>
                                    <span class="bar"></span>
                                    <label for="IdEscuela">Escuela</label>
                                </div>
                            </div>
						<?php }else{?>
                            <input type="hidden" name="IdEscuela" id="IdEscuela" value="<?php echo $IdEscuela?>">
						<?php } ?>
                    </div>
                    <div class="row mt-2">
                        <div class="col-3">
                            <div class="form-group clearfix">
                                <input name="Inicio" id="Inicio" placeholder="dd/mm/aaaa" class="form-control input-md" type="text" maxlength="10"
                                       value="<?php echo (isset($_SESSION["BusquedaAvanzadaMedicas"]["Inicio"])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION["BusquedaAvanzadaMedicas"]["Inicio"], ENT_QUOTES) : ""; ?>"/>
                                <span class="bar"></span>
                                <label for="Inicio">Inicio</label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group clearfix">
                                <input name="Fin" id="Fin" placeholder="dd/mm/aaaa" class="form-control input-md" type="text" maxlength="10"
                                       value="<?php echo (isset($_SESSION["BusquedaAvanzadaMedicas"]["Fin"])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION["BusquedaAvanzadaMedicas"]["Fin"], ENT_QUOTES) : ""; ?>"/>
                                <span class="bar"></span>
                                <label for="Fin">Fin</label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group clearfix">
                                <select name="IdMotivo" id="IdMotivo" class="form-control input-md chzn-select">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($Motivos['filas'] as $r) { ?>
                                        <option value="<?php echo $r['Id']; ?>" <?php echo(isset($_SESSION["BusquedaAvanzadaMedicas"]["IdMotivo"]) && $r['Id'] == $_SESSION["BusquedaAvanzadaMedicas"]["IdMotivo"] ? 'selected="selected"' : ''); ?>> <?php echo $r['Nombre'];
                                            echo($r['Descripcion'] != "" ? ' - ' . $r['Descripcion'] : ''); ?> </option>
                                    <?php } ?>
                                </select>
                                <span class="bar"></span>
                                <label for="IdMotivo">Diagn&oacute;stico</label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group clearfix">
                                <select name='IdTipoAutorizante' id='IdTipoAutorizante' class='form-control input-md chzn-select'>
                                    <option value=''>Seleccione un tipo</option>
									<?php foreach ($tiposAutorizantes as $r) { ?>
                                        <option value="<?php echo $r['Id']; ?>" <?php echo(isset($_SESSION["BusquedaAvanzadaMedicas"]["IdTipoAutorizante"]) && $r['Id'] == $_SESSION["BusquedaAvanzadaMedicas"]["IdTipoAutorizante"] ? 'selected="selected"' : ''); ?>> <?php echo $r['Nombre']; ?> </option>
									<?php } ?>
                                </select>
                                <span class="bar"></span>
                                <label for='IdTipoAutorizante'>Tratante</label>
                            </div>
                        </div>
                        <input type="hidden" name="Estado" id="Estado" value="<?php echo ACTIVO . ',' . NOACTIVO ?>"/>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group clearfix">
                                <select class="form-control input-md" name="IdRegion" id="IdRegion">
                                    <option value="">Seleccione</option>
                                    <?php while($fila = $conexion->ObtenerSiguienteRegistro($resultado_regiones)) {
                                        if (!empty($_SESSION['Regiones']) && !array_key_exists($fila['IdRegion'],$_SESSION['Regiones']))
                                            continue;
                                        ?>
                                        <option value="<?php echo $fila['IdRegion']; ?>" <?php echo (isset($_SESSION["BusquedaAvanzadaMedicas"]["IdRegion"]) && $_SESSION["BusquedaAvanzadaMedicas"]["IdRegion"] == $fila['IdRegion'] ? "selected='selected'" : ""); ?>>
                                            <?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Nombre'],ENT_QUOTES)?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="bar"></span>
                                <label for='IdRegion'>Regi&oacute;n</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group clearfix">
                                <select class="form-control input-md" name="IdLocalidad" id="IdLocalidad">
                                    <option value="">Seleccione</option>
                                    <?php while ($fila = $conexion->ObtenerSiguienteRegistro($resultado_localidades)) { ?>
                                        <option value="<?= $fila['IdLocalidad']; ?>" <?= (isset($_SESSION["BusquedaAvanzadaMedicas"]["IdLocalidad"]) && $_SESSION["BusquedaAvanzadaMedicas"]["IdLocalidad"] == $fila['IdLocalidad'] ? "selected='selected'" : ""); ?>>
                                            <?= FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Nombre'],ENT_QUOTES)?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="bar"></span>
                                <label for='IdLocalidad'>Localidad</label>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group clearfix">
                                <select name="IdEstado" id="IdEstado" class="form-control input-md chzn-select">
                                    <option value="">Seleccione</option>
                                    <?php
                                    $valorPredeterminado = null;

                                    if ($_SESSION['ConstanteRol'] =='ROL_MEDTRABAJO_MED') {
                                        $valorPredeterminado = 10;
                                    } elseif ($_SESSION['ConstanteRol'] == 'ROL_MEDTRABAJO_ADMIN') {
                                        $valorPredeterminado = 25;
                                    }
                                    foreach ($arrayEstadosPublicos['filas'] as $estado) {
                                        ?>
                                        <option value="<?php echo $estado['Id']; ?>" <?php echo(isset($_SESSION["BusquedaAvanzadaMedicas"]["Id"]) && $estado['Id'] == $_SESSION["BusquedaAvanzadaMedicas"]["Id"] ? 'selected="selected"' : ($valorPredeterminado !== null && $estado['Id'] == $valorPredeterminado ? 'selected="selected"' : '')); ?>>
                                            <?php echo $estado['NombrePublico'];?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="bar"></span>
                                <label for="IdEstado">Estado P&uacute;blico</label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group clearfix">
                                <select class="form-control input-md chzn-select" name="Semaforo" id="Semaforo">
                                    <option value="">Todos</option>
                                    <option value="ultimas_24_horas">Menos de 24 hs </option>
                                    <option value="entre_24_y_48_horas">Entre 24 y 48 hs</option>
                                    <option value="mas_de_48_horas">Mas de 48 hs</option>
                                </select>
                                <span class="bar"></span>
                                <label for="Semaforo">Fecha de envio</label>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group clearfix">
                                <select name="IdNivel" id="IdNivel" class="form-control input-md chzn-select">
                                    <option value="">Seleccione</option>
                                    <?php  $oNiveles = new cNiveles($conexion);
                                    if (!$oNiveles->buscarCombo($resultadoNiveles, $numfilasNiveles))
                                        return false;

                                    if ($numfilasNiveles > 0) {
                                        while ($filaNivel = $conexion->ObtenerSiguienteRegistro($resultadoNiveles)) {?>
                                            <option value="<?php echo $filaNivel['IdNivel']; ?>"> <?php echo $filaNivel['Nombre']; ?></option>
                                        <?php }
                                    } ?>
                                </select>
                                <span class="bar"></span>
                                <label for="IdNivel">Nivel</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group clearfix">
                                <select name="Reincorporado" id="Reincorporado" class="form-control input-md">
                                    <option value="">Seleccione</option>
                                    <option value="0">No</option>
                                    <option value="1">Si</option>
                                </select>
                                <span class="bar"></span>
                                <label for="Reincorporado">Reincorporado</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <a class="btn btn-outline-primary" href="javascript:void(0)" id="btnBuscar">Buscar</a>
                            <a class="btn btn-outline-secondary" href="javascript:void(0)" id="btnLimpiar">Limpiar</a>
                        </div>
                    </div>
            </form>
        </div>
        <div class="mt-2">&nbsp;</div>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="obligatorios" role="tabpanel" aria-labelledby="tabObligatorios" >
                <div id="LstDatos" style="width:100%;">
                    <table id="listarDatos"></table>
                    <div id="pager2"></div>
                </div>
            </div>
            <div class="tab-pane fade" id="opcionales" role="tabpanel" aria-labelledby='tabOpcionales'>
                <div id="LstDatos2" style="width:100%;">
                    <table id="listarDatos2"></table>
                    <div id="pager22"></div>
                </div>
            </div>
            <div class='tab-pane fade' id='certificados' role='tabpanel' aria-labelledby='tabCertificados'>
                <div id='LstDatos3' style='width:100%;'>
                    <table id='listarDatos3'></table>
                    <div id='pager23'></div>
                </div>
            </div>
            <div class='tab-pane fade' id='rectificado' role='tabpanel' aria-labelledby='tabRectificados'>
                <div id='LstDatos4' style='width:100%;'>
                    <table id='listarDatos4'></table>
                    <div id='pager24'></div>
                </div>
            </div>
            <div class='tab-pane fade' id='condicional' role='tabpanel' aria-labelledby='tabApCondicional'>
                <div id='LstDatos5' style='width:100%;'>
                    <table id='listarDatos5'></table>
                    <div id='pager25'></div>
                </div>
            </div>
        </div>
        <div id="Popup"></div>
    </div>
<?php
$oEncabezados->PieMenuEmergente();

?>
