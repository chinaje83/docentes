<?php 
include(DIR_CLASES_DB."cReglasCargaNovedades.db.php");

class cReglasCargaNovedades extends cReglasCargaNovedadesdb
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
	
	
	
	public function BuscarxTipoOrganizacionxAnio($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxTipoOrganizacionxAnio($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarxTipoOrganizacionxAnioxCargo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxTipoOrganizacionxAnioxCargo($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarxTipoOrganizacionxAnioxCargoxNivelEnsenanza($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'TipoOrganizacion' => $datos['TipoOrganizacion'],
			'Anio' => $datos['Anio'],
			'Cargo'=> $datos['Cargo'],
			'NivelEnsenanza'=> "",
			'xNivelEnsenanza'=> 0,
			'xNivelEnsenanzaNull'=> 0
			
		);
		
		if(isset($datos['NivelEnsenanza']) && $datos['NivelEnsenanza']!="")
		{	
			$sparam['NivelEnsenanza'] = $datos['NivelEnsenanza'];
			$sparam['xNivelEnsenanza'] = 1;
		
		}
		else
		   $sparam['xNivelEnsenanzaNull'] = 1;
		
		if (!parent::BuscarxTipoOrganizacionxAnioxCargoxNivelEnsenanza($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdReglasCargaNovedades' => 0,
			'IdReglasCargaNovedades' => "", 
			'xTipoOrganizacion'=> 0,
			'TipoOrganizacion'=> "",
			'xCargo'=> 0,
			'Cargo'=> "",
			'xAnio'=> 0,
			'Anio'=> "",
			'xNivelEnsenanza'=> 0,
			'xNivelEnsenanzaNull'=> 0,
			'NivelEnsenanza'=> "",
			'xModalidadCarrera'=> 0,
			'ModalidadCarrera'=> "",
			'xAsignatura'=> 0,
			'Asignatura'=> "",
			'xArea'=> 0,
			'Area'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "TipoOrganizacion DESC"
		);

		if(isset($datos['IdReglasCargaNovedades']) && $datos['IdReglasCargaNovedades']!="")
		{
			$sparam['IdReglasCargaNovedades']= $datos['IdReglasCargaNovedades'];
			$sparam['xIdReglasCargaNovedades']= 1;
		}
		if(isset($datos['TipoOrganizacion']) && $datos['TipoOrganizacion']!="")
		{
			$sparam['TipoOrganizacion']= $datos['TipoOrganizacion'];
			$sparam['xTipoOrganizacion']= 1;
		}
		if(isset($datos['Cargo']) && $datos['Cargo']!="")
		{
			$sparam['Cargo']= $datos['Cargo'];
			$sparam['xCargo']= 1;
		}
		if(isset($datos['Anio']) && $datos['Anio']!="")
		{
			$sparam['Anio']= $datos['Anio'];
			$sparam['xAnio']= 1;
		}
		if(isset($datos['NivelEnsenanza']) && $datos['NivelEnsenanza']!="")
		{
			if($datos['NivelEnsenanza']=="-1")
				$sparam['xNivelEnsenanzaNull']= 1;
			else
			{
				$sparam['NivelEnsenanza']= $datos['NivelEnsenanza'];
				$sparam['xNivelEnsenanza']= 1;
			}
		}
		if(isset($datos['ModalidadCarrera']) && $datos['ModalidadCarrera']!="")
		{
			$sparam['ModalidadCarrera']= $datos['ModalidadCarrera'];
			$sparam['xModalidadCarrera']= 1;
		}
		if(isset($datos['Asignatura']) && $datos['Asignatura']!="")
		{
			$sparam['Asignatura']= $datos['Asignatura'];
			$sparam['xAsignatura']= 1;
		}
		if(isset($datos['Area']) && $datos['Area']!="")
		{
			$sparam['Area']= $datos['Area'];
			$sparam['xArea']= 1;
		}
		
		if(isset($datos['Estado']) && $datos['Estado']!="")
		{
			$sparam['Estado']= $datos['Estado'];
			$sparam['xEstado']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function TiposOrganizacionSP(&$spnombre,&$sparam)
	{
		if (!parent::TiposOrganizacionSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function TiposOrganizacionSPResult(&$resultado,&$numfilas)
	{
		if (!$this->TiposOrganizacionSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function CargosSP(&$spnombre,&$sparam)
	{
		if (!parent::CargosSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function CargosSPResult(&$resultado,&$numfilas)
	{
		if (!$this->CargosSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function NivelEnsenanzasSP(&$spnombre,&$sparam)
	{
		if (!parent::NivelEnsenanzasSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function NivelEnsenanzasSPResult(&$resultado,&$numfilas)
	{
		if (!$this->NivelEnsenanzasSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		$oAuditoriasReglasCargaNovedades = new cAuditoriasReglasCargaNovedades($this->conexion,$this->formato);
		$datos['IdReglasCargaNovedades'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasReglasCargaNovedades->InsertarLog($datos,$codigoInsertadolog))
			return false;

		$datos['TipoOrganizacion'] =$codigoinsertado;
		if (!$this->Publicar($datos))
			return false;

		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		$oAuditoriasReglasCargaNovedades = new cAuditoriasReglasCargaNovedades($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasReglasCargaNovedades->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;

		if (!$this->Publicar($datos))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		/*$oAuditoriasReglasCargaNovedades = new cAuditoriasReglasCargaNovedades($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasReglasCargaNovedades->InsertarLog($datosLog,$codigoInsertadolog))
			return false;*/

		$datosmodif['IdReglasCargaNovedades'] = $datos['IdReglasCargaNovedades'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function ModificarEstado($datos)
	{
		if (!parent::ModificarEstado($datos))
			return false;
		if (!$this->Publicar($datos))
			return false;

		return true;
	}



	public function Activar($datos)
	{
		$datosmodif['IdReglasCargaNovedades'] = $datos['IdReglasCargaNovedades'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdReglasCargaNovedades'] = $datos['IdReglasCargaNovedades'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function Publicar($datos)
	{
		if (!$this->PublicarListadoJson())
			return false;
		return true;
	}



	public function GuardarDatosJson($nombrearchivo,$carpeta,$array)
	{
		$datosJson = FuncionesPHPLocal::ConvertiraUtf8($array);
		$jsonData = json_encode($datosJson);
		if(!is_dir($carpeta)){
			@mkdir($carpeta);
		}
		if(!FuncionesPHPLocal::GuardarArchivo($carpeta,$jsonData,$nombrearchivo.".json"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_INF,"Error, al generar el archivo json. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}



	public function EliminarDatosJson($nombrearchivo,$carpeta)
	{
		if(file_exists($carpeta.$nombrearchivo.".json"))
		{
			unlink($carpeta.$nombrearchivo.".json");
		}
		return true;
	}



	public function PublicarListadoJson()
	{
		$nombrearchivo = "reg_reglas_carga_novedades";
		$carpeta = PUBLICA."json/";
		if(!$this->GerenarArrayDatosJsonListado($array))
			return false;
		if(count($array)>0)
		{
			if(!$this->GuardarDatosJson($nombrearchivo,$carpeta,$array))
				return false;
		}
		else
		{
			if(!$this->EliminarDatosJson($nombrearchivo,$carpeta))
				return false;
		}
		return true;
	}



	public function GerenarArrayDatosJsonListado(&$array)
	{
		$array = array();
		$datos['Estado'] = ACTIVO;
		if(!$this->BusquedaAvanzada($datos,$resultados,$numfilas))
			return false;
		if($numfilas>0)
		{
			while($fila = $this->conexion->ObtenerSiguienteRegistro($resultados))
			{
				$array[$fila['IdReglasCargaNovedades']] = $fila;
			}
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un codigo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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

		if (!isset($datos['TipoOrganizacion']) || $datos['TipoOrganizacion']=="")
			$datos['TipoOrganizacion']="NULL";

		if (!isset($datos['Cargo']) || $datos['Cargo']=="")
			$datos['Cargo']="NULL";

		if (!isset($datos['Anio']) || $datos['Anio']=="")
			$datos['Anio']="NULL";

		if (!isset($datos['NivelEnsenanza']) || $datos['NivelEnsenanza']=="")
			$datos['NivelEnsenanza']="NULL";

		if (!isset($datos['ModalidadCarrera']) || $datos['ModalidadCarrera']=="")
			$datos['ModalidadCarrera']="NULL";

		if (!isset($datos['Asignatura']) || $datos['Asignatura']=="")
			$datos['Asignatura']="NULL";

		if (!isset($datos['Area']) || $datos['Area']=="")
			$datos['Area']="NULL";

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

		if (!isset($datos['TipoOrganizacion']) || $datos['TipoOrganizacion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tipo Organizacion",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		/*if (!$this->conexion->TraerCampo('TiposOrganizacion','TipoOrganizacion',array('TipoOrganizacion='.$datos['TipoOrganizacion']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		
		
		
		if (!isset($datos['Cargo']) || $datos['Cargo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un cargo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		/*if (!$this->conexion->TraerCampo('Cargos','Codigo',array('Codigo='.$datos['Cargo']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		if (!isset($datos['Anio']) || $datos['Anio']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un a�o",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['Anio']) && $datos['Anio']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Anio'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		/*if (!isset($datos['NivelEnsenanza']) || $datos['NivelEnsenanza']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nivel de ense�anza",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		
		if (isset($datos['NivelEnsenanza']) && $datos['NivelEnsenanza']!="")
		{
			/*if (!$this->conexion->TraerCampo('NivelEnsenanzas','Codigo',array('Codigo='.$datos['NivelEnsenanza']),$dato,$numfilas,$errno))
				return false;
	
	
			if ($numfilas!=1)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}*/
		}
		

		if (!isset($datos['ModalidadCarrera']) || $datos['ModalidadCarrera']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una modalidad carrera",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['ModalidadCarrera']) && $datos['ModalidadCarrera']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['ModalidadCarrera'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['Asignatura']) || $datos['Asignatura']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una asignatura",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['Asignatura']) && $datos['Asignatura']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Asignatura'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['Area']) || $datos['Area']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un �rea",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['Area']) && $datos['Area']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Area'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		

		

		
		return true;
	}





}
?>