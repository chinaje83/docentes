<?php 
include(DIR_CLASES_DB."cReglasGrupoSubGrupoOcupacional.db.php");

class cReglasGrupoSubGrupoOcupacional extends cReglasGrupoSubGrupoOcupacionaldb
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
			'xIdReglasGrupoSubGrupoOcupacional'=> 0,
			'IdReglasGrupoSubGrupoOcupacional'=> "",
			'xCodigoRegimenEstatutario'=> 0,
			'CodigoRegimenEstatutario'=> "",
			'xCodigoRevista'=> 0,
			'CodigoRevista'=> "",
			'xCodigoGrupo'=> 0,
			'CodigoGrupo'=> "",
			'xCodigoSubGrupo'=> 0,
			'CodigoSubGrupo'=> "",
			'xCategoriaDesde'=> 0,
			'CategoriaDesde'=> "",
			'xCategoriaHasta'=> 0,
			'CategoriaHasta'=> "",
			'limit'=> '',
			'orderby'=> "IdReglasGrupoSubGrupoOcupacional DESC"
		);

		if(isset($datos['IdReglasGrupoSubGrupoOcupacional']) && $datos['IdReglasGrupoSubGrupoOcupacional']!="")
		{
			$sparam['IdReglasGrupoSubGrupoOcupacional']= $datos['IdReglasGrupoSubGrupoOcupacional'];
			$sparam['xIdReglasGrupoSubGrupoOcupacional']= 1;
		}
		if(isset($datos['CodigoRegimenEstatutario']) && $datos['CodigoRegimenEstatutario']!="")
		{
			$sparam['CodigoRegimenEstatutario']= $datos['CodigoRegimenEstatutario'];
			$sparam['xCodigoRegimenEstatutario']= 1;
		}
		if(isset($datos['CodigoRevista']) && $datos['CodigoRevista']!="")
		{
			$sparam['CodigoRevista']= $datos['CodigoRevista'];
			$sparam['xCodigoRevista']= 1;
		}
		if(isset($datos['CodigoGrupo']) && $datos['CodigoGrupo']!="")
		{
			$sparam['CodigoGrupo']= $datos['CodigoGrupo'];
			$sparam['xCodigoGrupo']= 1;
		}
		if(isset($datos['CodigoSubGrupo']) && $datos['CodigoSubGrupo']!="")
		{
			$sparam['CodigoSubGrupo']= $datos['CodigoSubGrupo'];
			$sparam['xCodigoSubGrupo']= 1;
		}
		if(isset($datos['CategoriaDesde']) && $datos['CategoriaDesde']!="")
		{
			$sparam['CategoriaDesde']= $datos['CategoriaDesde'];
			$sparam['xCategoriaDesde']= 1;
		}
		if(isset($datos['CategoriaHasta']) && $datos['CategoriaHasta']!="")
		{
			$sparam['CategoriaHasta']= $datos['CategoriaHasta'];
			$sparam['xCategoriaHasta']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarRegimenEstatutarios($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'limit'=> '',
			'orderby'=> "Codigo ASC"
		);
		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];
		
		if (!parent::BuscarRegimenEstatutarios($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarRevistas($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'CodigoRegimenEstatutario' =>$datos['CodigoRegimenEstatutario'],
			'limit'=> '',
			'orderby'=> "Codigo ASC"
		);
		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];
		
		if (!parent::BuscarRevistas($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarGruposOcupacional($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'CodigoRegimenEstatutario' =>$datos['CodigoRegimenEstatutario'],
			'CodigoRevista' =>$datos['CodigoRevista'],
			'limit'=> '',
			'orderby'=> "Codigo ASC"
		);
		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];
		
		if (!parent::BuscarGruposOcupacional($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarSubGruposOcupacional($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'CodigoRegimenEstatutario' =>$datos['CodigoRegimenEstatutario'],
			'CodigoRevista' =>$datos['CodigoRevista'],
			'CodigoGrupo' =>$datos['CodigoGrupo'],
			'limit'=> '',
			'orderby'=> "Codigo ASC"
		);
		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];
		
		if (!parent::BuscarSubGruposOcupacional($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BuscarCargos($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'CodigoRegimenEstatutario' =>$datos['CodigoRegimenEstatutario'],
			'CodigoRevista' =>$datos['CodigoRevista'],
			'CodigoGrupo' =>$datos['CodigoGrupo'],
			'xCodigoSubGrupo' => 0,
			'CodigoSubGrupo' =>"",
			'limit'=> '',
			'orderby'=> "Codigo ASC"
		);
		
		
		if(isset($datos['CodigoSubGrupo']) && $datos['CodigoSubGrupo']!="")
		{
			$sparam['CodigoSubGrupo']= $datos['CodigoSubGrupo'];
			$sparam['xCodigoSubGrupo']= 1;
		}
		
		
		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];
		
		if (!parent::BuscarCargos($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BuscarRegla($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'CodigoRegimenEstatutario' =>$datos['CodigoRegimenEstatutario'],
			'CodigoRevista' =>$datos['CodigoRevista'],
			'CodigoGrupo' =>$datos['CodigoGrupo'],
			'CodigoCargo' =>$datos['CodigoCargo'],
			'xCodigoSubGrupo' => 0,
			'CodigoSubGrupo' =>"",
		);
		
		
		if(isset($datos['CodigoSubGrupo']) && $datos['CodigoSubGrupo']!="")
		{
			$sparam['CodigoSubGrupo']= $datos['CodigoSubGrupo'];
			$sparam['xCodigoSubGrupo']= 1;
		}
		
		if (!parent::BuscarRegla($sparam,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
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

		$oAuditoriasReglasGrupoSubGrupoOcupacional = new cAuditoriasReglasGrupoSubGrupoOcupacional($this->conexion,$this->formato);
		$datos['IdReglasGrupoSubGrupoOcupacional'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasReglasGrupoSubGrupoOcupacional->InsertarLog($datos,$codigoInsertadolog))
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

		$oAuditoriasReglasGrupoSubGrupoOcupacional = new cAuditoriasReglasGrupoSubGrupoOcupacional($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasReglasGrupoSubGrupoOcupacional->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasReglasGrupoSubGrupoOcupacional = new cAuditoriasReglasGrupoSubGrupoOcupacional($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasReglasGrupoSubGrupoOcupacional->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		$datosmodif['IdReglasGrupoSubGrupoOcupacional'] = $datos['IdReglasGrupoSubGrupoOcupacional'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function ModificarEstado($datos)
	{
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}



	public function Activar($datos)
	{
		$datosmodif['IdReglasGrupoSubGrupoOcupacional'] = $datos['IdReglasGrupoSubGrupoOcupacional'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdReglasGrupoSubGrupoOcupacional'] = $datos['IdReglasGrupoSubGrupoOcupacional'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
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



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['CodigoRegimenEstatutario']) || $datos['CodigoRegimenEstatutario']=="")
			$datos['CodigoRegimenEstatutario']="NULL";

		if (!isset($datos['CodigoRevista']) || $datos['CodigoRevista']=="")
			$datos['CodigoRevista']="NULL";

		if (!isset($datos['CodigoGrupo']) || $datos['CodigoGrupo']=="")
			$datos['CodigoGrupo']="NULL";

		if (!isset($datos['CodigoSubGrupo']) || $datos['CodigoSubGrupo']=="")
			$datos['CodigoSubGrupo']="NULL";

		if (!isset($datos['CategoriaDesde']) || $datos['CategoriaDesde']=="")
			$datos['CategoriaDesde']="NULL";

		if (!isset($datos['CategoriaHasta']) || $datos['CategoriaHasta']=="")
			$datos['CategoriaHasta']="NULL";

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


		if (!isset($datos['CodigoRegimenEstatutario']) || $datos['CodigoRegimenEstatutario']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un CodigoRegimenEstatutario",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['CodigoRevista']) || $datos['CodigoRevista']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un CodigoRevista",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['CodigoGrupo']) || $datos['CodigoGrupo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un CodigoGrupo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		/*if (!isset($datos['CodigoSubGrupo']) || $datos['CodigoSubGrupo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un CodigoSubGrupo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		
		
		if ((isset($datos['CategoriaDesde']) && $datos['CategoriaDesde']!="") || (isset($datos['CategoriaHasta']) && $datos['CategoriaHasta']!="") )
		{
			if (!isset($datos['CategoriaDesde']) || $datos['CategoriaDesde']=="")
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una Categoria Desde",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}

			if (!isset($datos['CategoriaHasta']) || $datos['CategoriaHasta']=="")
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una Categoria Hasta",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			
		}

		
		return true;
	}





}
?>