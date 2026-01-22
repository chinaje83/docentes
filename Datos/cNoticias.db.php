<?php
abstract class cNoticiasDB
{
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;

	/**
	 * Constructor de la clase cNoticiasDB.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion,$formato){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
	}

	/**
	 * Destructor de la clase cNoticiasDB.
	 */
	function __destruct(){}

	/**
	 * Devuelve el mensaje de error almacenado
	 *
	 * @return array
	 */
	public abstract function getError(): array;

	/**
	 * Guarda un mensaje de error
	 *
	 * @param string|array  $error
	 * @param string        $error_description
	 */
	protected function setError($error,$error_description=''): void {
		$this->error = is_array($error) ? $error : ['error' => $error, 'error_description' => $error_description];
	}

	protected function RolesSP(?string &$spnombre, ?array &$sparam): void
	{
		$spnombre = 'sel_Roles_combo';
		$sparam = [];
	}
	public abstract function RolesSPResult( &$resultado, ?int &$numfilas): bool;

	protected function BuscarxCodigo(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_Noticias_xIdNoticia";
		$sparam=array(
			'pIdNoticia'=> $datos['IdNoticia']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	protected function BusquedaAvanzada(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_Noticias_busqueda_avanzada";
		$sparam=array(
			'pxIdNoticia'=> $datos['xIdNoticia'],
			'pIdNoticia'=> $datos['IdNoticia'],
			'pxTitulo'=> $datos['xTitulo'],
			'pTitulo'=> $datos['Titulo'],
			'pxCuerpo'=> $datos['xCuerpo'],
			'pCuerpo'=> $datos['Cuerpo'],
			'pxFechaDesde'=> $datos['xFechaDesde'],
			'pFechaDesde'=> $datos['FechaDesde'],
			'pxFechaHasta'=> $datos['xFechaHasta'],
			'pFechaHasta'=> $datos['FechaHasta'],
			'pxLink'=> $datos['xLink'],
			'pLink'=> $datos['Link'],
			'pxOrden'=> $datos['xOrden'],
			'pOrden'=> $datos['Orden'],
			'pxEstado'=> $datos['xEstado'],
			'pEstado'=> $datos['Estado'],
			'plimit'=> $datos['limit'],
			'porderby'=> $datos['orderby']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	protected function BuscarxIdRol($datos,&$resultado,&$numfilas): bool
    {
        $spnombre="sel_Noticias_xIdRol";
        $sparam=array(
            'pIdRol'=> $datos['IdRol']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar noticias por rol. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

	protected function BuscarAuditoriaRapida(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_Noticias_AuditoriaRapida";
		$sparam=array(
			'pIdNoticia'=> $datos['IdNoticia']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar auditoria. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	protected function BuscarCombo(&$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_Noticias_combo";
		$sparam=array();
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar combo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	protected function BuscarRolesNoticia(array $datos, &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_NoticiasRoles_xIdNoticia";
		$sparam=array(
			'pIdNoticia'=> $datos['IdNoticia']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar roles de noticia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}



    protected function Insertar(array $datos, ?int &$codigoInsertado): bool
	{
		$spnombre="ins_Noticias";
		$sparam=array(
			'pTitulo'=> $datos['Titulo'],
			'pCuerpo'=> $datos['Cuerpo'],
			'pFechaDesde'=> $datos['FechaDesde'],
			'pFechaHasta'=> $datos['FechaHasta'],
			'pLink'=> $datos['Link'],
			//'pOrden'=> $datos['Orden'],
			'pEstado'=> $datos['Estado'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$codigoInsertado=$this->conexion->UltimoCodigoInsertado();
		return true;
	}

	protected function Modificar(array $datos): bool
	{
		$spnombre="upd_Noticias_xIdNoticia";
		$sparam=array(
			'pTitulo'=> $datos['Titulo'],
			'pCuerpo'=> $datos['Cuerpo'],
			'pFechaDesde'=> $datos['FechaDesde'],
			'pFechaHasta'=> $datos['FechaHasta'],
			'pLink'=> $datos['Link'],
			//'pOrden'=> $datos['Orden'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
			'pIdNoticia'=> $datos['IdNoticia'],
            //'pEstado' => $datos['Estado']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	protected function Eliminar(array $datos): bool
	{
		$spnombre="del_Noticias_xIdNoticia";
		$sparam=array(
			'pIdNoticia'=> $datos['IdNoticia'],
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	protected function ModificarEstado(array $datos): bool
	{
		$spnombre="upd_Noticias_Estado_xIdNoticia";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pIdNoticia'=> $datos['IdNoticia'],
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	protected function InsertarNoticiaRol(array $datos): bool
	{
		$spnombre="ins_NoticiasRoles";
		$sparam=array(
			'pIdNoticia'=> $datos['IdNoticia'],
			'pIdRol'=> $datos['IdRol'],
			'pEstado'=> $datos['Estado'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar rol de noticia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	protected function EliminarRolesNoticia(array $datos): bool
	{
		$spnombre="del_NoticiasRoles_xIdNoticia";
		$sparam=array(
			'pIdNoticia'=> $datos['IdNoticia']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar roles de noticia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}
    protected function ActualizarArchivo(array $datos): bool
    {
        $spnombre="upd_Noticias_Archivo_xIdNoticia";
        $sparam=array(
            'pArchivo'=> $datos['Archivo'],
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
            'pIdNoticia'=> $datos['IdNoticia']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al actualizar archivo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    protected function ModificarOrden(array $datos): bool
    {
        $spnombre="upd_Noticias_Orden_xIdNoticia";
        $sparam=array(
            'pOrden'=> $datos['Orden'],
            'pIdNoticia'=> $datos['IdNoticia']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el orden. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

}
