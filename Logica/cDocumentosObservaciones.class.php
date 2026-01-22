<?php 
include(DIR_CLASES_DB."cDocumentosObservaciones.db.php");

class cDocumentosObservaciones extends cDocumentosObservacionesdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdObservacion'=> 0,
			'IdObservacion'=> "",
			'xIdDocumento'=> 0,
			'IdDocumento'=> "",
			'xObservaciones'=> 0,
			'Observaciones'=> "",
			'xIdEstado'=> 0,
			'IdEstado'=> "",
			'limit'=> '',
			'orderby'=> "IdObservacion DESC"
		);

		if(isset($datos['IdObservacion']) && $datos['IdObservacion']!="")
		{
			$sparam['IdObservacion']= $datos['IdObservacion'];
			$sparam['xIdObservacion']= 1;
		}
		if(isset($datos['IdDocumento']) && $datos['IdDocumento']!="")
		{
			$sparam['IdDocumento']= $datos['IdDocumento'];
			$sparam['xIdDocumento']= 1;
		}
		if(isset($datos['Observaciones']) && $datos['Observaciones']!="")
		{
			$sparam['Observaciones']= $datos['Observaciones'];
			$sparam['xObservaciones']= 1;
		}
		
		if(isset($datos['IdEstado']) && $datos['IdEstado']!="")
		{
			$sparam['IdEstado']= $datos['IdEstado'];
			$sparam['xIdEstado']= 1;
		}



		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BusquedaAvanzadaxIdDocumento($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdObservacion'=> 0,
			'IdObservacion'=> "",
			'IdDocumento'=> $datos['IdDocumento'],
			'xObservaciones'=> 0,
			'Observaciones'=> "",
			'xIdEstado'=> 0,
			'IdEstado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdObservacion DESC"
		);

		if(isset($datos['IdObservacion']) && $datos['IdObservacion']!="")
		{
			$sparam['IdObservacion']= $datos['IdObservacion'];
			$sparam['xIdObservacion']= 1;
		}
		if(isset($datos['Observaciones']) && $datos['Observaciones']!="")
		{
			$sparam['Observaciones']= $datos['Observaciones'];
			$sparam['xObservaciones']= 1;
		}
		
		if(isset($datos['IdEstado']) && $datos['IdEstado']!="")
		{
			$sparam['IdEstado']= $datos['IdEstado'];
			$sparam['xIdEstado']= 1;
		}



		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzadaxIdDocumento($sparam,$resultado,$numfilas))
			return false;
		return true;
	}



	public function Insertar($datos,&$codigoinsertado)
	{
		$oObjeto = new cDocumentosPermisos($this->conexion,$this->formato);
		$datosBuqueda = array();
		$datosBuqueda['IdDocumento'] = $datos['IdDocumento'];
		$datosBuqueda['IdTipoDocumento'] = $datos['IdTipoDocumento'];
		$datosBuqueda['IdArea'] = $_SESSION['IdArea'];
		if(!$oObjeto->PuedeAgregarComentario($datosBuqueda,$resultado,$numfilas))
			return false;

		if ($numfilas<1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no tiene permisos para agregar el comentario.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}		
		if (!$this->_ValidarInsertar($datos))
			return false;
		
		$this->_SetearNull($datos);
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['IdEstado'] = DOCOBSERVACIONNUEVO;
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		return true;
	}



	public function Modificar($datos)
	{
		
		$oObjeto = new cDocumentosPermisos($this->conexion,$this->formato);
		$datosBuqueda = array();
		$datosBuqueda['IdDocumento'] = $datos['IdDocumento'];
		$datosBuqueda['IdTipoDocumento'] = $datos['IdTipoDocumento'];
		$datosBuqueda['IdArea'] = $_SESSION['IdArea'];
		if(!$oObjeto->PuedeModificarComentario($datosBuqueda,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no tiene permisos para modificar el comentario.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		
		
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$datosmodif['IdObservacion'] = $datos['IdObservacion'];
		$datosmodif['IdEstado'] = ELIMINADO;
		if (!$this->ModificarIdEstado($datosmodif))
			return false;
		return true;
	}



	public function ModificarIdEstado($datos)
	{
		$datos['UltimaModificacionUsuario']= $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= date("Y-m-d H:i:s");

		if (!parent::ModificarIdEstado($datos))
			return false;
		return true;
	}



	public function Activar($datos)
	{
		$datosmodif['IdObservacion'] = $datos['IdObservacion'];
		$datosmodif['IdEstado'] = ACTIVO;
		if (!$this->ModificarIdEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdObservacion'] = $datos['IdObservacion'];
		$datosmodif['IdEstado'] = NOACTIVO;
		if (!$this->ModificarIdEstado($datosmodif))
			return false;
		return true;
	}
	
	
	public function ValidarAccionMetodoPrevio($datos,$datosWorkflow)
	{
		// Modifico el estado de la observacion;
		
		if(isset($datos['IdDocumento']) && $datos['IdDocumento']!="")
		{
		
			$datosObservacion['IdDocumento'] = $datos['IdDocumento'];
			$datosObservacion['IdEstado'] = DOCOBSERVACIONNUEVO;
			$datosObservacion['limit'] = "Limit 0,1";
			$datosObservacion['Orderby'] = "AltaFecha DESC";
			if(!$this->BusquedaAvanzada($datosObservacion,$resultadoObservacion,$numfilasObservacion))
				return false;
			
			
			if(isset($datosWorkflow['AccionObligatorio']) && $datosWorkflow['AccionObligatorio']==1)
			{
				if($numfilasObservacion != 1)
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un comentario",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;


				}
				
				
			}
				
		}
		
		
		return true;
	}
	
	
	public function ValidarAccionMetodoPosterior($datos,$datosTipoDocumento)
	{
		
		// Modifico el estado de la observacion;
		$datosObservacion['IdDocumento'] = $datos['IdDocumento'];
		$datosObservacion['IdEstado'] = DOCOBSERVACIONNUEVO;
		$datosObservacion['limit'] = "Limit 0,1";
		$datosObservacion['Orderby'] = "AltaFecha DESC";
		if(!$this->BusquedaAvanzada($datosObservacion,$resultadoObservacion,$numfilasObservacion))
			return false;	
		
		if($numfilasObservacion == "1")
		{
			
			
			
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



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['IdDocumento']) || $datos['IdDocumento']=="")
			$datos['IdDocumento']="NULL";

		if (!isset($datos['Observaciones']) || $datos['Observaciones']=="")
			$datos['Observaciones']="NULL";

		if (!isset($datos['AltaFecha']) || $datos['AltaFecha']=="")
			$datos['AltaFecha']="NULL";

		if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario']=="")
			$datos['AltaUsuario']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdDocumento']) || $datos['IdDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Id Documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdDocumento']) && $datos['IdDocumento']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdDocumento'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['Observaciones']) || trim($datos['Observaciones'])=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una Observaci�n",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	
}
?>