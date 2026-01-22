<?php
include(DIR_CLASES_DB."cRoles.db.php");

class cRoles extends cRolesdb
{
	protected $conexion;
	protected $formato;


	// Constructor de la clase
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		parent::__construct();
    }

	// Destructor de la clase
	function __destruct() {
		parent::__destruct();
    }



//-----------------------------------------------------------------------------------------
//							 PUBLICAS
//-----------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------


	public function RolAdministrador($IdRol)
	{
		if (in_array(ADMISITE,$IdRol))
			return true;

		return false;
	}

	public function RolesSP(&$spnombre,&$spparam)
	{
		$spnombre="sel_roles_orden";
		$spparam=array("porderby"=>"IdRol");

		return true;
	}

    public function buscarCombo(&$resultado, &$numfilas) {
        return parent::buscarCombo($resultado, $numfilas);
    }

//------------------------------------------------------------------------------------------
// Retorna en un arreglo con los datos de un categoria de la página

// Parámetros de Entrada:
//		catcod: catetoria a buscar

// Retorna:
//		resultado= Arreglo con todos los datos de un categoria.
//		numfilas= cantidad de filas
//		la función retorna true o false si se pudo ejecutar con éxito o no
	public function BuscarRolesModificables($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$sparam=array(
			'IdRol'=> $roles,
			'xDescripcion'=> 0,
			'IdRolActualizado'=> "",
			'xIdRolActualizado'=> 0,
			'limit'=> '',
			'orderby'=> "IdRol DESC"
		);
		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['IdRolActualizado']) && $datos['IdRolActualizado']!="")
		{
			$sparam['IdRolActualizado']= $datos['IdRolActualizado'];
			$sparam['xIdRolActualizado']= 1;
		}
		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BuscarRolesModificables($sparam,$resultado,$numfilas))
			return false;

		return true;
	}

//------------------------------------------------------------------------------------------
// Retorna en un arreglo con los datos de un categoria de la página

// Parámetros de Entrada:
//		catcod: catetoria a buscar

// Retorna:
//		resultado= Arreglo con todos los datos de un categoria.
//		numfilas= cantidad de filas
//		la función retorna true o false si se pudo ejecutar con éxito o no
	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		return true;
	}

	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdRol'=> 0,
			'IdRol'=> "",
			'xDescripcion'=> 0,
			'Descripcion'=> "",
			'limit'=> '',
			'orderby'=> "IdRol DESC"
		);

		if (isset ($datos['IdRol']) && $datos['IdRol']!="")
		{
			$sparam['IdRol']= $datos['IdRol'];
			$sparam['xIdRol']= 1;
		}

		if (isset ($datos['Descripcion']) && $datos['Descripcion']!="")
		{
			$sparam['Descripcion']= $datos['Descripcion'];
			$sparam['xDescripcion'] = 1;
		}

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarxDescripcion($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxDescripcion($datos,$resultado,$numfilas))
			return false;
		return true;
	}

    public function BuscarRoles(&$resultado, &$numfilas)
    {
        if (!parent::BuscarRoles($resultado,$numfilas))
            return false;
        return true;
    }





//-----------------------------------------------------------------------------------------
// Retorna los roles que puede asignar un usuario a otro, eliminando los que ya tiene asignados

