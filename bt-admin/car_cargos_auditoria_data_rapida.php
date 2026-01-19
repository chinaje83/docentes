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
$oObjeto = new cCargos($conexion);
if (!isset($_POST['IdCargo']) || !is_numeric($_POST['IdCargo']) || $_POST['IdCargo']=="")
	die();
$IdCargo = $_POST['IdCargo'];
$datos['IdCargo'] = $IdCargo;
if(!$oObjeto->BuscarAuditoriaRapida($datos,$resultado,$numfilas))
	die();
if ($numfilas!=1)
	die();
$datosRegistro = $conexion->ObtenerSiguienteRegistro($resultado);
$AltaFecha = FuncionesPHPLocal::ConvertirFecha($datosRegistro['AltaFecha'],"aaaa-mm-dd","dd/mm/aaaa")." ".substr($datosRegistro['AltaFecha'],11,5)."Hs";
$AltaUsuarioNombreCompleto = FuncionesPHPLocal::HtmlspecialcharsSistema($datosRegistro['AltaUsuarioNombreCompleto'],ENT_QUOTES);
$UltimaModificacionFecha = FuncionesPHPLocal::ConvertirFecha($datosRegistro['UltimaModificacionFecha'],"aaaa-mm-dd","dd/mm/aaaa")." ".substr($datosRegistro['UltimaModificacionFecha'],11,5)."Hs";
$UltimaModificacionUsuarioNombreCompleto = FuncionesPHPLocal::HtmlspecialcharsSistema($datosRegistro['UltimaModificacionUsuarioNombreCompleto'],ENT_QUOTES);
?>
<div class="PopupAuditoria">
	<div class="row">
	<div class="col-md-6">
		<div class="form-group">
				<label for="UsuarioAlta"><i class="fa fa-user">&nbsp;</i>Usuario que di&oacute; de alta</label>
				<div><?php echo  $AltaUsuarioNombreCompleto;?></div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
				<label for="FechaAlta"><i class="fa fa-calendar">&nbsp;</i>Fecha de alta</label>
				<div><?php echo  $AltaFecha;?></div>
			</div>
	</div>
	</div>
	<div class="row">
	<div class="col-md-6">
	<div class="form-group">
				<label for="UsuarioAlta"><i class="fa fa-user">&nbsp;</i>&Uacute;ltimo usuario que modific&oacute;</label>
				<div><?php echo $UltimaModificacionUsuarioNombreCompleto;?></div>
	</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
				<label for="FechaAlta"><i class="fa fa-calendar">&nbsp;</i>Fecha de &uacute;ltima modificaci&oacute;n</label>
				<div><?php echo $UltimaModificacionFecha;?></div>
		</div>
	</div>
	</div>
</div>

<?php 
?>