<?php
require("./config/include.php");
require_once(DIR_CLASES_LOGICA.'cPersonasAntiguedadesTipos.class.php');

$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes($conexion,array("roles"=>"si","sistema"=>SISTEMA));
$conexion->SetearAdmiGeneral(ADMISITE);

$sesion = new Sesion($conexion,false);
// $sesion->TienePermisos($conexion,$_SESSION['usuariocod'],$_SESSION['rolcod'],$_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

$oEncabezados = new cEncabezados($conexion);
$oEncabezados->CargarPreload();
$oEncabezados->setTitle("PersonasAntiguedadesTipos");
$oEncabezados->addScript("modulos/per_personasantigtipos/js/per_personasantigtipos.js?v=1.0");
$oEncabezados->EncabezadoMenuEmergente($_SESSION['rolcod'],$_SESSION['usuariocod']);

?>
<div class="card">
<div class="card-body">

    <form action="per_personasantigtipos.php" method="post" name="formbusqueda" class="floating-labels mt-3" id="formbusqueda">
        <div class="search-filters">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <input name="Nombre" placeholder="Nombre" id="Nombre" class="form-control input-md" type="text" maxlength="255" value="<?php echo (isset($_SESSION['BusquedaAvanzada']['Nombre'])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION['BusquedaAvanzada']['Nombre'],ENT_QUOTES) : ''; ?>" />
                        <span class="bar"></span>
                        <label for="Nombre">Nombre:</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <input name="UltimaModificacionesFecha" placeholder="UltimaModificacionesFecha" id="UltimaModificacionesFecha" class="form-control input-md" type="date" maxlength="255" value="<?php echo (isset($_SESSION['BusquedaAvanzada']['UltimaModificacionesFecha'])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION['BusquedaAvanzada']['UltimaModificacionesFecha'],ENT_QUOTES) : ''; ?>" />
                        <span class="bar"></span>
                        <label for="UltimaModificacionesFecha">UltimaModificacionesFecha:</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <input name="SoloLiquidacion" placeholder="SoloLiquidacion" id="SoloLiquidacion" class="form-control input-md" type="number" maxlength="255" value="<?php echo (isset($_SESSION['BusquedaAvanzada']['SoloLiquidacion'])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION['BusquedaAvanzada']['SoloLiquidacion'],ENT_QUOTES) : ''; ?>" />
                        <span class="bar"></span>
                        <label for="SoloLiquidacion">SoloLiquidacion:</label>
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
                    <div class="float-right"><a class="btn btn-success" href="per_personasantigtipos_am.php"><i class="mdi mdi-plus-circle"></i>&nbsp;Crear nuevo</a>
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
<?php
$oEncabezados->PieMenuEmergente();
?>