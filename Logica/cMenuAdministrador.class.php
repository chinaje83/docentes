<?php
//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------
// Clase con la lógica para el manejo de modulos

class cMenuAdministrador
{
	protected $conexion;
	protected $formato;


	// Constructor de la clase
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
    }

	// Destructor de la clase
	function __destruct() {
    }





//-----------------------------------------------------------------------------------------
//							 PUBLICAS
//-----------------------------------------------------------------------------------------


	public function PublicarMenu($datos)
	{
		$carpeta = PATH_STORAGE."menu";
		if(!file_exists($carpeta))
			@mkdir($carpeta);

		// se piden todos los gruposmod que tengan 'S' o 'L'
		// en modulomostrar
		$oRoles = new cRoles($this->conexion);
		if(!$oRoles->BusquedaAvanzada($datos,$res,$num))
			return false;
		while($filaRoles = $this->conexion->ObtenerSiguienteRegistro($res))
		{
			$roles = $filaRoles['IdRol'];
			$file = "$carpeta/rol_$roles.json";
			$param=array('pIdRol'=> $roles);
			if(!$this->conexion->ejecutarStoredProcedure("sel_menusuperior_xrol",$param,$menuprinc,$numfilas,$errno))
			{
				continue;
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no existen grupos modulos para el rol.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}

			$arrayMenu = array();

			while ($MenuItem = $this->conexion->ObtenerSiguienteRegistro($menuprinc))
			{
				$arrayMenu['IdGrupoMod_'.$MenuItem['IdGrupoMod']] = $MenuItem;
				$arrayMenu['IdGrupoMod_'.$MenuItem['IdGrupoMod']]['Subitems'] = array();

				$param=array('pIdRol'=> $roles,'pIdGrupoMod'=>$MenuItem['IdGrupoMod']);
				if(!$this->conexion->ejecutarStoredProcedure("sel_menuizq_xrolcod_xgrupocod",$param,$menusecund,$numfilas,$errno))
				{
					continue;
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no existen modulos para el rol.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
				}
				$arraySubmenu = array();
				while($SubMenuItem = $this->conexion->ObtenerSiguienteRegistro($menusecund))
					$arraySubmenu['modulocod_'.$SubMenuItem['modulocod']] = $SubMenuItem;

				$arrayMenu['IdGrupoMod_'.$MenuItem['IdGrupoMod']]['Subitems'] = $arraySubmenu;
			}
			$menu = FuncionesPHPLocal::ConvertiraUtf8($arrayMenu);
			$str = json_encode($menu);
			if(file_exists($file))
				unlink($file);

			file_put_contents($file,$str);
		}

		return true;
	}

}
