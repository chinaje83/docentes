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
header('Content-Type: text/html; charset=iso-8859-1');
$oObjeto = new cAuditoriasCargos($conexion);
if (!isset($_POST['IdFilaLog']) || !is_numeric($_POST['IdFilaLog']) || $_POST['IdFilaLog']=="")
	die();
$IdFilaLog = $_POST['IdFilaLog'];
$datos['IdFilaLog'] = $IdFilaLog;
if(!$oObjeto->BuscarxCodigo($datos,$resultado,$numfilas))
	die();
if ($numfilas!=1)
	die();
$datosRegistro = $conexion->ObtenerSiguienteRegistro($resultado);
$Fecha = FuncionesPHPLocal::ConvertirFecha($datosRegistro['UltimaModificacionFecha'],"aaaa-mm-dd","dd/mm/aaaa")." ".substr($datosRegistro['UltimaModificacionFecha'],11,5)."Hs";
?>
<div class="PopupAuditoria">
	<div>
		<div class="form-group">
			<div>
				<label><?php   echo htmlentities("Codigo",ENT_QUOTES)?></label>
				<div class="clearboth"></div>
				<?php   echo FuncionesPHPLocal::HtmlspecialcharsSistema($datosRegistro['Codigo'],ENT_QUOTES)?>
			</div>
			<div>
				<label><?php   echo htmlentities("Descripcion",ENT_QUOTES)?></label>
				<div class="clearboth"></div>
				<?php   echo FuncionesPHPLocal::HtmlspecialcharsSistema($datosRegistro['Descripcion'],ENT_QUOTES)?>
			</div>
			<div>
				<label><?php   echo htmlentities("Esdeno",ENT_QUOTES)?></label>
				<div class="clearboth"></div>
				<?php   echo FuncionesPHPLocal::HtmlspecialcharsSistema($datosRegistro['Esdeno'],ENT_QUOTES)?>
			</div>
			<div>
				<label><?php   echo htmlentities("EquivalenciaHs",ENT_QUOTES)?></label>
				<div class="clearboth"></div>
				<?php   echo FuncionesPHPLocal::HtmlspecialcharsSistema($datosRegistro['EquivalenciaHs'],ENT_QUOTES)?>
			</div>
		</div>
	</div>
	<div class='row'>
		<div class="col-md-6">
			<div class="form-group">
				<label for="Usuario"><i class="fa fa-user">&nbsp;</i>&nbsp;Usuario</label>
				<div><?php   echo FuncionesPHPLocal::HtmlspecialcharsSistema($datosRegistro['NombreCompleto'],ENT_QUOTES)?></div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="Fecha"><i class="fa fa-calendar">&nbsp;</i>&nbsp;Fecha</label>
				<div><?php  echo FuncionesPHPLocal::HtmlspecialcharsSistema($Fecha,ENT_QUOTES)?></div>
				</div>
			</div>
	</div>
</div>

<?php 
?>