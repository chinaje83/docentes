<?php  
abstract class cRolesdb
{
	
	// Constructor de la clase
	function __construct(){


    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	


//-----------------------------------------------------------------------------------------
//							 PRIVADAS	
//----------------------------------------------------------------------------------------- 

//----------------------------------------------------------------------------------------- 

//----------------------------------------------------------------------------------------- 

	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_roles_xIdRol";
		$sparam=array(
			'pIdRol'=> $datos['IdRol']
			);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener el rol. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	
	}
	
	
	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_roles_busqueda_avanzada";
		$sparam=array(
			'pxIdRol'=> $datos['xIdRol'],
			'pIdRol'=> $datos['IdRol'],
			'pxDescripcion'=> $datos['xDescripcion'],
			'pDescripcion'=> $datos['Descripcion'],
			'porderby'=> $datos['orderby'],
			'plimit'=> $datos['limit']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarxDescripcion($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Roles_xDescripcion";
		$sparam=array(
			'pDescripcion'=> $datos['Descripcion']
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	
	protected function BuscarRolesModificables($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_roles_amb_roles_xIdRol";
		$sparam=array(
			'pIdRol'=> $datos['IdRol'],
			'pxIdRolActualizado'=> $datos['xIdRolActualizado'],
			'pIdRolActualizado'=> $datos['IdRolActualizado'],
			'porderby'=> $datos['orderby'],
			'plimit'=> $datos['limit']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la busqueda de los roles que puedo modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


    protected function BuscarRoles(&$resultado, &$numfilas)
    {
        $spnombre="sel_Roles_combo_Descripcion";
        $sparam=array(
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener el rol. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }
	
	
//----------------------------------------------------------------------------------------- 
// Retorna una consulta con todos los roles asociados a un usuario

// Parámetros de Entrada:
//		IdUsuario: buscar roles a los que acceda este usuario

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	protected function RolesDeUnUsuario($IdUsuario,&$numfilas,&$resultado)
	{
		$this->RolesDeUnUsuarioSP($IdUsuario,$spnombre,$spparam);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$spparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error seleccionando roles de un usuario. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
			return false;
		}
		
		return true;
	}
	
	
	protected function RolesClientesAreasDeUnUsuario($IdUsuario,&$numfilas,&$resultado)
	{
		$spnombre="sel_roles_Clientes_Areas_xIdUsuario";
		$spparam=array("pIdUsuario"=>$IdUsuario);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$spparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"No se ha podido traer los roles sin asignar de un usuario.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
			return false;
		}
		
		return true;
	}
	
	
	protected function BuscarRolesClientesAreasDeUnUsuario($datos,&$numfilas,&$resultado)
	{
		$spnombre="sel_roles_Clientes_Areas_xIdUsuario_IdCliente_IdArea";
		$spparam=array(
		"pIdUsuario"=>$datos['IdUsuario'],
		"pIdCliente"=>$datos['IdCliente'],
		"pIdArea"=>$datos['IdArea']
		
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$spparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"No se ha podido traer los roles sin asignar de un usuario.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
			return false;
		}
		
		return true;
	}

//----------------------------------------------------------------------------------------- 
// Retorna el SP y los parametros para cargar los roles de un usuario

// Parámetros de Entrada:
//		IdUsuario:

// Retorna:
//		spnombre,spparam
//		la función retorna true o false si se pudo ejecutar con éxito o no
	
	protected function RolesDeUnUsuarioSP($IdUsuario,&$spnombre,&$spparam)
	{
		$spnombre="sel_roles_xIdUsuario";
		$spparam=array("pIdUsuario"=>$IdUsuario);
		
		return true;
	}



	protected function TraerRolesSinAsignar($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_roles_abm_roles_sin_asignar_xrolactualiza";
		$spparam=array("pIdRolActualiza"=>$datos['IdRolActualiza'],"prolesasignados"=>$datos['in_roles_asignados']);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$spparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"No se ha podido traer los roles sin asignar de un usuario.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
			return false;
		}
		
		return true;	
	}
	
	
	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_roles";
		$sparam=array(
			'pDescripcion'=> $datos['Descripcion'],
			'pTieneDistrito'=> $datos['TieneDistrito'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s")
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}


    protected function buscarCombo(&$resultado, &$numfilas) {

        $spnombre = 'sel_Roles_combo';
        $sparam = [


        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar roles por combo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }



	protected function Modificar($datos)
	{
		$spnombre="upd_roles_xIdRol";
		$sparam=array(
			'pDescripcion'=> $datos['Descripcion'],
			'pTieneDistrito'=> $datos['TieneDistrito'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pIdRol'=> $datos['IdRol']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

    protected function ModificarNombre($datos)
    {
        $spnombre="upd_roles_Descripcion_xIdRol";
        $sparam=array(
            'pDescripcion'=> $datos['Descripcion'],
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
            'pIdRol'=> $datos['IdRol']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }
	
	protected function Eliminar($datos)
	{
		$spnombre="del_roles_xIdRol";
		$sparam=array(
			'pIdRol'=> $datos['IdRol']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

}


?>