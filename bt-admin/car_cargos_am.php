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
$oEncabezados->addScript("/js/tiny_mce/tiny_mce.min.js");
$oEncabezados->addScript("/modulos/car_cargos/js/car_cargos_am.js?v=1.2");
$oEncabezados->EncabezadoMenuEmergente($_SESSION['rolcod'],$_SESSION['usuariocod']);

$oObjeto = new cCargos($conexion);

$esmodif = false;
$botonejecuta = "BtAlta";
$boton = "Alta";
$btn = "BtnInsertar";
$IdCargo = "";
$IdTipoCargo = "";
$Codigo = "";
$IdExterno = "";
$AdmiteSuplente = "";
$Descripcion = "";
$Esdeno = "0";
$EquivalenciaHs = "0.00";
$Jerarquico = "";
$Estado = "";
$AltaFecha = "";
$AltaUsuario = "";
$UltimaModificacionUsuario = "";
$UltimaModificacionFecha = "";
$PermiteSimultaneo =0;
$SCParcial = 0;
$IdRegimenSalarial = "";
$IdJornada = "";
$IdEscalafon = $DesempenoLugar = $IdTipo = "";
if (isset($_GET['IdCargo']) && $_GET['IdCargo']!="")
{
	$esmodif = true;
	$datos = $_GET;
	if(!$oObjeto->BuscarxCodigo($datos,$resultado,$numfilas))
		return false;
	if($numfilas!=1){
		FuncionesPHPLocal::MostrarMensaje($conexion,MSG_ERRGRAVE,"Codigo inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
		return false;
	}
	$datosregistro = $conexion->ObtenerSiguienteRegistro($resultado);
	$btn = "BtnModificar";
	$IdCargo = $datosregistro["IdCargo"];
	$IdTipoCargo = $datosregistro["IdTipoCargo"];
	$Codigo = $datosregistro["Codigo"];
    $IdExterno = $datosregistro["IdExterno"];
    $AdmiteSuplente = $datosregistro["AdmiteSuplente"];
	$Descripcion = $datosregistro["Descripcion"];
	$Esdeno = $datosregistro["Esdeno"];
	$EquivalenciaHs = $datosregistro["EquivalenciaHs"];
    $Jerarquico = $datosregistro["Jerarquico"];
    $PermiteSimultaneo = $datosregistro["PermiteSimultaneo"];
    $SCParcial = $datosregistro["SCParcial"];
    $IdRegimenSalarial = $datosregistro["IdRegimenSalarial"];
    $IdJornada = $datosregistro["IdJornada"];
    $IdEscalafon = $datosregistro["IdEscalafon"];
    $DesempenoLugar =$datosregistro["DesempenoLugar"];
    $IdTipo = $datosregistro["IdTipo"];
	$Estado = $datosregistro["Estado"];
	$AltaFecha = FuncionesPHPLocal::ConvertirFecha($datosregistro["AltaFecha"],'aaaa-mm-dd','dd/mm/aaaa');
	$AltaUsuario = $datosregistro["AltaUsuario"];
	$UltimaModificacionUsuario = $datosregistro["UltimaModificacionUsuario"];
	$UltimaModificacionFecha = FuncionesPHPLocal::ConvertirFecha($datosregistro["UltimaModificacionFecha"],'aaaa-mm-dd','dd/mm/aaaa');

}
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


// busco Tipos de cargosHorarios
$oCargoTipoH = new cCargosTiposHorarios($conexion);
if(!$oCargoTipoH->BusquedaAvanzada(["Estado" => [ACTIVO]],$resultadoCargosH,$numfilasCargosH))
    return false;

?>


<div class="card">
<div class="card-body">
	<div class="form row">
		<div class="col-md-12 col-xs-12 col-sm-12">
            <?php if ($esmodif)
				{
					include('popup_auditoria.php');
				}?>
			<div class="clearboth aire_vertical">&nbsp;</div>
		</div>

		<div class="col-md-12">
			<form  class="form-material" action="car_cargos" method="post" name="formalta" id="formalta" >

                <div class="row">


                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="Codigo">Codigo</label>
                            <input type="text" class="form-control input-md " maxlength="255" name="Codigo" id="Codigo" value="<?php  echo FuncionesPHPLocal::HtmlspecialcharsSistema($Codigo,ENT_QUOTES) ?>" />
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="Descripcion">Descripcion</label>
                            <input type="text" class="form-control input-md " maxlength="255" name="Descripcion" id="Descripcion" value="<?php  echo FuncionesPHPLocal::HtmlspecialcharsSistema($Descripcion,ENT_QUOTES) ?>" />
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="IdTipoCargo">Agrupamiento en la <?php echo PLANTA_ANALITICA_ALIAS ?></label>
                            <select class="form-control input-md" name="IdTipoCargo" id="IdTipoCargo">
                                <option value="">Seleccione</option>
                                <?php while($filaCombo = $conexion->ObtenerSiguienteRegistro($result_CargosTipos)){?>
                                    <option <?php if ($filaCombo["IdTipoCargo"]==$IdTipoCargo) echo "selected='selected'"?> value="<?php echo $filaCombo["IdTipoCargo"]?>"><?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaCombo["Nombre"],ENT_QUOTES);?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                </div>



                <div class="row">



                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="DesempenoLugar">Lugar de desempeño</label>
                            <select class="form-control input-md" name="DesempenoLugar" id="DesempenoLugar">
                                <option value="">Seleccione</option>
                                <?php
                                if($numfilasDesempenoLugar >0):
                                    while($filaDesempenoLugar = $conexion->ObtenerSiguienteRegistro($resultadoDesempenoLugar)):
                                        ?>
                                        <option value="<?php echo $filaDesempenoLugar["Id"]; ?>" <?php if ($DesempenoLugar == $filaDesempenoLugar["Id"]) echo "selected" ?>>
                                            <?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaDesempenoLugar["Nombre"], ENT_QUOTES); ?>
                                        </option>
                                    <?php endwhile;
                                endif; ?>
                            </select>
                        </div>
                    </div>

                    <?php if (CARGOS_SELECCIONA_JORNADA):
                        $cJornadas = new cJornadas($conexion);
                        if (!$cJornadas->BuscarListado([], $resultadoJornadas, $numfilasJornadas))
                            return false;
                        ?>
                            <div class="col-md-4">
                                <div class="form-group clearfix">
                                    <label for="IdJornada">Jornada</label>
                                    <select class="form-control input-md" name="IdJornada" id="IdJornada">
                                        <option value="">Seleccione una jornada</option>
                                        <?php
                                        if ($numfilasJornadas>0):
                                            while ($filaJornada = $conexion->ObtenerSiguienteRegistro($resultadoJornadas)): ?>
                                                <option <?php if ($filaJornada["IdJornada"]==$IdJornada) echo "selected='selected'"?> value="<?php echo $filaJornada["IdJornada"]?>">
                                                    <?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaJornada["Descripcion"],ENT_QUOTES);?>
                                                </option>
                                            <?php endwhile;
                                        endif; ?>
                                    </select>
                                </div>
                            </div>
                    <?php endif; ?>
                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="Jerarquico">Cargo Jer&aacute;rquico (cargos sin aula)</label>
                            <select class="form-control input-md" name="Jerarquico" id="Jerarquico">
                                <option value="">Seleccione</option>
                                <option value="1" <?php echo ($Jerarquico != "" && $Jerarquico == 1? "selected='selected'" : ""); ?> >S&iacute;</option>
                                <option value="0" <?php echo ($Jerarquico != "" && $Jerarquico == 0? "selected='selected'" : ""); ?> >No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="IdRegimenSalarial">Regimen Salarial</label>
                            <select class="form-control input-md" name="IdRegimenSalarial" id="IdRegimenSalarial">
                                <option value="">Seleccione un regimen</option>
                                <?php while($filaRegimen = $conexion->ObtenerSiguienteRegistro($result_RegimenTipos)){?>
                                    <option <?php if ($filaRegimen["Id"]==$IdRegimenSalarial) echo "selected='selected'"?> value="<?php echo $filaRegimen["Id"]?>"><?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaRegimen["Nombre"],ENT_QUOTES);?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>



                    <?php
                    $elijeEscalafon = false;
                    if(!empty($IdTipo) && $IdTipo == 3)
                        $elijeEscalafon = true;
                    ?>
                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="IdEscalafon">Escalaf&oacute;n <small>(solo tipo cargo)</small></label>
                            <select class="form-control input-md" name="IdEscalafon" id="IdEscalafon" <?php echo $elijeEscalafon ? "" : "disabled" ?>>
                                <option value="">Seleccione</option>
                                <?php
                                if($numfilasEscalafones>0):
                                    while($filaEscalafon = $conexion->ObtenerSiguienteRegistro($resultadoEscalafones)):
                                        ?>
                                        <option value="<?php echo $filaEscalafon["IdEscalafon"]; ?>" <?php if ($IdEscalafon == $filaEscalafon["IdEscalafon"]) echo "selected" ?>>
                                            <?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaEscalafon["Nombre"] . " - " . $filaEscalafon["Descripcion"], ENT_QUOTES); ?>
                                        </option>
                                    <?php endwhile;
                                endif;?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="IdTipo">Tipo</label>
                            <select class="form-control input-md" name="IdTipo" id="IdTipo">
                                <option value="">Seleccione</option>
                                <?php while($filaTipoH = $conexion->ObtenerSiguienteRegistro($resultadoCargosH)){?>
                                    <option <?php if ($filaTipoH["IdTipo"]==$IdTipo) echo "selected='selected'"?> value="<?php echo $filaTipoH["IdTipo"]?>"><?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaTipoH["Nombre"],ENT_QUOTES);?></option>
                                <?php }?>
                                <!--
                                <option value="1" <?php if ($IdTipo == "1") echo "selected" ?>><?= NOMBRE_HORAS; ?></option>
                                <option value="2" <?php if ($IdTipo == "2") echo "selected" ?>><?= utf8_decode(NOMBRE_MODULOS); ?></option>
                                <option value="3" <?php if ($IdTipo == "3") echo "selected" ?>>Cargo</option>
                                -->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group clearfix">
                            <label for="AdmiteSuplente">Admite suplente</label>
                            <select class="form-control input-md" name="AdmiteSuplente" id="AdmiteSuplente">
                                <option value="">Seleccione</option>
                                <option value="1" <?php echo ($AdmiteSuplente != "" && $AdmiteSuplente == 1? "selected='selected'" : ""); ?> >S&iacute;</option>
                                <option value="0" <?php echo ($AdmiteSuplente != "" && $AdmiteSuplente == 0? "selected='selected'" : ""); ?> >No</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group clearfix">
                            <label for="Esdeno">Esdeno</label>
                            <select class="form-control input-md" name="Esdeno" id="Esdeno" >
                                <option value="1" <?php   if ($Esdeno==1) echo "selected" ?> >Si</option>
                                <option value="0" <?php   if ($Esdeno==0) echo "selected" ?> >No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group clearfix">
                            <label for="EquivalenciaHs">Carga Horaria</label>
                            <input type="number" step=".01" class="form-control input-md " maxlength="255" name="EquivalenciaHs" id="EquivalenciaHs" value="<?php  echo FuncionesPHPLocal::HtmlspecialcharsSistema($EquivalenciaHs,ENT_QUOTES) ?>" />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group clearfix">
                            <label for="IdExterno">IdExterno</label>
                            <input type="text" class="form-control input-md " maxlength="255" name="IdExterno" id="IdExterno" value="<?php  echo FuncionesPHPLocal::HtmlspecialcharsSistema($IdExterno,ENT_QUOTES) ?>" autocomplete="off"/>
                        </div>
                    </div>

                </div>

                <div class="form-group clearfix">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="font-weight-bold" for="PermiteSimultaneo">Simult&aacute;neo</label>
                            <br><small>Permite superponer desempe&ntilde;os sobre otros cargos y/o materias que tengan la misma propiedad</small>
                            <div class="row mt-3">
                                <div class="col-md-2">
                                    <input type="radio" id="PermiteSimultaneo_NO" name="PermiteSimultaneo" value="0" <?php echo($PermiteSimultaneo==0)?"checked":"" ?> >
                                    <label for="PermiteSimultaneo_NO">No</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="radio" id="PermiteSimultaneo_SI" name="PermiteSimultaneo" value="1" <?php echo($PermiteSimultaneo==1)?"checked":"" ?>>
                                    <label for="PermiteSimultaneo_SI">Si</label>
                                </div>
                                <div class="col-md-4">&nbsp;</div>
                            </div>
                        </div>

                        <div class='col-md-4'>
                            <label class='font-weight-bold' for='SCParcial'>Permite solicitud de cobertura parcial</label>
                            <div class='row'>
                                <div class='col-md-2'>
                                    <input type='radio' id='SCParcial_NO' name='SCParcial'
                                           value='0' <?php echo ($SCParcial == 0) ? 'checked' : '' ?> >
                                    <label for="SCParcial_NO">No</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="radio" id="SCParcial_SI" name="SCParcial"
                                           value="1" <?php echo ($SCParcial == 1) ? 'checked' : '' ?>>
                                    <label for="SCParcial_SI">Si</label>
                                </div>
                                <div class="col-md-4">&nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>

				<input type="hidden" name="IdCargo" id="IdCargo" value="<?php  echo $IdCargo ?>" />

				<div class="menuAcciones">
					<div class="container">
							<a class="btn btn-success" href="javascript:void(0)" id="<?php   echo $btn ?>" ><i class="fa fa-check"></i>&nbsp;Guardar</a>
                        <?php if($esmodif){?>
							<a class="btn btn-danger" href="javascript:void(0)" id="BtnEliminar" >Eliminar</a>
                        <?php }?>
					<div class="msgaccionupd">&nbsp;</div>
					<div class="menubarra float-right">
                        <?php if($esmodif){?>
							<a class="btn btn-info" href="car_cargos_auditoria.php?IdCargo=<?php  echo $IdCargo ?>"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Ver registros de auditoria</a>
                        <?php }?>
							<a class="btn btn-secondary" href="car_cargos.php">Volver</a>
					</div>
					</div>
				</div>
				<div class="clearboth">&nbsp;</div>
			</form>
		</div>
		<div class="col-md-4 col-xs-12 col-sm-6">

		</div>
		<div class="clearboth">&nbsp;</div>
	</div>
	 <div class="clearboth">&nbsp;</div>
</div>
</div>


<?php
$oEncabezados->PieMenuEmergente();

?>
