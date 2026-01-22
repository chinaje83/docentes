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
$oEncabezados->addScript("/modulos/esc_escalafones/js/esc_escalafones_am.js?v=1.0");
$oEncabezados->EncabezadoMenuEmergente($_SESSION['rolcod'],$_SESSION['usuariocod']);

$oObjeto = new cEscalafones($conexion);

$esmodif = false;
$btn = "BtnInsertar";
$IdEscalafon = "";$IdEscalafonExterno = "";
$Nombre = "";
$Descripcion = "";
$IdRegimenSalarial = "";
$Estado = "";
$AltaFecha = "";
$AltaUsuario = "";
$UltimaModificacionUsuario = "";
$UltimaModificacionFecha = "";
if (isset($_GET['IdEscalafon']) && $_GET['IdEscalafon']!="")
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
    $IdEscalafon = $datosregistro["IdEscalafon"];    $IdEscalafonExterno = $datosregistro["IdEscalafonExterno"];
    $Nombre = $datosregistro["Nombre"];
    $Descripcion = $datosregistro["Descripcion"];
    $IdRegimenSalarial = $datosregistro["IdRegimenSalarial"];
    $Estado = $datosregistro["Estado"];
    $AltaFecha = $datosregistro["AltaFecha"];
    $AltaUsuario = $datosregistro["AltaUsuario"];
    $UltimaModificacionUsuario = $datosregistro["UltimaModificacionUsuario"];
    $UltimaModificacionFecha = $datosregistro["UltimaModificacionFecha"];
}
?>

<div class="card">
<div class="card-body">
    <div class="form row">
        <div class="col-md-12">
            <form class="form-material" action="esc_escalafones" method="post" name="formalta" id="formalta">
                <input type="hidden" name="IdEscalafon" id="IdEscalafon" value="<?php echo $IdEscalafon; ?>" />
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="IdEscalafonExterno">Id Externo</label>
                            <input type="number" class="form-control input-md" name="IdEscalafonExterno" id="IdEscalafonExterno" value="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($IdEscalafonExterno ?? '',ENT_QUOTES) ?>" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="Nombre">Nombre del Campo</label>
                            <input type="text" class="form-control input-md" name="Nombre" id="Nombre" value="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($Nombre ?? '',ENT_QUOTES) ?>" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="Descripcion">Descripcion del campo</label>
                            <input type="text" class="form-control input-md" name="Descripcion" id="Descripcion" value="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($Descripcion ?? '',ENT_QUOTES) ?>"  />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="IdRegimenSalarial">IdRegimenSalarial</label>
                            <input type="number" class="form-control input-md" name="IdRegimenSalarial" id="IdRegimenSalarial" value="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($IdRegimenSalarial ?? '',ENT_QUOTES) ?>"  />
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