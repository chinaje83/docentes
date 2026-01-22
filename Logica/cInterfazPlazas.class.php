<?php 
include(DIR_CLASES_DB."cInterfazPlazas.db.php");
class cInterfazPlazas extends cInterfazPlazasdb
{
	/**
	 * Constructor de la clase cInterfazPlazas.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion,$formato=FMT_TEXTO){
		parent::__construct($conexion,$formato);
	}
	/**
	 * Destructor de la clase cInterfazPlazas.
	 */
	function __destruct(){
		parent::__destruct();
	}
	/**
	 * Devuelve el mensaje de error almacenado
	 *
	 * @return array
	 */
	public function getError(): array {
		return $this->error;
	}
	
	public function BuscarxCodigo($datos, &$resultado,&$numfilas): bool
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}

    public function BusquedaAvanzadaCantidad($datos,&$resultado,&$numfilas): bool
    {
        $sparam=array(
            'xIdRegistro'=> 0,
            'IdRegistro'=> "",
            'xID'=> 0,
            'ID'=> "",
            'xCUPOF'=> 0,
            'CUPOF'=> "",
            'xidEscuelaAnexo'=> 0,
            'idEscuelaAnexo'=> "",
            'xIdEstado'=> 0,
            'IdEstado'=> ""
        );
        if(isset($datos['IdRegistro']) && $datos['IdRegistro']!="")
        {
            $sparam['IdRegistro']= $datos['IdRegistro'];
            $sparam['xIdRegistro']= 1;
        }
        if(isset($datos['ID']) && $datos['ID']!="")
        {
            $sparam['ID']= $datos['ID'];
            $sparam['xID']= 1;
        }
        if(isset($datos['CUPOF']) && $datos['CUPOF']!="")
        {
            $sparam['CUPOF']= $datos['CUPOF'];
            $sparam['xCUPOF']= 1;
        }
        if(isset($datos['idEscuelaAnexo']) && $datos['idEscuelaAnexo']!="")
        {
            $sparam['idEscuelaAnexo']= $datos['idEscuelaAnexo'];
            $sparam['xidEscuelaAnexo']= 1;
        }
        if(isset($datos['IdEstado']) && $datos['IdEstado']!="")
        {
            $sparam['IdEstado']= $datos['IdEstado'];
            $sparam['xIdEstado']= 1;
        }

        if (!parent::BusquedaAvanzadaCantidad($sparam,$resultado,$numfilas))
            return false;
        return true;
    }

	public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xIdRegistro'=> 0,
			'IdRegistro'=> "",
			'xID'=> 0,
			'ID'=> "",
			'xCUPOF'=> 0,
			'CUPOF'=> "",
			'xidEscuelaAnexo'=> 0,
			'idEscuelaAnexo'=> "",
			'xIdEstado'=> 0,
			'IdEstado'=> "",
            'xBaja'=> 0,
            'Baja'=> "-1",
			'limit'=> '',
			'orderby'=> "IdRegistro DESC"
		);
		if(isset($datos['IdRegistro']) && $datos['IdRegistro']!="")
		{
			$sparam['IdRegistro']= $datos['IdRegistro'];
			$sparam['xIdRegistro']= 1;
		}
		if(isset($datos['ID']) && $datos['ID']!="")
		{
			$sparam['ID']= $datos['ID'];
			$sparam['xID']= 1;
		}
		if(isset($datos['CUPOF']) && $datos['CUPOF']!="")
		{
			$sparam['CUPOF']= $datos['CUPOF'];
			$sparam['xCUPOF']= 1;
		}
		if(isset($datos['idEscuelaAnexo']) && $datos['idEscuelaAnexo']!="")
		{
			$sparam['idEscuelaAnexo']= $datos['idEscuelaAnexo'];
			$sparam['xidEscuelaAnexo']= 1;
		}
		if(isset($datos['IdEstado']) && $datos['IdEstado']!="")
		{
			$sparam['IdEstado']= $datos['IdEstado'];
			$sparam['xIdEstado']= 1;
		}
        if(isset($datos['Baja']) && $datos['Baja']!="")
        {
            $sparam['Baja']= $datos['Baja'];
            $sparam['xBaja']= 1;
        }

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];
		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];
		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}


	public function PlazasEjecucionesSP(&$spnombre,&$sparam): void
	{
		parent::PlazasEjecucionesSP($spnombre,$sparam);
	}




	public function PlazasEjecucionesSPResult(&$resultado, &$numfilas): bool
	{
		$this->PlazasEjecucionesSP($spnombre,$sparam);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	public function EscuelasPuestosSP(&$spnombre,&$sparam): void
	{
		parent::EscuelasPuestosSP($spnombre,$sparam);
	}


	public function EscuelasPuestosSPResult(&$resultado, &$numfilas): bool
	{
		$this->EscuelasPuestosSP($spnombre,$sparam);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	public function EscuelasSP(&$spnombre,&$sparam): void
	{
		parent::EscuelasSP($spnombre,$sparam);
	}


	public function EscuelasSPResult(&$resultado, &$numfilas): bool
	{
		$this->EscuelasSP($spnombre,$sparam);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	public function Plazas_EstadosSP(&$spnombre,&$sparam): void
	{
		parent::Plazas_EstadosSP($spnombre,$sparam);
	}


	public function Plazas_EstadosSPResult(&$resultado, &$numfilas): bool
	{
		$this->Plazas_EstadosSP($spnombre,$sparam);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	public function Insertar($datos,&$codigoInsertado): bool
	{
		if (!$this->_ValidarInsertar($datos))
			return false;
		$this->_SetearNull($datos);
		if (!parent::Insertar($datos,$codigoInsertado))
			return false;
		return true;
	}


	public function Modificar($datos): bool
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro)) {
            return false;
        }
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		return true;
	}


	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		if (!parent::Eliminar($datos))
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
		return true;
	}


	private function _ValidarModificar($datos,&$datosRegistro)
	{

		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		return true;
	}


	private function _ValidarEliminar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}


	private function _SetearNull(&$datos): void
	{

		if (!isset($datos['IdEjecucion']) || $datos['IdEjecucion']=="")
			$datos['IdEjecucion']="NULL";

		if (!isset($datos['ID']) || $datos['ID']=="")
			$datos['ID']="NULL";

		if (!isset($datos['CUPOF']) || $datos['CUPOF']=="")
			$datos['CUPOF']="NULL";

		if (!isset($datos['idPuesto']) || $datos['idPuesto']=="")
			$datos['idPuesto']="NULL";

		if (!isset($datos['idEscuelaAnexo']) || $datos['idEscuelaAnexo']=="")
			$datos['idEscuelaAnexo']="NULL";

		if (!isset($datos['idOrientacion']) || $datos['idOrientacion']=="")
			$datos['idOrientacion']="NULL";

		if (!isset($datos['idPlanEstudio']) || $datos['idPlanEstudio']=="")
			$datos['idPlanEstudio']="NULL";

		if (!isset($datos['idCargo']) || $datos['idCargo']=="")
			$datos['idCargo']="NULL";

		if (!isset($datos['idMateria']) || $datos['idMateria']=="")
			$datos['idMateria']="NULL";

		if (!isset($datos['Turno']) || $datos['Turno']=="")
			$datos['Turno']="NULL";

		if (!isset($datos['Anio']) || $datos['Anio']=="")
			$datos['Anio']="NULL";

		if (!isset($datos['Seccion']) || $datos['Seccion']=="")
			$datos['Seccion']="NULL";

		if (!isset($datos['CantidadModulos']) || $datos['CantidadModulos']=="")
			$datos['CantidadModulos']="NULL";

		if (!isset($datos['CantidadHoras']) || $datos['CantidadHoras']=="")
			$datos['CantidadHoras']="NULL";

		if (!isset($datos['RegimenSuplencias']) || $datos['RegimenSuplencias']=="")
			$datos['RegimenSuplencias']="NULL";

		if (!isset($datos['Temporalidad']) || $datos['Temporalidad']=="")
			$datos['Temporalidad']="NULL";

		if (!isset($datos['FechaActualizacion']) || $datos['FechaActualizacion']=="")
			$datos['FechaActualizacion']="NULL";

		if (!isset($datos['FechaAltaRegistro']) || $datos['FechaAltaRegistro']=="")
			$datos['FechaAltaRegistro']="NULL";

		if (!isset($datos['IdEstado']) || $datos['IdEstado']=="")
			$datos['IdEstado']="NULL";

		if (!isset($datos['Observaciones']) || $datos['Observaciones']=="")
			$datos['Observaciones']="NULL";
		
	}


	private function _ValidarDatosVacios($datos)
	{

        /*
		if (!isset($datos['IdEjecucion']) || $datos['IdEjecucion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una ejecucion",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['ID']) || $datos['ID']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id plaza",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['CUPOF']) || $datos['CUPOF']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un cupof",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['CUPOF']) && $datos['CUPOF']!="")
		{
			if (strlen($datos['CUPOF'])>255)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo CUPOF no puede ser mayor a 255 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		if (!isset($datos['idPuesto']) || $datos['idPuesto']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['idPuesto']) && $datos['idPuesto']!="")
		{
			if (strlen($datos['idPuesto'])>255)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo  no puede ser mayor a 255 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['idEscuelaAnexo']) || $datos['idEscuelaAnexo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id escuela",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		/*
		if (!isset($datos['idOrientacion']) || $datos['idOrientacion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		*/		/*
		if (!isset($datos['idPlanEstudio']) || $datos['idPlanEstudio']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		*/		/*
		if (!isset($datos['idCargo']) || $datos['idCargo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		*/		/*
		if (!isset($datos['idMateria']) || $datos['idMateria']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		*/		/*
		if (!isset($datos['Turno']) || $datos['Turno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['Turno']) && $datos['Turno']!="")
		{
			if (strlen($datos['Turno'])>255)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo  no puede ser mayor a 255 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['Anio']) || $datos['Anio']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['Anio']) && $datos['Anio']!="")
		{
			if (strlen($datos['Anio'])>255)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo  no puede ser mayor a 255 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		if (!isset($datos['Seccion']) || $datos['Seccion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['Seccion']) && $datos['Seccion']!="")
		{
			if (strlen($datos['Seccion'])>255)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo  no puede ser mayor a 255 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['CantidadModulos']) || $datos['CantidadModulos']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['CantidadModulos']) && $datos['CantidadModulos']!="")
		{
			if (strlen($datos['CantidadModulos'])>255)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo  no puede ser mayor a 255 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['CantidadHoras']) || $datos['CantidadHoras']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['RegimenSuplencias']) || $datos['RegimenSuplencias']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Temporalidad']) || $datos['Temporalidad']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['FechaActualizacion']) || $datos['FechaActualizacion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['FechaActualizacion']) && $datos['FechaActualizacion']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['FechaActualizacion'],"FechaDDMMAAAA"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una fecha valida para el campo .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
        */
		if (!isset($datos['FechaAltaRegistro']) || $datos['FechaAltaRegistro']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una fecha de alta de registro",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (isset($datos['FechaAltaRegistro']) && $datos['FechaAltaRegistro']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['FechaAltaRegistro'],"FechaDDMMAAAA"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una fecha valida para el campo .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['IdEstado']) || $datos['IdEstado']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un estado registro",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdEstado']) && $datos['IdEstado']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdEstado'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el campo Estado Registro.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['IdEstado'])>2)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Estado Registro no puede ser mayor a 2 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (isset($datos['Observaciones']) && $datos['Observaciones']!="")
		{
			if (strlen($datos['Observaciones'])>255)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo  no puede ser mayor a 255 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		return true;
	}




}