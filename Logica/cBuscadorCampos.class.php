<?php 
include(DIR_CLASES_DB."cBuscadorCampos.db.php");

class cBuscadorCampos extends cBuscadorCamposdb
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

	public function BuscarxIdBuscador($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdBuscador($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdBuscador'=> 0,
			'IdBuscador'=> "",
			'xIdCampo'=> 0,
			'IdCampo'=> "",
			'xEnBuscador'=> 0,
			'EnBuscador'=> "",
			'xEnListado'=> 0,
			'EnListado'=> "",
			'limit'=> '',
			'orderby'=> "Orden ASC"
		);
		
		if(isset($datos['IdBuscador']) && $datos['IdBuscador']!="")
		{
			$sparam['IdBuscador']= $datos['IdBuscador'];
			$sparam['xIdBuscador']= 1;
		}
		
		if(isset($datos['IdCampo']) && $datos['IdCampo']!="")
		{
			$sparam['IdCampo']= $datos['IdCampo'];
			$sparam['xIdCampo']= 1;
		}
		
		if(isset($datos['EnBuscador']) && $datos['EnBuscador']!="")
		{
			$sparam['EnBuscador']= $datos['EnBuscador'];
			$sparam['xEnBuscador']= 1;
		}
		
		if(isset($datos['EnListado']) && $datos['EnListado']!="")
		{
			$sparam['EnListado']= $datos['EnListado'];
			$sparam['xEnListado']= 1;
		}

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}

	
	
	public function Insertar($datos)
	{
		
		if (!$this->_ValidarInsertar($datos))
			return false;
		$this->ObtenerProximoOrden($datos,$proxorden);	
		$datos['Orden']= $proxorden;
		
		$this->_SetearNull($datos);
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datos['AltaFecha'] = date("Y-m-d H:i:s");
		if (!parent::Insertar($datos))
			return false;
			
			
		if(!$this->GenerarHtmlBuscador($datos))	
			return false;
			
		$oAuditoriasBuscadorCampos = new cAuditoriasBuscadorCampos($this->conexion,$this->formato);
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasBuscadorCampos->InsertarLog($datos,$codigoInsertadolog))
			return false;
			
			

		return true;
	}
	
	
	public function Eliminar($datos)
	{
		
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
			
		$oAuditoriasBuscadorCampos = new cAuditoriasBuscadorCampos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasBuscadorCampos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;	
			

		if (!parent::Eliminar($datos))
			return false;
			
		if(!$this->GenerarHtmlBuscador($datos))	
			return false;		
			

		return true;
	}
	
	
	public function EliminarxIdBuscador($datos)
	{
		if(!$this->BuscarxIdBuscador($datos,$resultado,$numfilas))
			return false;

		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			if (!$this->Eliminar($fila))
				return false;
		}
		return true;
	}
	
	
	public function ModificarDataJson($datos)
	{
		
		if (!$this->_ValidarModificarDataJson($datos,$datosRegistro))
			return false;
		$datos['DataJson'] = json_encode($datos);
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		if (!parent::ModificarDataJson($datos))
			return false;
			
		if(!$this->GenerarHtmlBuscador($datos))	
			return false;			

		$oAuditoriasBuscadorCampos = new cAuditoriasBuscadorCampos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasBuscadorCampos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;		
			

		return true;
	}
	
	
	
	
	
//----------------------------------------------------------------------------------------- 
//Retorna true o false si pudo cambiar el orden de las categorias

