<?php 
include(DIR_CLASES_DB."cDocumentosTiposZonas.db.php");

class cDocumentosTiposZonas extends cDocumentosTiposZonasdb
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


	public function BuscarxZonaTipoDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxZonaTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarxIdZona($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdZona($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function BuscarxIdRegistroTipoDocumentoxIdCampo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdRegistroTipoDocumentoxIdCampo($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarxIdRegistroTipoDocumentoxIdDocumentoAdjunto($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdRegistroTipoDocumentoxIdDocumentoAdjunto($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function InsertarModuloEnZona($datos,&$codigoinsertado,&$CampoEditable)
	{
		if (isset($datos['Tipo']) && ($datos['Tipo']=="campos" || $datos['Tipo']=="modulos" || $datos['Tipo']=="adjuntos"))
		{
			
			switch($datos['Tipo'])
			{
				case "campos":
					$oEstructuraCampos = new cEstructuraCampos($this->conexion,$this->formato);
					if (!$oEstructuraCampos->BuscarxCodigo($datos,$resultado,$numfilas))
						return false;
					if ($numfilas!=1)
					{
						FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, modulo inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
						return false;
					}
					$datosModulo = $this->conexion->ObtenerSiguienteRegistro($resultado);
					$CampoEditable = $datosModulo['TipoCampoEditable'];	
				break;	
				case "modulos":
					$datosBuscar['IdModulo']=$datos['IdCampo'];
					$oFormulariosModulos = new cFormulariosModulos($this->conexion,$this->formato);
					if (!$oFormulariosModulos->BuscarxCodigo($datosBuscar,$resultado,$numfilas))
						return false;
					if ($numfilas!=1)
					{
						FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, modulo inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
						return false;
					}
					$datosModulo = $this->conexion->ObtenerSiguienteRegistro($resultado);
					$CampoEditable = $datosModulo['ModuloEditable'];	
				break;
				case "adjuntos":
					$datosBuscar['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
					$datosBuscar['IdDocumentoAdjunto']=$datos['IdCampo'];
					$oDocumentosTiposDocumentacionAdjunta = new cDocumentosTiposDocumentacionAdjunta($this->conexion,$this->formato);
					if (!$oDocumentosTiposDocumentacionAdjunta->BuscarxIdRegistroTipoDocumentoxIdDocumentoAdjunto($datosBuscar,$resultado,$numfilas))
						return false;
					
					if ($numfilas!=1)
					{
						FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, archivo adjunto inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
						return false;
					}
					$datosModulo = $this->conexion->ObtenerSiguienteRegistro($resultado);
					$CampoEditable = true;	
				break;
			}
			$datosBusqueda['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
			$datosBusqueda['IdZona'] = $datos['IdZona'];
			if(!$this->BuscarxZonaTipoDocumento($datosBusqueda,$resultadoModulos,$numfilasModulos))
				return false;
				
			$datosmodif['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
			$datosmodif['IdZona'] = $datos['IdZona'];
			$datosmodif['Orden'] = 1;
			
			while($filaDatos = $this->conexion->ObtenerSiguienteRegistro($resultadoModulos))
			{
				if ($datosmodif['Orden']==$datos['orden'])
					$datosmodif['Orden']++;
	
				$datosmodif['IdZonaModulo'] = $filaDatos['IdZonaModulo'];
				if (!parent::ModificarOrden ($datosmodif))
					return false;
				$datosmodif['Orden']++;
			}
		

			$datosinsertar['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
			$datosinsertar['IdZona'] = $datos['IdZona'];
			$datosinsertar['Orden'] = $datos['orden'];
			$datosinsertar['DataJson']=json_encode(array());
			
			switch($datos['Tipo'])
			{
				case "campos":
					$datosinsertar['IdCampo']=$datos['IdCampo'];
					if ($datosinsertar['IdCampo']!="")
					{
						if(!$this->BuscarxIdRegistroTipoDocumentoxIdCampo($datosinsertar,$resultado,$numfilas))
							return false;
						if ($numfilas>0)
						{
							FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo ya se encuentra asociado al documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
							return false;
						}	
					}
				break;
				
				case "modulos":
					$datosinsertar['IdModulo']=$datos['IdCampo'];
				break;
				
				case "adjuntos":
					$datosinsertar['IdDocumentoAdjunto']=$datos['IdCampo'];
				break;
				
			}
			if (!$this->Insertar($datosinsertar,$codigoinsertado))
				return false;
	
		}

		return true;			
	}



	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		$this->ObtenerProximoOrden($datos,$proxorden);
		$datos['AltaFecha'] = $datos['UltimaModificacionFecha'] =date("Y-m-d H:i:s");
		$datos['AltaUsuario'] = $datos['UltimaModificacionUsuario']= $_SESSION['usuariocod'];
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		$oAuditoriasDocumentosTiposZonas = new cAuditoriasDocumentosTiposZonas($this->conexion,$this->formato);
		$datos['IdZonaModulo'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasDocumentosTiposZonas->InsertarLog($datos,$codigoInsertadolog))
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

		/*
		$oAuditoriasDocumentosTiposZonas = new cAuditoriasDocumentosTiposZonas($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposZonas->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		*/
		return true;
	}

	public function ModificarDatosConfiguracion($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		if ((!isset($datos['CampoObligatorio'])) || ($datos['CampoObligatorio']!=1 && $datos['CampoObligatorio']!=0))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo obligatorio.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::ModificarDatosConfiguracion($datos))
			return false;

		/*
		$oAuditoriasDocumentosTiposZonas = new cAuditoriasDocumentosTiposZonas($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposZonas->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		*/
		return true;
	}





	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasDocumentosTiposZonas = new cAuditoriasDocumentosTiposZonas($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosTiposZonas->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		if (!parent::Eliminar($datos))
			return false;

		return true;
	}
	
	
	
	public function EliminarxIdMacroPosicion($datos)
	{
		/*if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasDocumentosTiposZonas = new cAuditoriasDocumentosTiposZonas($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosTiposZonas->InsertarLog($datosLog,$codigoInsertadolog))
			return false;*/

		if (!parent::EliminarxIdMacroPosicion($datos))
			return false;

		return true;
	}




	public function ModificarOrdenCompleto($datos)
	{
		$datosmodif['Orden'] = 1;
		$datosmodif['IdZona'] = $datos['IdZona'];
		if (isset($datos['module']) && is_array($datos['module']))
		{
			foreach ($datos['module'] as $IdZonaModulo){
				$datosmodif['IdZonaModulo'] = $IdZonaModulo;
				if (!parent::ModificarOrden($datosmodif))
					return false;
				$datosmodif['Orden']++;
			}
		}
		return true;
	}



	private function ObtenerProximoOrden($datos,&$proxorden)
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



		if (!isset($datos['IdCampo']) || $datos['IdCampo']=="")
			$datos['IdCampo']="NULL";

		if (!isset($datos['IdModulo']) || $datos['IdModulo']=="")
			$datos['IdModulo']="NULL";

		if (!isset($datos['IdDocumentoAdjunto']) || $datos['IdDocumentoAdjunto']=="")
			$datos['IdDocumentoAdjunto']="NULL";

		if (!isset($datos['DataJson']) || $datos['DataJson']=="")
			$datos['DataJson']="NULL";


		if (!isset($datos['CampoObligatorio']) || $datos['CampoObligatorio']=="")
			$datos['CampoObligatorio']=0;

		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id del tipo de documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRegistroTipoDocumento'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico (Id Tipo de documento).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdZona']) || $datos['IdZona']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una zona",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdZona'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico (Id Zona).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		
		if (isset($datos['IdCampo']) && $datos['IdCampo']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdCampo'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico (Id Campo).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (isset($datos['IdModulo']) && $datos['IdModulo']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdModulo'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico (Id Modulo).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}


		if (isset($datos['IdDocumentoAdjunto']) && $datos['IdDocumentoAdjunto']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdDocumentoAdjunto'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico (Id Adjunto).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		
		
		if ( (!isset($datos['IdCampo']) || $datos['IdCampo']=="") &&  (!isset($datos['IdModulo']) || $datos['IdModulo']=="") &&  (!isset($datos['IdDocumentoAdjunto']) || $datos['IdDocumentoAdjunto']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un Id Modulo, un Id del Campo o un Id de un Archivo Adjunto.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		

		return true;
	}





}
?>