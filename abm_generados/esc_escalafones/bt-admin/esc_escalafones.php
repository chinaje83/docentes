<?php
require("./config/include.php");
require_once(DIR_CLASES_LOGICA.'cEscalafones.class.php');

$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes($conexion,array("roles"=>"si","sistema"=>SISTEMA));
$conexion->SetearAdmiGeneral(ADMISITE);

$sesion = new Sesion($conexion,false);
$sesion->TienePermisos($conexion,$_SESSION['usuariocod'],$_SESSION['rolcod'],$_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

$oEncabezados = new cEncabezados($conexion);
$oEncabezados->CargarPreload();
$oEncabezados->setTitle("Escalafones");
$oEncabezados->addScript("/modulos/esc_escalafones/js/esc_escalafones.js?v=1.0");
$oEncabezados->EncabezadoMenuEmergente($_SESSION['rolcod'],$_SESSION['usuariocod']);

?>
<div class="card">
<div class="card-body">

    <form action="esc_escalafones.php" method="post" name="formbusqueda" class="floating-labels mt-3" id="formbusqueda">
        <div class="search-filters">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <input name="IdEscalafonExterno" placeholder="Id Externo" id="IdEscalafonExterno" class="form-control input-md" type="number" maxlength="255" value="<?php echo (isset($_SESSION[\"BusquedaAvanzada\"][\"IdEscalafonExterno\"])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION[\"BusquedaAvanzada\"][\"IdEscalafonExterno\"],ENT_QUOTES) : \"\"; ?>" />
                        <span class="bar"></span>
                        <label for='IdEscalafonExterno'>Id Externo:</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <input name="Nombre" placeholder="Nombre del Campo" id="Nombre" class="form-control input-md" type="text" maxlength="255" value="<?php echo (isset($_SESSION[\"BusquedaAvanzada\"][\"Nombre\"])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION[\"BusquedaAvanzada\"][\"Nombre\"],ENT_QUOTES) : \"\"; ?>" />
                        <span class="bar"></span>
                        <label for='Nombre'>Nombre del Campo:</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <input name="Descripcion" placeholder="Descripcion del campo" id="Descripcion" class="form-control input-md" type="text" maxlength="255" value="<?php echo (isset($_SESSION[\"BusquedaAvanzada\"][\"Descripcion\"])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION[\"BusquedaAvanzada\"][\"Descripcion\"],ENT_QUOTES) : \"\"; ?>" />
                        <span class="bar"></span>
                        <label for='Descripcion'>Descripcion del campo:</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <input name="IdRegimenSalarial" placeholder="IdRegimenSalarial" id="IdRegimenSalarial" class="form-control input-md" type="number" maxlength="255" value="<?php echo (isset($_SESSION[\"BusquedaAvanzada\"][\"IdRegimenSalarial\"])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION[\"BusquedaAvanzada\"][\"IdRegimenSalarial\"],ENT_QUOTES) : \"\"; ?>" />
                        <span class="bar"></span>
                        <label for='IdRegimenSalarial'>IdRegimenSalarial:</label>
                    </div>
                </div>

                <input type="hidden" name="Estado" id="Estado" value="<?php echo ACTIVO.','.NOACTIVO ?>" />
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-8">
                    <a class="btn btn-primary" href="javascript:void(0)" id="btnBuscar">Buscar</a>
                    <a class="btn btn-secondary" href="javascript:void(0)" id="btnLimpiar">Limpiar</a>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-4">
                    <div class="float-right"><a class="btn btn-success" href="esc_escalafones_am.php"><i class="mdi mdi-plus-circle"></i>&nbsp;Crear nuevo</a>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="card-body" id="LstDatos">
    <table id="listarDatos"></table>
    <div id="pager2"></div>
</div>
</div>