// Parámetros de Entrada:
//		IdRolactualiza:   es el rol del usuario logueado que quiere dar de alta el usuario/rol.
//		usuarioactualizar: es el usuariocod del usuario al cual se le quiere asignar el rol.

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no.

	public function RolesPosiblesAsignar($IdRolactualiza,$usuarioaactualizar,&$numfilas,&$roles_sin_asignar)
	{

		$roles_sin_asignar=array();

		if(!$this->RolesDeUnUsuario($usuarioaactualizar,$numfilas_rolusuario,$resultado))
			return false;

		$roles=array();
		if($numfilas_rolusuario>0) {
			while($fila=$this->conexion->ObtenerSiguienteRegistro($resultado))
				$roles_asignados[]=$fila["IdRol"];
		} else
			$roles_asignados[]=0; // rol falso para que no de error el "in" del sql cuando no tiene roles asignados

		// genero un string para el "in" con los roles que ya tiene asignado el usuario
		$in_roles_asignados="(".implode(",",$roles_asignados).")";

		$datos['IdRolActualiza'] = implode(",",$IdRolactualiza);
		$datos['in_roles_asignados'] = $in_roles_asignados;

		if(!parent::TraerRolesSinAsignar($datos,$resultado_roles,$numfilas_resu))
			return false;

		$roles_sin_asignar=array();
		if($numfilas_resu>0) {
			while($fila2=$this->conexion->ObtenerSiguienteRegistroArray($resultado_roles))
			{
				$roles_sin_asignar[]=$fila2["IdRol"];
			}
		}

		$numfilas=count($roles_sin_asignar);

		return true;
	}


	public function RolesPosiblesAsignarxClientexArea($IdRolactualiza,$usuarioaactualizar,$IdCliente,$IdArea,&$numfilas,&$roles_sin_asignar)
	{

		$roles_sin_asignar= $roles_asignados= array();
		$datosbuscar['IdUsuario'] = $usuarioaactualizar;
		$datosbuscar['IdCliente'] = $IdCliente;
		$datosbuscar['IdArea'] = $IdArea;

		if(!$this->BuscarRolesClientesAreasDeUnUsuario($datosbuscar,$numfilas_rolusuario,$resultado))
			return false;

		$roles=array();
		if($numfilas_rolusuario>0)
		{
			while($fila=$this->conexion->ObtenerSiguienteRegistro($resultado))
					$roles_asignados[]=$fila["IdRol"];
		} else
			$roles_asignados[]=0; // rol falso para que no de error el "in" del sql cuando no tiene roles asignados

		// genero un string para el "in" con los roles que ya tiene asignado el usuario
		$in_roles_asignados="(".implode(",",$roles_asignados).")";
		$datos['IdRolActualiza'] = implode(",",$IdRolactualiza);
		$datos['in_roles_asignados'] = $in_roles_asignados;
		if(!parent::TraerRolesSinAsignar($datos,$resultado_roles,$numfilas_resu))
			return false;


		$roles_sin_asignar=array();
		if($numfilas_resu>0) {
			while($fila2=$this->conexion->ObtenerSiguienteRegistroArray($resultado_roles))
			{
				$roles_sin_asignar[]=$fila2["IdRol"];
			}
		}

		$numfilas=count($roles_sin_asignar);

		return true;
	}



	public function TraerRolesActualizar($datos,&$resultado,&$numfilas)
	{
		$oRolesAbmRoles=new cRolesAbmRoles($this->conexion);
		$roles = implode(",",$datos['rolcod']);
		$ArregloDatos['IdRolActualiza'] = $roles;
		if (!$oRolesAbmRoles->Buscar ($ArregloDatos,$numfilas,$resultado))
			return false;

		return true;
	}


	public function RolesDeUnUsuario($usuariocod,&$numfilas,&$resultado)
	{
		if (!parent::RolesDeUnUsuario($usuariocod,$numfilas,$resultado))
			return false;

		return true;
	}


	public function RolesClientesAreasDeUnUsuario($usuariocod,&$numfilas,&$resultado)
	{
		if (!parent::RolesClientesAreasDeUnUsuario($usuariocod,$numfilas,$resultado))
			return false;

		return true;
	}

	public function BuscarRolesClientesAreasDeUnUsuario($datos,&$numfilas,&$resultado)
	{
		if (!parent::BuscarRolesClientesAreasDeUnUsuario($datos,$numfilas,$resultado))
			return false;

		return true;
	}



	public function RolesDeUnUsuarioSP($usuariocod,&$spnombre,&$spparam)
	{
		if (!parent::RolesDeUnUsuarioSP($usuariocod,$spnombre,$spparam))
			return false;

		return true;
	}



