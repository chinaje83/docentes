<?php 
include(DIR_CLASES_DB."cLicenciasEncuadreAdministrativas.db.php");


class cLicenciasEncuadreAdministrativas extends cLicenciasEncuadreAdministrativasdb
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
			'xIdEncuadre'=> 0,
			'IdEncuadre'=> "",

			'xCategoria'=> 0,
			'Categoria'=> "",

			'xTipo'=> 0,
			'Tipo'=> "",

			'xTope'=> 0,
			'Tope'=> "",

			'xEncuadre'=> 0,
			'Encuadre'=> "",

			'xRegimenCodigo'=> 0,
			'RegimenCodigo'=> "",

            'xEstado'=> 0,
            'Estado'=> "-1",

			'limit'=> '',
			'orderby'=> "IdEncuadre DESC"
		);

		if(isset($datos['IdEncuadre']) && $datos['IdEncuadre']!="")
		{
			$sparam['IdEncuadre']= $datos['IdEncuadre'];
			$sparam['xIdEncuadre']= 1;
		}
		if(isset($datos['Categoria']) && $datos['Categoria']!="")
		{
			$sparam['Categoria']= $datos['Categoria'];
			$sparam['xCategoria']= 1;
		}


		if(isset($datos['Tipo']) && $datos['Tipo']!="")
		{
			$sparam['Tipo']= $datos['Tipo'];
			$sparam['xTipo']= 1;
		}

		if(isset($datos['Tope']) && $datos['Tope']!="")
		{
			$sparam['Tope']= $datos['Tope'];
			$sparam['xTope']= 1;
		}

		if(isset($datos['Encuadre']) && $datos['Encuadre']!="")
		{
			$sparam['Encuadre']= $datos['Encuadre'];
			$sparam['xEncuadre']= 1;
		}

		if(isset($datos['RegimenCodigo']) && $datos['RegimenCodigo']!="")
		{
			$sparam['RegimenCodigo']= $datos['RegimenCodigo'];
			$sparam['xRegimenCodigo']= 1;
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

		$oAuditoriasLicenciasEncuadreAdm = new cAuditoriasLicenciasEncuadreAdm($this->conexion,$this->formato);
		$datos['IdEncuadre'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasLicenciasEncuadreAdm->InsertarLog($datos,$codigoInsertadolog))
			return false;
		
		if (!$this->Publicar($datos))
			return false;

		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['Estado'] = ACTIVO;

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		$oAuditoriasLicenciasEncuadreAdm = new cAuditoriasLicenciasEncuadreAdm($this->conexion,$this->formato);
		$datos['Accion'] = MODIFICACION;

		if(!$oAuditoriasLicenciasEncuadreAdm->InsertarLog($datos,$codigoInsertadolog))
			return false;

		if (!$this->Publicar($datos))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasLicenciasEncuadreAdm = new cAuditoriasLicenciasEncuadreAdm($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasLicenciasEncuadreAdm->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		$datosmodif['IdEncuadre'] = $datos['IdEncuadre'];
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
		$datosmodif['IdEncuadre'] = $datos['IdEncuadre'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdEncuadre'] = $datos['IdEncuadre'];
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
		$nombrearchivo = "licencias_encuadre_adm";
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
				$array[$fila['IdEncuadre']] = $fila;
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un cÃ³digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un cÃ³digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['Categoria']) || $datos['Categoria']=="")
			$datos['Categoria']="NULL";


		if (!isset($datos['Detalle']) || $datos['Detalle']=="")
			$datos['Detalle']="NULL";

		if (!isset($datos['Tipo']) || $datos['Tipo']=="")
			$datos['Tipo']="NULL";

		if (!isset($datos['Grupo']) || $datos['Grupo']=="")
			$datos['Grupo']="NULL";

        if (!isset($datos['Tope']) || $datos['Tope']=="")
            $datos['Tope']="NULL";

		if (!isset($datos['LeyendaEstatuto']) || $datos['LeyendaEstatuto']=="")
			$datos['LeyendaEstatuto']="NULL";

		if (!isset($datos['LeyendaDecreto']) || $datos['LeyendaDecreto']=="")
			$datos['LeyendaDecreto']="NULL";

		if (!isset($datos['Encuadre']) || $datos['Encuadre']=="")
			$datos['Encuadre']="NULL";

		if (!isset($datos['RegimenCodigo']) || $datos['RegimenCodigo']=="")
			$datos['RegimenCodigo']="NULL";

		if (!isset($datos['Estado']) || $datos['Estado']=="")
			$datos['Estado']="NULL";

		if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario']=="")
			$datos['AltaUsuario']="NULL";

		if (!isset($datos['UltModificacionUsuario']) || $datos['UltModificacionUsuario']=="")
			$datos['UltModificacionUsuario']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{

		if (isset($datos['Tope']) && $datos['Tope']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Tope'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error el valor de tope debe ser un campo numerico",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['LeyendaEstatuto']) || $datos['LeyendaEstatuto']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una descripción de Leyenda Estatuto",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
	

		if (!isset($datos['Encuadre']) || $datos['Encuadre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una descripción de Encuadre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		
		return true;
	}




}
?>