// Parámetros de Entrada:
//		catorden = orden de las categorias.

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no		
	public function ModificarOrdenCompleto($datos)
	{
				
		$datosmodif['Orden'] = 1;
		$datosmodif['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datosmodif['UltimaModificacionFecha']=  date("Y-m-d H:i:s");
		$datosmodif['IdBuscador']=  $datos['IdBuscador'];
		
		
		if (isset($datos['IdCampo']) && count($datos['IdCampo'])>0)
		{
			foreach ($datos['IdCampo'] as $key=>$IdCampo)
			{
				
				$datosmodif['IdCampo'] = $IdCampo;
				if (!$this->ModificarOrden($datosmodif))
						return false;
				$datosmodif['Orden']++;
			}
		}
		
		
		return true;
	}
	
	public function ModificarOrden($datos)
	{
		$datosmodif['Orden'] = $datos['Orden'];
		$datosmodif['IdBuscador'] = $datos['IdBuscador'];
		$datosmodif['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datosmodif['UltimaModificacionFecha']=  date("Y-m-d H:i:s");
		if (!parent::ModificarOrden($datosmodif))
			return false;
			
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);	
			
			
		/*$oAuditoriasBuscadorCampos = new cAuditoriasBuscadorCampos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasBuscadorCampos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;	*/
			
		
		return true;	
	}
	
	
	
	
	
	
	
//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

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

	
	
	
	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
			
		$datosbuscar['IdBuscador'] = $datos['IdBuscador'];
		$datosbuscar['IdCampo'] = $datos['IdCampo'];
		if(!$this->BusquedaAvanzada($datosbuscar,$resultado,$numfilas))
			return false;
		
		if($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe el campo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
			
		}	

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
	
	
	private function _ValidarModificarDataJson($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		
		if(!isset($datos['CantidadColumnas']) || $datos['CantidadColumnas']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una cantidad de columnas.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

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


		if (!isset($datos['IdBuscador']) || $datos['IdBuscador']=="")
			$datos['IdBuscador']="NULL";

		if (!isset($datos['IdCampo']) || $datos['IdCampo']=="")
			$datos['IdCampo']="NULL";
			
		
		if (!isset($datos['Orden']) || $datos['Orden']=="")
			$datos['Orden']="NULL";
			
		if (!isset($datos['DataJson']) || $datos['DataJson']=="")
			$datos['DataJson']="NULL";		
		
		if (!isset($datos['AltaFecha']) || $datos['AltaFecha']=="")
			$datos['AltaFecha']="NULL";

		if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario']=="")
			$datos['AltaUsuario']="NULL";

		if (!isset($datos['AltaApp']) || $datos['AltaApp']=="")
			$datos['AltaApp']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";

		if (!isset($datos['UltimaModificacionApp']) || $datos['UltimaModificacionApp']=="")
			$datos['UltimaModificacionApp']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{

		if (!isset($datos['IdBuscador']) || $datos['IdBuscador']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un buscador",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdBuscador'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdCampo']) || $datos['IdCampo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un campo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		
		return true;
	}	
	
	
	public function GenerarHtmlBuscador($datos)
	{
		if(!$this->BuscarxIdBuscador($datos,$resultadoBusqueda,$numfilasBusqueda))
			return false;

		if ($numfilasBusqueda>0)
		{
			$this->previsualizar = true;
			$html='<div class="row">';
			$width = round(70/($numfilasBusqueda));
			$oEstructuraCamposValores = new cEstructuraCamposValores($this->conexion,$this->formato);
			while($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoBusqueda))
			{
				$fila['CampoObligatorio']=0;
				if (trim($fila['DataJson'])!="")
				{
					$datosJson = json_decode($fila['DataJson'],1);
					$datosJson = FuncionesPHPLocal::ConvertiraUtf8($datosJson);
				}
				//$fila = FuncionesPHPLocal::DecodificarUtf8($fila);
				if (!isset($datosJson['CantidadColumnas']) || (!is_numeric($datosJson['CantidadColumnas'])) || ($datosJson['CantidadColumnas']<1 || $datosJson['CantidadColumnas']>12))
					$datosJson['CantidadColumnas'] = 4;
					
				$fila['IdZonaModulo']="";
				$fila['CampoEditable'] = false;
				$html.='<div class="col-md-'.$datosJson['CantidadColumnas'].'">';
					$html_generado = "";
					
					$this->CargarModulo($fila,$html_generado,true);
					$html .= $html_generado;
					
					
				$html.='</div>';
			}
			
			
			$html.='</div>';
			
			if(!is_dir(CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_FISICA."cliente_".$_SESSION['IdCliente']."/")){ 
				@mkdir(CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_FISICA."cliente_".$_SESSION['IdCliente']."/");
			}
			if(!is_dir(CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_FISICA."cliente_".$_SESSION['IdCliente']."/html/")){ 
				@mkdir(CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_FISICA."cliente_".$_SESSION['IdCliente']."/html/");
			}
			$file = CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_FISICA."cliente_".$_SESSION['IdCliente']."/html/buscador_general_".$datos['IdBuscador'].".html";
			if (!file_put_contents($file, $html, LOCK_EX) !== false)
				return false;
			
		}
		return true;
		//Subir imagenes
	}
	
	
	private function CargarModulo($datosModulo,&$html_generado,$enBuscador=false)
	{
		$carpeta = "estructuras/modulos";
		$Eliminar = 1;
		if ($datosModulo['IdCampo']!="")
		{
			$carpeta = "estructuras/campos";
			$Eliminar = 2;
		}
		if (isset($datosModulo['IdDocumentoAdjunto']) && $datosModulo['IdDocumentoAdjunto']!="")
		{
			$carpeta = "estructuras/archivos";
			$Eliminar = 3;
		}
		$datosModulo['htmleditfooter']="";
		$datosModulo['mouseaction'] = "";
		$datosModulo['htmledit'] = "";
		$datosModulo['conexion'] = $this->conexion;
		if (!$this->previsualizar)
		{
			$datosModulo['htmledit'] = "";
			FuncionesPHPLocal::ArmarLinkMD5("doc_documentos_tipos_confeccionar_campo_avanzado.php",array("IdZonaModulo"=>$datosModulo['IdZonaModulo']),$get,$md5);
			if ($datosModulo['IdCampo']!="")
			{
				//$datosModulo['htmledit'] .= '<div class="modules_header_advance">';
				//$datosModulo['htmledit'] .='<div class="btn-advance btn-xs btn btn-primary" data-id="'.$datosModulo['IdZonaModulo'].'" data-md5="'.$md5.'"><i class="fas fa-cog"></i>&nbsp;Avanzado</div>';
				//$datosModulo['htmledit'] .= '</div>';
			}
			$datosModulo['htmledit'] .= '<div class="modules_header" id="tools_'.$datosModulo['IdZonaModulo'].'">';
			$datosModulo['htmledit'] .='<div class="moverModulo btn-xs btn btn-primary"><i class="fas fa-expand-arrows-alt"></i></div>';
			if ($datosModulo['IdCampo']!="")
				$datosModulo['htmledit'] .= '	<div class="modules_edit"><a href="javascript:void(0)" data-href="'.$datosModulo['IdZonaModulo'].'" data-id="'.$datosModulo['IdCampo'].'" class="btn btn-info btn-xs editarCampo" title="Editar"><i class="fa fa-th"></i>&nbsp;Editar campo</a></div>';
			if ($datosModulo['CampoEditable'])
				$datosModulo['htmledit'] .= '	<div class="modules_edit"><a href="javascript:void(0)" class="btn btn-info btn-xs" onclick="AbrirEditarModulos('.$datosModulo['IdZonaModulo'].')" title="Editar"><i class="fa fa-edit"></i>&nbsp;Editar</a></div>';
			$datosModulo['htmledit'] .= '	<div class="modules_delete"><a href="javascript:void(0)" class="btn btn-danger btn-xs"  onclick="EliminarModulo('.$datosModulo['IdZonaModulo'].','.$Eliminar.')" title="Eliminar"><i class="fa fa-times"></i>&nbsp;Eliminar</a></div>';		
			$datosModulo['htmledit'] .= '</div><div style="clear:both"></div>';
			$datosModulo['mouseaction'] = "";
			
			if ($datosModulo['IdCampo']!="")
			{
				$campoObligatorio=$datosModulo['CampoObligatorio'];
				$classUno = ($campoObligatorio==1)?"active":"";	
				$classDos = ($campoObligatorio==0)?"active":"";	
				$checkUno = ($campoObligatorio==1)?'checked="checked"':'';	
				$checkDos = ($campoObligatorio==0)?'checked="checked"':'';	
				$datosModulo['htmleditfooter'] .= '<div class="footerAdvance"><div class="data_onoff"><label class="title_onoff">Obligatorio</label><div class="btn-group" id="status" data-toggle="buttons">
													  <label data-id="'.$datosModulo['IdZonaModulo'].'" data-check="1" class="btn btn-default btn-on btn-xs '.$classUno.'">
													  <input type="radio" class="radio" value="1" name="CampoObligatorio_'.$datosModulo['IdZonaModulo'].'" '.$checkUno.'>SI</label>
													  <label data-id="'.$datosModulo['IdZonaModulo'].'" data-check="0" class="btn btn-default btn-off btn-xs '.$classDos.'">
													  <input type="radio" class="radio" value="0" name="CampoObligatorio_'.$datosModulo['IdZonaModulo'].'" '.$checkDos.'>NO</label>
													</div><div style="clear:both"></div></div></div>';
			}
		}
		
		$datosModulo['Valores'] = false;
		if ($datosModulo['TieneValores']==1)
		{
			$oEstructuraCamposValores = new cEstructuraCamposValores($this->conexion,$this->formato);
			if(!$oEstructuraCamposValores->BuscarxIdCampo($datosModulo,$resultadoCampos,$numfilas))
				return false;
				
			while($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoCampos))
			{
				$datosModulo['Valores'][] = $fila;	
			}

		}
		$datosModulo['enBuscador'] = $enBuscador;
		$htmlModuleRender = FuncionesPHPLocal::RenderFile($carpeta."/html/".$datosModulo['NombreArchivo'],$datosModulo);
		$html = $this->ProcesarHtmlInterno($htmlModuleRender);
		$html_generado .= $html;
		
		
		return true;
	}


	
	private function ProcesarHtmlInterno($htmlModuleRender)
	{
		$html = "";
		cSepararHTML::ProcesarHTML($htmlModuleRender,$partes);
		foreach($partes as $partehtml)
		{
			if(is_array($partehtml))
			{
			}else
				$html .= $partehtml;
		}

		return $html;
	}


}
?>