//-----------------------------------------------------------------------------------------
// Alta de un rol

// Parámetros de Entrada:
//		datos: un array asociativo con los datos a cargar

// Retorna:
//		IdRol: es el codigo del nuevo rol
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		$datosMenu['IdRol'] = $datos['IdRol'] = $codigoinsertado;

		$oRolesAbmRoles = new cRolesAbmRoles($this->conexion,$this->formato);
		foreach ($_SESSION['rolcod'] as $rolcod)
		{
			$datosEnviar['IdRolActualiza'] = $rolcod;
			$datosEnviar['IdRolActualizado'] = $codigoinsertado;
			if(!$oRolesAbmRoles->Insertar($datosEnviar))
				return false;
		}

		$oMenu = new cMenuAdministrador($this->conexion,$this->formato);
		if(!$oMenu->PublicarMenu($datosMenu))
			return false;

       /* $oServicioRoles = new cServiciosRoles($this->conexion);

        $datos['Nombre'] = $datos['Descripcion'];
        $datos['Constante'] = $codigoinsertado;
		if (!$oServicioRoles->insertar($datos))
		    return false;*/

		return true;
	}

//-----------------------------------------------------------------------------------------
// Modificar un rol

// Parámetros de Entrada:
//		datos: un array asociativo con los datos nuevos
//		IdRol: es el codigo del rol a modificar

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos))
			return false;

		$this->_SetearNull($datos);
        if (!parent::Modificar($datos))
			return false;

		if (!$this->ActualizarRolesModulos($datos))
			return false;

        /*
        $oServicioRoles = new cServiciosRoles($this->conexion);

        $datosModificar['Nombre'] = $datos['Descripcion'];
        $datosModificar['Constante'] = $datos['IdRol'];
        if (!$oServicioRoles->modificar($datosModificar))
            return false;
        */
		return true;
	}

	public function ModificarNombre($datos): bool
    {
        return parent::ModificarNombre($datos);
    }



//-----------------------------------------------------------------------------------------
// Borra un rol

// Parámetros de Entrada:
//		IdRol: es el codigo del rol a borrar

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function Eliminar($datos)
	{

		$datos['rolcod'] = $datos['IdRol'];
		if (!$this->_ValidarEliminar($datos))
			return false;


		$oRolesModulos = new cRolesModulos($this->conexion,$this->formato);
		if (!$oRolesModulos->EliminarxRol($datos))
			return false;


		$oUsuariosRoles = new cUsuariosRoles($this->conexion,$this->formato);
		if (!$oUsuariosRoles->BajaUsuarioRolesxIdRol($datos))
			return false;

		if(!$this->EliminarAbmRoles($datos))
			return false;

		if (!parent::Eliminar($datos))
			return false;


        $oServicioRoles = new cServiciosRoles($this->conexion);

        $datos['Constante'] = $datos['IdRol'];
        if (!$oServicioRoles->eliminar($datos))
            return false;

		return true;
	}


