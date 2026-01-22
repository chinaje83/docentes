<?php
include(DIR_CLASES_DB."cPlanesEducativos.db.php");
class cPlanesEducativos extends cPlanesEducativosdb
{
	/**
	 * Constructor de la clase cPlanesEducativos.
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
	 * Destructor de la clase cPlanesEducativos.
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


	public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xNombre'=> 0,
			'Nombre'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
            'xIdPlanEducativo'=> 0,
            'IdPlanEducativo'=> "",
            'xIdExterno'=> 0,
            'IdExterno'=> "",
            'xFechaDesde'=> 0,
            'FechaDesde'=> "",
            'xFechaHasta'=> 0,
            'FechaHasta'=> "",
			'limit'=> '',
			'orderby'=> "IdPlanEducativo ASC"
		);
        if (isset($datos['Nombre']) && $datos['Nombre'] != "")
        {
            $sparam['Nombre'] = utf8_decode($datos['Nombre']);
            $sparam['xNombre'] = 1;
		}
		if(isset($datos['Estado']) && $datos['Estado']!="")
		{
			$sparam['Estado']= $datos['Estado'];
			$sparam['xEstado']= 1;
		}
        if(isset($datos['IdPlanEducativo']) && $datos['IdPlanEducativo']!="")
        {
            $sparam['IdPlanEducativo']= $datos['IdPlanEducativo'];
            $sparam['xIdPlanEducativo']= 1;
        }
        if(isset($datos['IdExterno']) && $datos['IdExterno']!="")
        {
            $sparam['IdExterno']= $datos['IdExterno'];
            $sparam['xIdExterno']= 1;
        }
        if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
        {
            $sparam['FechaDesde']= $datos['FechaDesde'];
            $sparam['xFechaDesde']= 1;
        }
        if(isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
        {
            $sparam['FechaHasta']= $datos['FechaHasta'];
            $sparam['xFechaHasta']= 1;
        }

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];
		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];
		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BusquedaAvanzadaCsv($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xNombre'=> 0,
			'Nombre'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
            'xIdPlanEducativo'=> 0,
            'IdPlanEducativo'=> "",
            'xIdExterno'=> 0,
            'IdExterno'=> "",
            'xFechaDesde'=> 0,
            'FechaDesde'=> "",
            'xFechaHasta'=> 0,
            'FechaHasta'=> "",
			'limit'=> '',
			'orderby'=> "IdPlanEducativo ASC"
		);
        if (isset($datos['Nombre']) && $datos['Nombre'] != "")
        {
            $sparam['Nombre'] = utf8_decode($datos['Nombre']);
            $sparam['xNombre'] = 1;
		}
		if(isset($datos['Estado']) && $datos['Estado']!="")
		{
			$sparam['Estado']= $datos['Estado'];
			$sparam['xEstado']= 1;
		}
        if(isset($datos['IdPlanEducativo']) && $datos['IdPlanEducativo']!="")
        {
            $sparam['IdPlanEducativo']= $datos['IdPlanEducativo'];
            $sparam['xIdPlanEducativo']= 1;
        }
        if(isset($datos['IdExterno']) && $datos['IdExterno']!="")
        {
            $sparam['IdExterno']= $datos['IdExterno'];
            $sparam['xIdExterno']= 1;
        }
        if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
        {
            $sparam['FechaDesde']= $datos['FechaDesde'];
            $sparam['xFechaDesde']= 1;
        }
        if(isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
        {
            $sparam['FechaHasta']= $datos['FechaHasta'];
            $sparam['xFechaHasta']= 1;
        }

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];
		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];
		if (!parent::BusquedaAvanzadaCsv($sparam,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas): bool
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function Insertar($datos,&$codigoInsertado): bool
	{
        if (!$this->_ValidarInsertar($datos))
			return false;

        $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],"dd/mm/aaaa","aaaa-mm-dd");
        $datos['FechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'],"dd/mm/aaaa","aaaa-mm-dd");

		/*FuncionesPHPLocal::print_pre($datos);*/

