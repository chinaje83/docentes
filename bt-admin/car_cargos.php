<?php
require("./config/include.php");
require_once(DIR_CLASES_LOGICA.'cCargos.class.php');

$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes($conexion,array("roles"=>"si","sistema"=>SISTEMA,"multimedia"=>"si"));
$conexion->SetearAdmiGeneral(ADMISITE);

$sesion = new Sesion($conexion,false);
$sesion->TienePermisos($conexion,$_SESSION['usuariocod'],$_SESSION['rolcod'],$_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

$oEncabezados = new cEncabezados($conexion);
$oEncabezados->CargarPreload();
$oEncabezados->setTitle("Cargos");
$oEncabezados->addScript("/modulos/car_cargos/js/car_cargos.js?v=1.3");
$oEncabezados->EncabezadoMenuEmergente($_SESSION['rolcod'],$_SESSION['usuariocod']);

$oObjeto = new cCargos($conexion);

if(!$oObjeto->RegimenTiposResult($result_RegimenTipos,$numfilas_RegimenTipos))
    return false;

if(!$oObjeto->CargosTiposSPResult($result_CargosTipos,$numfilas_CargosTipos))
	return false;



// busco escalafones
$oEscalafones = new cEscalafones($conexion);
if(!$oEscalafones->BusquedaAvanzada(["Estado" => [ACTIVO]],$resultadoEscalafones,$numfilasEscalafones))
    return false;


// busco lugares de desempeno
if (!$oObjeto->ComboDesempenosLugar([], $resultadoDesempenoLugar, $numfilasDesempenoLugar))
    return false;

?>
<div class="card">
<div class="card-body">

	<form action="car_cargos.php" method="post" name="formbusqueda" class="floating-labels mt-3" id="formbusqueda">
		<div class="search-filters">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group clearfix">
						<input name="IdCargo" placeholder="IdCargo" id="IdCargo" class="form-control input-md " type="text"  maxlength="11" size="60" value="<?php echo (isset($_SESSION["BusquedaAvanzada"]["IdCargo"])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION["BusquedaAvanzada"]["IdCargo"],ENT_QUOTES) : ""; ?>" />
						<span class="bar"></span>
						<label for='IdCargo'>Id:</label>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group clearfix">
						<select class="form-control input-md" name="IdTipoCargo" id="IdTipoCargo">
							<option value="">Todos...</option>
							<?php while($filaCombo = $conexion->ObtenerSiguienteRegistro($result_CargosTipos)){?>
								<option <?php if (isset($_SESSION["BusquedaAvanzada"]["IdTipoCargo"]) && $filaCombo["IdTipoCargo"]==$_SESSION["BusquedaAvanzada"]["IdTipoCargo"]) echo "selected" ?> value="<?php echo $filaCombo["IdTipoCargo"]?>" ><?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaCombo["Nombre"],ENT_QUOTES);?></option>
							<?php }?>
						</select>
						<span class="bar"></span>
						<label for='IdTipoCargo'>Tipo:</label>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group clearfix">
						<input name="Codigo" placeholder="Codigo" id="Codigo" class="form-control input-md " type="text"  maxlength="255" size="60" value="<?php echo (isset($_SESSION["BusquedaAvanzada"]["Codigo"])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION["BusquedaAvanzada"]["Codigo"],ENT_QUOTES) : ""; ?>" />
						<span class="bar"></span>
						<label for='Codigo'>C&oacute;digo:</label>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group clearfix">
						<input name="Descripcion" placeholder="Descripcion" id="Descripcion" class="form-control input-md " type="text"  maxlength="255" size="60" value="<?php echo (isset($_SESSION["BusquedaAvanzada"]["Descripcion"])) ? FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION["BusquedaAvanzada"]["Descripcion"],ENT_QUOTES) : ""; ?>" />
						<span class="bar"></span>
						<label for='Descripcion'>Descripci&oacute;n:</label>
					</div>
				</div>
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <select class="form-control input-md" name="Jerarquico" id="Jerarquico">
                            <option value="">Seleccione</option>
                            <option value="1" <?php echo (isset($_SESSION["BusquedaAvanzada"]["Jerarquico"]) && $_SESSION["BusquedaAvanzada"]["Jerarquico"] != "" && $_SESSION["BusquedaAvanzada"]["Jerarquico"] == 1? "selected='selected'" : ""); ?> >S&iacute;</option>
                            <option value="0" <?php echo (isset($_SESSION["BusquedaAvanzada"]["Jerarquico"]) && $_SESSION["BusquedaAvanzada"]["Jerarquico"] != "" && $_SESSION["BusquedaAvanzada"]["Jerarquico"] == 0? "selected='selected'" : ""); ?> >No</option>
                        </select>
                        <span class="bar"></span>
                        <label for='Jerarquico'>Cargo Jer&aacute;rquico:</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <select class="form-control input-md" name="IdRegimenSalarial" id="IdRegimenSalarial">
                            <option value="">Seleccione un regimen</option>
                            <?php while($filaRegimen = $conexion->ObtenerSiguienteRegistro($result_RegimenTipos)){?>
                                <option <?php if (isset($_SESSION["BusquedaAvanzada"]["IdRegimenSalarial"]) && $filaRegimen["Id"]==$_SESSION["BusquedaAvanzada"]["IdRegimenSalarial"]) echo "selected" ?> value="<?php echo $filaRegimen["Id"]?>" ><?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaRegimen["Nombre"],ENT_QUOTES);?></option>
                            <?php }?>
                        </select>
                        <label for="IdRegimenSalarial">Regimen Salarial</label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <select class="form-control input-md" name="DesempenoLugar" id="DesempenoLugar">
                            <option value="">Seleccione</option>
                            <?php
                            if($numfilasDesempenoLugar >0):
                                while($filaDesempenoLugar = $conexion->ObtenerSiguienteRegistro($resultadoDesempenoLugar)):
                                    ?>
                                    <option value="<?php echo $filaDesempenoLugar["Id"]; ?>"
                                       <?php if (isset($_SESSION["BusquedaAvanzada"]["DesempenoLugar"]) && $filaDesempenoLugar["Id"]==$_SESSION["BusquedaAvanzada"]["DesempenoLugar"]) echo "selected" ?>
                                    >
                                        <?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaDesempenoLugar["Nombre"], ENT_QUOTES); ?>
                                    </option>
                                <?php endwhile;
                            endif; ?>
                        </select>
                        <label for="DesempenoLugar">Lugar de desempeño</label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <select class="form-control input-md" name="IdEscalafon" id="IdEscalafon">
                            <option value="">Seleccione</option>
                            <?php
                            if($numfilasEscalafones>0):
                                while($filaEscalafon = $conexion->ObtenerSiguienteRegistro($resultadoEscalafones)):
                                    ?>
                                    <option value="<?php echo $filaEscalafon["IdEscalafon"]; ?>"
                                        <?php if (isset($_SESSION["BusquedaAvanzada"]["IdEscalafon"]) && $filaEscalafon["IdEscalafon"]==$_SESSION["BusquedaAvanzada"]["IdEscalafon"]) echo "selected" ?>
                                    >
                                        <?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaEscalafon["Nombre"] . " - " . $filaEscalafon["Descripcion"], ENT_QUOTES); ?>
                                    </option>
                                <?php endwhile;
                            endif;?>
                        </select>
                        <label for="IdEscalafon">Escalaf&oacute;n</label>
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
				<div class="float-right"><a class="btn btn-success" href="car_cargos_am.php"><i class="mdi mdi-plus-circle"></i>&nbsp;Crear nuevo</a>
				<a class="btn btn-success" href="car_cargos_csv.php"><i class="far fa-file-excel"></i>&nbsp;Exportar CSV</a>
			</div>
		</div>
			</div>
	</form>
</div>
<div class="mt-2">&nbsp;</div>
<div id="LstDatos" style="width:100%;">
	<table id="listarDatos"></table>
	<div id="pager2"></div>
</div>
<div id="Popup"></div>
</div>
<?php
$oEncabezados->PieMenuEmergente();

?>