//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		if(!$this->BuscarxDescripcion($datos,$resultadoDescripcion,$numfilasDescripcion))
			return false;

		if($numfilasDescripcion>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe otro rol con ese nombre.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	private function _ValidarModificar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un codigo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarDatosVacios($datos))
			return false;


		if(!$this->BuscarxDescripcion($datos,$resultadoDescripcion,$numfilasDescripcion))
			return false;

		if($numfilasDescripcion>0)
		{
			$fila =$this->conexion->ObtenerSiguienteRegistro($resultadoDescripcion);
			if($fila['IdRol']!=$datos['IdRol'])
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe otro Rol con ese nombre.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		return true;
	}



	private function _ValidarEliminar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un c&oacute;digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	private function _SetearNull(&$datos)
	{
		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";

        if (!isset($datos['Constante']) || $datos['Constante']=="")
            $datos['Constante']="NULL";

		if (isset($datos['TieneDistrito']) && $datos['TieneDistrito']=="1")
			$datos['TieneDistrito']="1";
		elseif(isset($datos['TieneDistrito']) && $datos['TieneDistrito']=="2")
			$datos['TieneDistrito']="2";
        elseif(isset($datos['TieneDistrito']) && $datos['TieneDistrito']=="3")
            $datos['TieneDistrito']="3";
        elseif(isset($datos['TieneDistrito']) && $datos['TieneDistrito']=="4")
            $datos['TieneDistrito']="4";
        elseif(isset($datos['TieneDistrito']) && $datos['TieneDistrito']=="5")
            $datos['TieneDistrito']="5";
		else
			$datos['TieneDistrito']="0";

		return true;
	}



	private function _ValidarDatosVacios($datos)
	{

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}




	private function EliminarAbmRoles($datos)
	{

		$oRolesAbmRoles = new cRolesAbmRoles($this->conexion,$this->formato);
		/*
		$datosBusqueda['IdRolActualiza'] = $datos['IdRol'];
		$oRolesAbmRoles->Buscar($datosBusqueda,$numfilas,$resultado);

		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			$datosEnviar['IdRolActualiza'] = $fila['IdRolActualiza'];
			$datosEnviar['IdRolActualizado'] = $fila['IdRolActualizado'];
			if(!$oRolesAbmRoles->Eliminar($datosEnviar))
				return false;
		}

		$datosBusqueda = array();
		$datosBusqueda['IdRolActualiza'] = implode(",",$_SESSION['rolcod']);
		$datosBusqueda['IdRolActualizado'] = $datos['IdRol'];
		$oRolesAbmRoles->Buscar($datosBusqueda,$numfilas,$resultado);

		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			$datosEnviar['IdRolActualiza'] = $fila['IdRolActualiza'];
			$datosEnviar['IdRolActualizado'] = $fila['IdRolActualizado'];
			if(!$oRolesAbmRoles->Eliminar($datosEnviar))
				return false;
		}*/
		if(!$oRolesAbmRoles->EliminarxIdRol($datos))
			return false;
		return true;
	}



	public function ActualizarRolesModulos($datos)
	{
		$datos['IdRolActualizado'] = $datos['IdRol'];
		if(!$this->BuscarRolesModificables($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Acción Ilegal.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$oRolesModulos = new cRolesModulos($this->conexion,$this->formato);
		if (!$oRolesModulos->EliminarxRol($datos))
			return false;

		if (!$oRolesModulos->InsertarModulosDefault($datos))
			return false;

		if (!$this->ObtenerIdsSeleccionados($datos,$arreglocodigos))
			return false;

		if(count($arreglocodigos)>0)
		{

			$datosinsertar['IdRol'] = $datos['IdRol'];
			foreach($arreglocodigos as $IdModulo)
			{
				//INSERTO ROLCOD, MODULOCOD
				$datosinsertar['IdModulo'] = $IdModulo;
				if(!$oRolesModulos->Insertar ($datosinsertar))
					return false;

			}
		}
		$oRolesModulosAcciones = new cRolesModulosAcciones($this->conexion,$this->formato);
		if(!$oRolesModulosAcciones->ActualizarAccionesxRol($datos,$arreglocodigos))
			return false;


		return true;
	}


	public function ObtenerIdsSeleccionados($datos,&$arrayfinal)
	{


		$arrayfinal=array();
		foreach ($datos as $nombre_var => $valor_var) {
			if (empty($valor_var)) {
				$vacio[$nombre_var] = $valor_var;
			} else {

				$post[$nombre_var] = $valor_var;
				$opcion = substr($nombre_var,0,10);
				if ($opcion=="modulocod_")
					$arrayfinal[] = $valor_var;
			}
		}


		return true;
	}



}
?>