        $this->_SetearNull($datos);
		$this->ObtenerProximoOrden($datos,$proxorden);
		$datos['IdPlanEducativo'] = $proxorden;
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoInsertado))
			return false;
		$oAuditoriasPlanesEducativos = new cAuditoriasPlanesEducativos($this->conexion,$this->formato);
		$datos['IdPlanEducativo'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasPlanesEducativos->InsertarLog($datos,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Modificar($datos): bool
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

        $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],"dd/mm/aaaa","aaaa-mm-dd");
        $datos['FechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'],"dd/mm/aaaa","aaaa-mm-dd");

		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		$oAuditoriasPlanesEducativos = new cAuditoriasPlanesEducativos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasPlanesEducativos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasPlanesEducativos = new cAuditoriasPlanesEducativos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasPlanesEducativos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		$datosmodif['IdPlanEducativo'] = $datos['IdPlanEducativo'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}


	public function ModificarEstado($datos): bool
	{
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}


	public function Activar(array $datos): bool
	{
		$datosmodif['IdPlanEducativo'] = $datos['IdPlanEducativo'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasPlanesEducativos = new cAuditoriasPlanesEducativos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasPlanesEducativos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function DesActivar(array $datos): bool
	{
		$datosmodif['IdPlanEducativo'] = $datos['IdPlanEducativo'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasPlanesEducativos = new cAuditoriasPlanesEducativos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasPlanesEducativos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function ModificarOrdenCompleto($datos): bool
	{
		$datosmodif['IdPlanEducativo'] = 1;
		$arregloOrden = explode(",",$datos['orden']);
		foreach ($arregloOrden as $IdPlanEducativo){
			$datosmodif['IdPlanEducativo'] = $IdPlanEducativo;
			if (!parent::ModificarOrden($datosmodif))
				return false;
			$datosmodif['IdPlanEducativo']++;
		}
		return true;
	}


	private function ObtenerProximoOrden(array $datos, ?int &$proxorden): bool
	{
		$proxorden = 0;
		if (!parent::BuscarUltimoOrden($datos,$resultado,$numfilas))
			return false;
		if ($numfilas!=0){
			$datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
			$proxorden = $datos['maximo'] + 1;
		}
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


		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		if (!isset($datos['FechaDesde']) || $datos['FechaDesde']=="")
			$datos['FechaDesde']="NULL";

		if (!isset($datos['FechaHasta']) || $datos['FechaHasta']=="")
			$datos['FechaHasta']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";

	}


	private function _ValidarDatosVacios($datos)
	{
		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			if (strlen($datos['Nombre'])>120)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Nombre no puede ser mayor a 120 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
        if (!isset($datos['IdExterno']) || $datos['IdExterno']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar IdExterno",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

		if (!isset($datos['FechaDesde']) || $datos['FechaDesde']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar una Fecha Desde",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['FechaDesde'],"FechaDDMMAAAA"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una fecha valida para el campo Fecha Desde.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['FechaHasta']) || $datos['FechaHasta']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar una Fecha Hasta",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['FechaHasta'],"FechaDDMMAAAA"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una fecha valida para el campo Fecha Hasta.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if ((isset($datos['FechaDesde']) && $datos['FechaDesde']!="") && (isset($datos['FechaHasta']) && $datos['FechaHasta']!=""))
        {
            if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "")
                $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos["FechaDesde"],"dd/mm/aaaa","aaaa-mm-dd");

            if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != "")
                $datos['FechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos["FechaHasta"],"dd/mm/aaaa","aaaa-mm-dd");

            $fechaDesde = new DateTime($datos['FechaDesde']);
            $fechaHasta = new DateTime($datos['FechaHasta']);
            if ($fechaDesde > $fechaHasta) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Fecha Desde no puede ser mayor a Fecha Hasta",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }

		return true;
	}
}
