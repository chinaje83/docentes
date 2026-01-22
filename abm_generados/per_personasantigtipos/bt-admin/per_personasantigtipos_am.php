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
$oEncabezados->addScript("modulos/per_personasantigtipos/js/per_personasantigtipos_am.js?v=1.0");
$oEncabezados->EncabezadoMenuEmergente($_SESSION['rolcod'],$_SESSION['usuariocod']);

$oObjeto = new cPersonasAntiguedadesTipos($conexion);

$esmodif = false;
$btn = "BtnInsertar";
$IdAntiguedadTipo = "";$Nombre = "";
$Estado = "";
$AltaFecha = "";
$AltaUsuario = "";
$UltimaModificacionesFecha = "";
$UltimaModificacionUsuario = "";
$SoloLiquidacion = "";
if (isset($_GET['IdAntiguedadTipo']) && $_GET['IdAntiguedadTipo']!="")
{
    $esmodif = true;
    $datos = $_GET;
    if(!$oObjeto->BuscarxCodigo($datos,$resultado,$numfilas))
        return false;
    if($numfilas!=1){
        FuncionesPHPLocal::MostrarMensaje($conexion,MSG_ERRGRAVE,"Codigo inexistente.",["archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__],["formato"=>FMT_TEXTO]);
        return false;
    }
    $datosregistro = $conexion->ObtenerSiguienteRegistro($resultado);
    $btn = "BtnModificar";
    $IdAntiguedadTipo = $datosregistro["IdAntiguedadTipo"];    $Nombre = $datosregistro["Nombre"];
    $Estado = $datosregistro["Estado"];
    $AltaFecha = $datosregistro["AltaFecha"];
    $AltaUsuario = $datosregistro["AltaUsuario"];
    $UltimaModificacionesFecha = $datosregistro["UltimaModificacionesFecha"];
    $UltimaModificacionUsuario = $datosregistro["UltimaModificacionUsuario"];
    $SoloLiquidacion = $datosregistro["SoloLiquidacion"];
}
?>

<div class="card">
<div class="card-body">
    <div class="form row">
        <div class="col-md-12">
            <form class="form-material" action="per_personasantigtipos" method="post" name="formalta" id="formalta">
                <input type="hidden" name="IdAntiguedadTipo" id="IdAntiguedadTipo" value="<?php echo $IdAntiguedadTipo; ?>" />
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="Nombre">Nombre</label>
                            <input type="text" class="form-control input-md" name="Nombre" id="Nombre" value="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($Nombre ?? '',ENT_QUOTES) ?>" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="UltimaModificacionesFecha">UltimaModificacionesFecha</label>
                            <input type="date" class="form-control input-md" name="UltimaModificacionesFecha" id="UltimaModificacionesFecha" value="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($UltimaModificacionesFecha ?? '',ENT_QUOTES) ?>"  />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="SoloLiquidacion">SoloLiquidacion</label>
                            <input type="number" class="form-control input-md" name="SoloLiquidacion" id="SoloLiquidacion" value="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($SoloLiquidacion ?? '',ENT_QUOTES) ?>"  />
                        </div>
                    </div>

                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button class="btn btn-success" type="button" id="<?php echo $btn; ?>">Guardar</button>
                        <?php if ($esmodif) { ?>
                            <button class="btn btn-danger" type="button" id="BtnEliminar">Eliminar</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<?php
$oEncabezados->PieMenuEmergente();
?>