<?php
require("./config/include.php");
require_once(DIR_CLASES_AUDITORIAS_LOGICA.'cAuditoriasCargos.class.php');

$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes($conexion,array("roles"=>"si","sistema"=>SISTEMA,"multimedia"=>"si"));
$sesion = new Sesion($conexion,false);
$sesion->TienePermisos($conexion,$_SESSION['usuariocod'],$_SESSION['rolcod'],$_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

$oEncabezados = new cEncabezados($conexion);
$oEncabezados->CargarPreload();
$oEncabezados->setTitle("Registro de Auditoria / Cargos");
$oEncabezados->addScript("/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.es.js?v=1.1");
$oEncabezados->addScript("/modulos/car_cargos/js/car_cargos_auditorias.js?v=1.1");
$oEncabezados->EncabezadoMenuEmergente($_SESSION['rolcod'],$_SESSION['usuariocod']);

if (!isset($_GET['IdCargo']) || !is_numeric($_GET['IdCargo']) || $_GET['IdCargo']=="")
{
	 header("Location:car_cargos.php");
	 die();

}

$IdCargo = $datos['IdCargo'] = $_GET['IdCargo'];
$oObjeto = new cCargos($conexion);

if(!$oObjeto->BuscarxCodigo($datos,$resultado,$numfilas))
	return false;
if ($numfilas==0)
{
	 FuncionesPHPLocal::MostrarMensaje($conexion,MSG_ERRSOSP,"Error, debe ingresar una penalidad sube valida.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
	 die();
}
?>
<div class="card">
    <div class="card-body">
        <form action="car_cargos_auditorias.php" method="post" name="formbusqueda" class="floating-labels mt-3" id="formbusqueda">
            <div class="row">
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group clearfix">
                        <input name="UltimaModificacionFechaDesde" placeholder="dd/mm/aaaa" id="UltimaModificacionFechaDesde" class="form-control input-md " type="text" maxlength="10"  value="" />
                        <span class="bar"></span>
                        <label for="UltimaModificacionFechaDesde">Fecha Modificaci&oacute;n Desde:</label>
                        <span class="help-block"><small>dd/mm/aaaa</small></span>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group clearfix">
                        <input name="UltimaModificacionFechaHasta" placeholder="dd/mm/aaaa" id="UltimaModificacionFechaHasta" class="form-control input-md" type="text" maxlength="10"  value="" />
                        <span class="bar"></span>
                        <label for="UltimaModificacionFechaHasta">Fecha Modificaci&oacute;n Hasta:</label>
                        <span class="help-block"><small>dd/mm/aaaa</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <a class="btn btn-primary" href="javascript:void(0)"  onclick="doSearch(arguments[0]||event)" >Buscar</a>
                    <a class="btn btn-secondary" href="car_cargos_am.php?IdCargo=<?php echo $IdCargo?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i>&nbsp;Volver</a>
                </div>
                <div class="col-md-6">
                    <div class=" float-right">
                        <a class="btn btn-success" href="car_cargos_auditoria_csv.php"><i class="far fa-file-excel" aria-hidden="true"></i>&nbsp;Exportar Resultados</a>
                    </div>
                </div>
            </div>

            <input type="hidden" name="IdCargo" id="IdCargo" value="<?php   echo $IdCargo?>" />
        </form>




        <div class="mt-2">&nbsp;</div>

        <div id="LstDatos" style="width:100%;">
               <table id="listarDatos"></table>
            <div id="pager2"></div>
        </div>
        <div id="Popup"></div>
    </div>

</div>
<div id="ModalData" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Registro de Auditor&iacute;a</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        </div>
      <div class="modal-body">
        <p id="DataAuditoria">

        </p>
        <div class="clearboth"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><?php
$oEncabezados->PieMenuEmergente();

?>
