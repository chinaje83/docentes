<?php 

/**
 * Class de Usuarios Permisos
 * Instancia de usuarios Permisos.
 *
 * El constructor la clase de usuarios maneja la persistencia a la base de datos
 *
 * @category  Usuarios
 * @example   usuarios.php
 * @example <br />
 * 	$oUsuariosPermisos = new cUsuariosPermisos($conexion);<br />
 *  $oUsuariosPermisos->CargarPermisos();<br />
 * @version   0.01
 * @since     2017-08-02
 * @author    Alejandro Precioso <aprecioso@gmail.com>
 */
 
include(DIR_CLASES_DB."cUsuariosPermisos.db.php");

class cUsuariosPermisos extends cUsuariosPermisosdb
{
	/**
	 * Conexion a la base de datos.
	 * @var objeto conexion
	 */
	protected $conexion;
	/**
	 * Formato de errores. Formato en que se muestran los errores.
	 * @var string
	 */
 	protected $formato;

	public function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
		$_SESSION['permisos'] = array();
	}

	public function __destruct(){parent::__destruct();}


	static function TienePermiso($IdAccion){if (array_key_exists($IdAccion,$_SESSION['permisos'])) return true; return false;}

	static function ObtenerPermisos(){return $_SESSION['permisos'];}

	public function CargarPermisos()
	{
		$datos['IdRol'] = implode(",",$_SESSION['rolcod']);
		if (!parent::BuscarPermisosxUsuario($datos,$resultado,$numfilas))
			return false;
		
		if ($numfilas>0)
			$this->SetearPermisos($resultado);	
		
		return true;
	}

	private function SetearPermisos($resultado)
	{
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			$_SESSION['permisos'][str_pad($fila['IdAccion'],6,"0",STR_PAD_LEFT)] = $fila['Descripcion'];
		}
	}


}
?>