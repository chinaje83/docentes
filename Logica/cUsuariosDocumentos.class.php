<?php 
 
include(DIR_CLASES_DB."cUsuariosDocumentos.db.php");

class cUsuariosDocumentos extends cUsuariosDocumentosdb
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

	public function BuscarxIdDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarDocumentosAgrupadoxIdTipoDocumentoxIdUsuario($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarDocumentosAgrupadoxIdTipoDocumentoxIdUsuario($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	



	public function BuscarxUsuario($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxUsuario($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarxUsuarioxTipoDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxUsuarioxTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BuscarUsuariosSinDocumentos($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xNombre'=> 0,
			'Nombre'=> "",
			'xApellido'=> 0,
			'Apellido'=> "",
			'xTipoEvitar'=> 0,
			'TipoEvitar'=> "",
			'xIdEstado'=> 0,
			'IdEstado'=> "",
			'limit'=> '',
			'orderby'=> "a.IdUsuario DESC"
		);

		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['Apellido']) && $datos['Apellido']!="")
		{
			$sparam['Apellido']= $datos['Apellido'];
			$sparam['xApellido']= 1;
		}
		if(isset($datos['TipoEvitar']) && $datos['TipoEvitar']!="")
		{
			$sparam['TipoEvitar']= $datos['TipoEvitar'];
			$sparam['xTipoEvitar']= 1;
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
		if (!parent::BuscarUsuariosSinDocumentos($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function Actualizar($datos)
	{
		if(empty($datos['IdDocumento']))
			$datos['IdDocumento'] = array();
		
		if (!$this->BuscarxUsuario($datos,$resultado,$numfilas))
			return false;
		
		$valoresInsertados = array();
		if($numfilas > 0)
		{
			while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
				$valoresInsertados[] = $fila['IdDocumento'];
		}
		
		$datosMod['AltaFecha'] = date("Y-m-d H:i:s");
		$datosMod['UltimaModificacionFecha'] = $datosMod['AltaFecha'];
		$datosMod['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datosDel['IdUsuario'] = $datosMod['IdUsuario'] = $datos['IdUsuario'];
		
		$docIns = array_diff($datos['IdDocumento'],$valoresInsertados);
		$docDel = array_diff($valoresInsertados,$datos['IdDocumento']);
		
		
		$datosIns = $datosMod;
		foreach($docIns as $IdDocumento)
		{
			$datosIns['IdDocumento'] = $IdDocumento;
			if(!$this->Insertar($datosIns))
				return false;	
		}
		
		foreach($docDel as $IdDocumento)
		{
			$datosDel['IdDocumento'] = $IdDocumento;
			if(!$this->Eliminar($datosDel))
				return false;	
		}
		
		return true;
	}
	
	
	public function InsertarDocumentos($datos)
	{
		
		if(empty($datos['IdDocumento']))
			$datos['IdDocumento'] = array();
		
		
		$datosMod['AltaFecha'] = date("Y-m-d H:i:s");
		$datosMod['UltimaModificacionFecha'] = $datosMod['AltaFecha'];
		$datosMod['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datosDel['IdUsuario'] = $datosMod['IdUsuario'] = $datos['IdUsuario'];
		
		$datosIns = $datosMod;
		if(count($datos['IdDocumento'])>0)
		{
			foreach($datos['IdDocumento'] as $IdDocumento)
			{
				$datosIns['IdDocumento'] = $IdDocumento;
				if(!$this->Insertar($datosIns))
					return false;	
			}
		}
		return true;
	}
	
	
	
	
	

	public function Insertar($datos)
	{
		$datos['AltaFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionFecha'] = $datos['AltaFecha'];
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['RegistroSeguridad'] = md5($datos['IdUsuario'].CLAVEENCRIPTACION.$datos['IdDocumento']);		
		if (!$this->_ValidarInsertar($datos))
			return false;
		$this->_SetearNull($datos);
		if (!parent::Insertar($datos))
			return false;
	
		return true;
	}



	
	public function Eliminar($datos)
	{
			
		if (!$this->_ValidarEliminar($datos))
			return false;
	
		if (!parent::Eliminar($datos))
			return false;
		
		return true;
	}



//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------




	private function _ValidarInsertar(&$datos)
	{
		$oDocumentos = new cDocumentos($this->conexion,$this->formato);
		if (!$oDocumentos->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un c&oacute;digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		$datos['IdTipoDocumento'] = $datosRegistro['IdTipoDocumento'];
		
		if (!$this->_ValidarDatosVacios($datos))
			return false;

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
		if (!isset($datos['BajaFecha']) || $datos['BajaFecha']=="")
			$datos['BajaFecha']="NULL";
		if (!isset($datos['RegistroSeguridad']) || $datos['RegistroSeguridad']=="")
			$datos['RegistroSeguridad']="NULL";
		
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{
		if (!isset($datos['IdUsuario']) || $datos['IdUsuario']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un usuario",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdUsuario'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->conexion->TraerCampo('Usuarios','IdUsuario',array('IdUsuario='.$datos['IdUsuario']),$dato,$numfilas,$errno))
			return false;
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdDocumento']) || $datos['IdDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdDocumento'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->conexion->TraerCampo('Documentos','IdDocumento',array('IdDocumento='.$datos['IdDocumento']),$dato,$numfilas,$errno))
			return false;
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		
		if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tipo de documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTipoDocumento'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		


		return true;
	}
	
}
?>