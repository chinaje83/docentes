<?php
include(DIR_CLASES_DB."cDocumentosTipos.db.php");

class cDocumentosTipos extends cDocumentosTiposdb
{
	/** @var accesoBDLocal */
	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	protected function relacionTipsCategoriasSP(&$spnombre, &$sparam) {
		return parent::relacionTipsCategoriasSP($spnombre, $sparam);
	}

	protected function cantidadEscuelasSP(&$spnombre, &$sparam) {
		return parent::cantidadEscuelasSP($spnombre, $sparam);
	}



	public function relacionTipsCategoriasSPResult(&$resultado,&$numfilas)
	{
		if (!$this->relacionTipsCategoriasSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function cantidadEscuelasSPResult(&$resultado,&$numfilas)
	{
		if (!$this->cantidadEscuelasSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarAreasEstadosxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarAreasEstadosxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BuscarxIdTipoDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarCamposxIdRegistroTipoDocumentoValidacionDatos($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarCamposxIdRegistroTipoDocumentoValidacionDatos($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarTiposDocumentosxIdxIdAreaVigente($datos,&$resultado,&$numfilas)
	{
		if (!isset($datos['Anio']) && $datos['Anio']!="" && is_numeric($datos['Anio']))
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) && $datos['Mes']!="" && is_numeric($datos['Mes']))
			$datos['Mes'] = date("m");

		$datos['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";
		if (!parent::BuscarTiposDocumentosxIdxIdAreaVigente($datos,$resultado,$numfilas))
			return false;
		return true;
	}




	public function BuscarxIdCircuito($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdCircuito($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarCamposRelacionadosxIdRegistroTipoDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarCamposRelacionadosxIdRegistroTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}




	public function BuscarxIdTipoDocumentoVigente($datos,&$resultado,&$numfilas)
	{
		if (!isset($datos['Anio']) || $datos['Anio']=="" || !is_numeric($datos['Anio']))
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) || $datos['Mes']=="" || !is_numeric($datos['Mes']))
			$datos['Mes'] = date("m");

		$datos['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";
		if (!parent::BuscarxIdTipoDocumentoVigente($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdNivel'=> 0,
			'IdNivel'=> '',
			'xEstadoFiltro'=> 0,
			'EstadoFiltro'=> '',
			'xNombre'=> 0,
			'Nombre'=> '',
			'xNombreCorto'=> 0,
			'NombreCorto'=> '',
			'xIdClasificacion'=> 0,
			'IdClasificacion'=> '',
            'xIdCategoria'=> 0,
            'IdCategoria'=> '',
			'limit'=> '',
			'orderby'=> "IdTipoDocumento DESC"
		);

		if (!isset($datos['Anio']) || $datos['Anio']!="" || !is_numeric($datos['Anio']))
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) || $datos['Mes']!="" || !is_numeric($datos['Mes']))
			$datos['Mes'] = date("m");

		$sparam['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";

		if(isset($datos['IdNivel']) && $datos['IdNivel']!="")
		{
			$sparam['IdNivel']= $datos['IdNivel'];
			$sparam['xIdNivel']= 1;
		}

		if(isset($datos['EstadoFiltro']) && $datos['EstadoFiltro']!="")
		{
			$sparam['EstadoFiltro']= $datos['EstadoFiltro'];
			$sparam['xEstadoFiltro']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['NombreCorto']) && $datos['NombreCorto']!="")
		{
			$sparam['NombreCorto']= $datos['NombreCorto'];
			$sparam['xNombreCorto']= 1;
		}
		if(isset($datos['IdClasificacion']) && $datos['IdClasificacion']!="")
		{
			$sparam['IdClasificacion']= $datos['IdClasificacion'];
			$sparam['xIdClasificacion']= 1;
        }
        if(isset($datos['IdCategoria']) && $datos['IdCategoria']!="")
        {
            $sparam['IdCategoria']= $datos['IdCategoria'];
            $sparam['xIdCategoria']= 1;
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

	public function BuscarValidacionVigencia($datos,&$resultado,&$numfilas)
	{
		if (!isset($datos['IdRegistro']))
			$datos['IdRegistro']="";
		$datos['xIdRegistro'] = 0;
		if (isset($datos['IdRegistro']) && $datos['IdRegistro']!="")
			$datos['xIdRegistro'] = 1;
		if (!isset($datos['VigenciaHasta']) || $datos['VigenciaHasta']=="")
			$datos['VigenciaHasta'] = "NULL";

		if (!parent::BuscarValidacionVigencia($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BuscarTipoDocumentoxTipoDocumentoPadre($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'IdTipoDocumentoPadre'=> $datos['IdTipoDocumentoPadre'],
			'xEstado' => 0,
			'Estado' => "-1"
			);
			if (isset($datos['Estado']) && $datos['Estado']!="")
			{
				$sparam['Estado']= $datos['Estado'];
				$sparam['xEstado']= 1;
			}

		if (!parent::BuscarTipoDocumentoxTipoDocumentoPadre($sparam,$resultado,$numfilas))
			return false;

		return true;
	}


	public function BuscarTipoDocumentoxTipoDocumentoPadreVigente($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'IdTipoDocumentoPadre'=> $datos['IdTipoDocumentoPadre'],
			'xEstado' => 0,
			'Estado' => "-1"
			);
			if (isset($datos['Estado']) && $datos['Estado']!="")
			{
				$sparam['Estado']= $datos['Estado'];
				$sparam['xEstado']= 1;
			}
		if (!isset($datos['Anio']) && $datos['Anio']!="" && is_numeric($datos['Anio']))
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) && $datos['Mes']!="" && is_numeric($datos['Mes']))
			$datos['Mes'] = date("m");

		$sparam['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";
		if (!parent::BuscarTipoDocumentoxTipoDocumentoPadreVigente($sparam,$resultado,$numfilas))
			return false;

		return true;
	}


	public function BuscarTipoDocumentoRaizVigente($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xEstado' => 0,
			'Estado' => "-1"
			);
			if (isset($datos['Estado']) && $datos['Estado']!="")
			{
				$sparam['Estado']= $datos['Estado'];
				$sparam['xEstado']= 1;
			}
		if (!isset($datos['Anio']) && $datos['Anio']!="" && is_numeric($datos['Anio']))
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) && $datos['Mes']!="" && is_numeric($datos['Mes']))
			$datos['Mes'] = date("m");

		$sparam['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";

		if (!parent::BuscarTipoDocumentoRaizVigente($sparam,$resultado,$numfilas))
			return false;

		return true;
	}

	public function BuscarTiposDocumentoPofa($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarTiposDocumentoPofa($datos, $resultado, $numfilas))
            return false;
        return true;
    }

	//-----------------------------------------------------------------------------------------
// Retorna un array con todo el arbol dependiente del IdArea ingresado

// Parámetros de Entrada:
//		IdArea: raiz del arbol a retornar. Si vale "", entonces retorna el arbol completo de categorias

// Retorna:
//		arbol: array con el resultado de la consulta.
//					Además de la información del categoria, se agregan los subindices:
//						SubArbol: arbol con los categorias dependientes del categoria
//						Ruta: jerarquia ascendente desde el categoria actual hasta la raiz
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function ArmarArbolTipoDocumentoVigente($IdTipoDocumento,&$arbol,$Anio = "",$Mes = "",$string_estado_cat = "")
	{
		//traigo primero todos los hijos del categoria solicitado
		if ($Anio=="")
			$Anio = date("Y");
		if ($Mes=="")
			$Mes = date("m");

		$total=0;
		if(!$this->ArregloHijosVigente($IdTipoDocumento,$arbol,$total,$Anio,$Mes,$string_estado_cat))
			return false;

		//ordeno por nombre los categorias
/*		$arbol=FuncionesPHPLocal::array_column_sort($arbol,"Nombre");*/

		//recorro todos los categorias para asignar la Ruta y armar el SubArbol dependiente
		foreach($arbol as $indice => $datos)
		{
			$arbol[$indice]["SubArbol"]=array();

			if(!$this->MostrarArbolJerarquiaVigente($datos["IdTipoDocumento"],$jerarquia,$Anio,$Mes,$string_estado_cat))
				return false;
			$arbol[$indice]["Ruta"]=$jerarquia;

			//si tiene hijos entonces llamo a la funcion recursivamente para armar el SubArbol dependiente
			if($this->TieneHijosVigente($datos["IdTipoDocumento"],$ok,$Anio,$Mes,$string_estado_cat) && $ok)
			{
				if(!$this->ArmarArbolTipoDocumentoVigente($datos["IdTipoDocumento"],$arbol[$indice]["SubArbol"],$Anio,$Mes,$string_estado_cat))
					return false;
			}
		}

		return true;
	}

	//-----------------------------------------------------------------------------------------
// Retorna un arreglo con todos los padres de una categoria

// Parámetros de Entrada:
//		IdArea: IdArea a buscar
//		nivelarbol= Se inicializa en 0.

// Retorna:
//		arrIdArea: devuelve el arreglo con todos los padres del categoria
//		nivelarbol: Devuelve el nivel en que se encuentra el categoria.
//		la función retorna true o false si se pudo ejecutar con éxito o no


 	public function ArregloPadresVigente($IdTipoDocumento,&$arrcat,&$nivelarbol,$Anio = "",$Mes = "",$string_estado_cat = "")
	{
		if ($IdTipoDocumento!="")
		{
			$datoscat['IdTipoDocumento'] = $IdTipoDocumento;
			$datoscat['Estado'] = $string_estado_cat;
			$datoscat['Anio'] = $Anio;
			$datoscat['Mes'] = $Mes;
			if (!$this->BuscarxIdTipoDocumentoVigente($datoscat,$resultado,$numfilas))
				return false;
			$result=true;

			if ($numfilas==0)
				$result=false;


			if ($result)
			{
				while ($filasub=$this->conexion->ObtenerSiguienteRegistro($resultado))
				{
					$padre=$filasub['IdTipoDocumentoPadre'];
					$arrcat[]=$filasub;
				}
				$nivelarbol++;

				if ($padre!="")
					if (!$this->ArregloPadresVigente($padre,$arrcat,$nivelarbol,$Anio,$Mes,$string_estado_cat))
						return false;
				if(is_array($arrcat) && count($arrcat)>0 )
					$darvueltaarreglo=asort($arrcat);
			}
		}
		return true;
	}

//-----------------------------------------------------------------------------------------
// Retorna un arreglo con todos los hijos de un categoria

// Parámetros de Entrada:
//		IdArea: categoria a buscar
//		cantidadarreglo: Se inicializa en 0.

// Retorna:
//		arrcat: devuelve el arreglo con todos los hijos del categoria
//		errcat: el error en caso de que se produzca
//		cantidadarreglo: La cantidad total del arreglo.
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function ArregloHijosVigente($IdTipoDocumento,&$arrcat,&$cantidadarreglo,$Anio,$Mes,$string_estado_cat)
	{

		$arrcat = array();
		if ($IdTipoDocumento!="")
		{
			$datoscat['IdTipoDocumentoPadre'] = $IdTipoDocumento;
			$datoscat['Estado'] = $string_estado_cat;
			$datoscat['Anio'] = $Anio;
			$datoscat['Mes'] = $Mes;
			if (!$this->BuscarTipoDocumentoxTipoDocumentoPadreVigente($datoscat,$resultado,$numfilas))
				return false;

			$result=true;
			if ($numfilas==0)
				$result=false;

			if ($result)
			{
				while ($filasub=$this->conexion->ObtenerSiguienteRegistro($resultado))
				{
					$arrcat[$cantidadarreglo]=$filasub;
					$cantidadarreglo++;
				}
			}
		}
		else
		{
			$datoscat['Estado'] = $string_estado_cat;
			$datoscat['Anio'] = $Anio;
			$datoscat['Mes'] = $Mes;
			if (!$this->BuscarTipoDocumentoRaizVigente($datoscat,$resultado,$numfilas))
				return false;

			while ($filasub=$this->conexion->ObtenerSiguienteRegistro($resultado))
			{
				$arrcat[$cantidadarreglo]=$filasub;
				$cantidadarreglo++;
			}
		}

		return true;
	}

//-----------------------------------------------------------------------------------------
// Retorna un arreglo con todos los hijos de un categoria

// Parámetros de Entrada:
//		IdArea: categoria a buscar
//		cantidadarreglo: Se inicializa en 0.

// Retorna:
//		arrcat: devuelve el arreglo con todos los hijos del categoria
//		errcat: el error en caso de que se produzca
//		cantidadarreglo: La cantidad total del arreglo.
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function ArregloArbolHijosVigente($IdTipoDocumento,&$arrcat,&$cantidadarreglo,$Anio,$Mes,$string_estado_cat)
	{

		$arrcat = array();
		if ($IdTipoDocumento!="")
		{
			$datoscat['IdTipoDocumentoPadre'] = $IdTipoDocumento;
			$datoscat['Estado'] = $string_estado_cat;
			$datoscat['Anio'] = $Anio;
			$datoscat['Mes'] = $Mes;
			if (!$this->BuscarTipoDocumentoxTipoDocumentoPadreVigente($datoscat,$resultado,$numfilas))
				return false;

			$result=true;
			if ($numfilas==0)
				$result=false;

			if ($result)
			{
				while ($filasub=$this->conexion->ObtenerSiguienteRegistro($resultado))
				{
					$cant = 0;
					if(!$this->ArregloArbolHijosVigente($filasub['IdTipoDocumento'],$arrHijos,$cant,$Anio,$Mes,$string_estado_cat))
						return false;
					$filasub['Id'] = $filasub['IdTipoDocumento'];
					$cantidadarreglo++;
					$filasub['Hijos'] = $arrHijos;
					$arrcat[$cantidadarreglo]=$filasub;
				}
			}
		}

		return true;
	}



	public function ArregloCodigosHijosVigente($IdTipoDocumento,&$arregloCodigos,$Anio ="",$Mes="",$validarActivos = false)
	{

		if ($IdTipoDocumento!="")
		{

			$datoscat['IdTipoDocumentoPadre'] = $IdTipoDocumento;
			$datoscat['Anio'] = $Anio;
			$datoscat['Mes'] = $Mes;
			if (!$this->BuscarTipoDocumentoxTipoDocumentoPadreVigente($datoscat,$resultado,$numfilas))
				return false;


			while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
			{
				$agregar = true;
				if ($validarActivos && $fila['Estado']!=10)
					$agregar = false;

				if ($agregar)
				{
					$arregloCodigos[$fila["IdRegistro"]] = $fila["IdRegistro"];
					if(!$this->ArregloCodigosHijosVigente($fila["IdTipoDocumento"],$arregloCodigos,$Anio,$Mes,$validarActivos))
						return false;
				}
			}
		}
		return true;
	}



//-----------------------------------------------------------------------------------------
// Retorna un ok si tiene hijos

// Parámetros de Entrada:
//		IdArea: IdArea a buscar

// Retorna:
//		errcat: el error en caso de que se produzca
//		ok: devulve verdadero en caso de que tenga hijos, falso si no tiene.
//		la función retorna true o false si se pudo ejecutar con éxito o no


	public function TieneHijosVigente($IdTipoDocumento,&$ok,$Anio = "",$Mes = "",$string_estado_cat = "")
	{

		$datoscat['IdTipoDocumentoPadre'] = $IdTipoDocumento;
		$datoscat['Estado'] = $string_estado_cat;
		$datoscat['Anio'] = $Anio;
		$datoscat['Mes'] = $Mes;
		if (!$this->BuscarTipoDocumentoxTipoDocumentoPadreVigente($datoscat,$resultado,$numfilas))
		{
			$ok = false;
			return false;
		}

		$result=true;
		if ($result)
		{
			if ($filasub=$this->conexion->ObtenerSiguienteRegistro($resultado))
				$ok=true;
			else
				$ok=false;
		}
		return true;
	}


//-----------------------------------------------------------------------------------------
// Retorna la rama ascendente de un categoria con redirección

// Parámetros de Entrada:
//		IdArea: categoria a buscar

// Retorna:
//		jerarquia: un string con la Ruta (href)
//		errcat: el error en caso de que se produzca
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function MostrarJerarquiaVigente($IdTipoDocumento,&$jerarquia,&$nivel,$Anio = "",$Mes = "",$string_estado_cat = "")
	{
		$i=1;
		$jerarquia="";
		$nivel=0;
		$arrjerarquia = array();
		if(!$this->ArregloPadresVigente($IdTipoDocumento,$arrjerarquia,$nivel,$Anio,$Mes,$string_estado_cat))
			return false;


		if ($nivel!=0)
			$jerarquia.="<a href='exp_areas.php'>Inicio</a> &raquo; ";
		else
			$jerarquia.="<span class=\"bold\">Inicio</span>";

		foreach ($arrjerarquia as $clave=>$valor)
		{

			if ($i!=$nivel)
			{
				FuncionesPHPLocal::ArmarLinkMD5("exp_areas.php",array("IdArea"=>$valor['IdArea']),$get,$md5);
				$jerarquia.="<a href='exp_areas.php?IdTipoDocumentoPadre=";
				$jerarquia.= FuncionesPHPLocal::HtmlspecialcharsSistema($valor['IdArea'],ENT_QUOTES);
				$jerarquia.="&md5=";
				$jerarquia.= FuncionesPHPLocal::HtmlspecialcharsSistema($md5,ENT_QUOTES);
				$jerarquia.="' class='bold'>";
				$jerarquia.=$valor['Nombre']."</a> &raquo; ";
			}
			else
				$jerarquia.="<span class=\"bold\">".$valor['Nombre']."</span>";

			$i++;
		}
		$nivel=0;

		return true;
	}

//-----------------------------------------------------------------------------------------
// Retorna la rama ascendente de un categoria

// Parámetros de Entrada:
//		IdArea: categoria a buscar

// Retorna:
//		jerarquia: un string con la Ruta
//		errcat: el error en caso de que se produzca
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function MostrarArbolJerarquiaVigente($IdArea,&$jerarquia,$estilos=true,$Anio = "",$Mes = "",$string_estado_cat = "")
	{
		$arrjerarquia=array();
		if(!$this->ArregloPadresVigente($IdArea,$arrjerarquia,$nivel,$Anio,$Mes,$string_estado_cat))
			return false;

		$i=1;
		$jerarquia="";
		foreach ($arrjerarquia as $clave=>$valor)
		{
			if ($i!=$nivel)
				$jerarquia.= FuncionesPHPLocal::HtmlspecialcharsSistema($valor['Nombre'],ENT_QUOTES)." &raquo; ";
			else
			{

				if($estilos)
					$jerarquia.="<span class='negrita'>". FuncionesPHPLocal::HtmlspecialcharsSistema($valor['Nombre'],ENT_QUOTES)."</span>";
				else
					$jerarquia.= FuncionesPHPLocal::HtmlspecialcharsSistema($valor['Nombre'],ENT_QUOTES);
			}
			$i++;
		}
		$nivel=0;

		return true;
	}

	public function Insertar($datos,&$codigoinsertado,&$IdTipoDocumento)
	{
		if (!$this->ValidarConstante($datos))
			return false;

		if (!cUsuariosPermisos::TienePermiso("004001")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para insertar un tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarInsertar($datos))
			return false;

		$IdTipoDocumento = $datos['IdTipoDocumento'] = $this->ObtenerSiguienteIdTipoDocumento();
		$this->_SetearNull($datos);
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = $datos['AltaFecha'] =date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::InsertarDB($datos,$codigoinsertado))
			return false;


		$oCircuitos = new cCircuitos($this->conexion,$this->formato);

		$datosCircuito['Nombre'] = $datos['Nombre'];
		$datosCircuito['NombreCorto'] = $datos['Nombre'];
		$datosCircuito['IdCircuito'] = $codigoinsertado;

		if(!$oCircuitos->Insertar($datosCircuito))
			return false;


		$datosCircuito['IdRegistro'] = $codigoinsertado;
		$datosCircuito['IdCircuito'] = $codigoinsertado;
		$datosCircuito['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
		if(!parent::ModificarCircuitoDB($datosCircuito))
			return false;



		$oAuditoriasDocumentosTipos = new cAuditoriasDocumentosTipos($this->conexion,$this->formato);
		$datos['IdRegistro'] = $codigoinsertado;
		$datos['IdTipoDocumento'] = $IdTipoDocumento;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasDocumentosTipos->InsertarLog($datos,$codigoInsertadolog))
			return false;

        $datos['IdRegistro'] =$codigoinsertado;
        if (!$this->Publicar($datos))
            return false;

		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->ValidarConstante($datos))
			return false;

		if (!cUsuariosPermisos::TienePermiso("004002")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para modificar un tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);

		if ($datosRegistro['IdTipoDocumentoPadre']!=$datos['IdTipoDocumentoPadre'])
		{
			$oDocumentosTiposPadresEstados = new cDocumentosTiposPadresEstados($this->conexion,$this->formato);
			$datosPadresEstados['IdRegistroTipoDocumento'] = $datos['IdRegistro'];
			if(!$oDocumentosTiposPadresEstados->BusquedaAvanzada($datosPadresEstados,$resultadoPadresEstados,$numfilasPadresEstados))
				return false;

			if($numfilasPadresEstados>0)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, debe eliminar los Estados Personalizados.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			// VALIDAR QUE NO TENGA ESTADOS DEPENDIENTES
		}


		if ($datos['EstadoPadreDependiente']==1)
		{
			// ELIMINAR TODOS POR IDREGISTRO
			$oDocumentosTiposPadresEstados = new cDocumentosTiposPadresEstados($this->conexion,$this->formato);
			$datosPadresEstados['IdRegistroTipoDocumento'] = $datos['IdRegistro'];
			if(!$oDocumentosTiposPadresEstados->EliminarxIdRegistroTipoDocumento($datosPadresEstados))
				return false;

		}

		if (!parent::Modificar($datos))
			return false;


		$oCircuitos = new cCircuitos($this->conexion,$this->formato);
		$datosCircuito['IdCircuito'] = $datosRegistro['IdCircuito'];
		if(!$oCircuitos->BuscarxCodigo($datosCircuito,$resultadoCircuito,$numfilasCircuito))
			return false;

		$filaCircuito = $this->conexion->ObtenerSiguienteRegistro($resultadoCircuito);

		if($filaCircuito['Nombre']!=$datos['Nombre'])
		{
			$datosCircuito['Nombre'] = $datos['Nombre'];
			$datosCircuito['NombreCorto'] = $datos['Nombre'];
			if(!$oCircuitos->Modificar($datosCircuito))
				return false;
		}


		/*if($datos['TieneAdjunto']=="0")
		{
			$oDocumentosTiposDocumentacionAdjunta = new cDocumentosTiposDocumentacionAdjunta($this->conexion,$this->formato);
			$datos['IdRegistroTipoDocumento'] = $datos['IdRegistro'];
			if(!$oDocumentosTiposDocumentacionAdjunta->EliminarxIdRegistroTipoDocumento($datos))
				return false;
		}*/




		/*$datosBusqueda['IdRegistroTipoDocumento'] = $datos['IdRegistro'];
		$oAreasTiposDocumentos = new cAreasTiposDocumentos($this->conexion,$this->formato);
		if(!$oAreasTiposDocumentos->BuscarxIdRegistroTipoDocumento($datosBusqueda,$resultadoAreas,$numfilasAreas))
			return false;



		if($numfilasAreas>0)
		{

			while($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoAreas))
			{
					$datosBuscar['IdRegistroArea'] = $fila['IdRegistroArea'];
					$datosBuscar['IdRegistro'] = $fila['IdRegistroArea'];
					$datosBuscar['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
					$datosBuscar['UltimaModificacionUsuario'] = $fila['UltimaModificacionUsuario'];
					$datosBuscar['UltimaModificacionUsuarioNombre'] = $_SESSION['usuarionombre']." ".$_SESSION['usuarioapellido'];
					$oElastic = new cModifElastic(INDICEAREAS);
					$oAreasTiposDocumentos = new cAreasTiposDocumentos($this->conexion,$this->formato);
					$datosElastic = $oAreasTiposDocumentos->ArmarArrayElastic($datosBuscar);
					if ($datosElastic===false)
						return false;
					if(!$oElastic->Actualizar($datosBuscar,$datosElastic))
						return false;
			}
		}*/



		$oAuditoriasDocumentosTipos = new cAuditoriasDocumentosTipos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTipos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;

        if (!$this->Publicar($datos))
            return false;

		return true;
	}


	public function AgregarCamposJson($datos)
	{
		//print_r($datos);die;
		if (!cUsuariosPermisos::TienePermiso("004002")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para modificar un tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datos['IdRegistro'] = $datos['IdRegistroTipoDocumento'];


		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		$oDocumentosTiposModulos = new cDocumentosTiposModulos($this->conexion,$this->formato);
		$datosBuscar['IdRegistroTipoDocumento'] = $datosRegistro['IdRegistro'];
		if(!$oDocumentosTiposModulos->BuscarxIdRegistroTipoDocumento($datosBuscar,$resultadoModulos,$numfilasModulos))
			return false;
		$arrayinsertar = array();
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoModulos))
		{
			$arrayinsertar[$fila['NombreCte']]['visualiza'] = true;
			$arrayinsertar[$fila['NombreCte']]['obligatorio'] = false;
			if($fila['Obligatorio']==1)
				$arrayinsertar[$fila['NombreCte']]['obligatorio'] = true;
			$arrayinsertar[$fila['NombreCte']]['titulo'] = utf8_encode($fila['Titulo']);
			$arrayinsertar[$fila['NombreCte']]['descripcion'] = utf8_encode($fila['Descripcion']);
			$arrayinsertar[$fila['NombreCte']]['orden'] = utf8_encode($fila['Orden']);

		}


		if(count($arrayinsertar)>0)
			$datos['DatosCamposJson'] = json_encode($arrayinsertar);
		else
			$datos['DatosCamposJson'] = "NULL";

		if(!$this->ModificarDatosCamposJson($datos))
			return false;

		return true;
	}

	public function ModificarVisualizaCamposJson($datos)
	{

		if (!cUsuariosPermisos::TienePermiso("004002")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para modificar un tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		if(!$this->AgregarCamposJson($datos))
			return false;

		$datos['IdRegistro'] = $datos['IdRegistroTipoDocumento'];
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}



		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		$DatosCamposJson = json_decode($datosRegistro['DatosCamposJson'],true);



		$valor = false;
		if(isset($datos['valor']) && $datos['valor']==1)
			$valor = true;


		if(array_key_exists($datos['Campo'],$DatosCamposJson))
		{
			$DatosCamposJson[$datos['Campo']]['visualiza']= $valor;
		}

		$datos['DatosCamposJson'] = json_encode($DatosCamposJson);

		//print_r($datos);die;
		if(!$this->ModificarDatosCamposJson($datos))
			return false;

		return true;
	}


	public function ModificarObligatorioCamposJson($datos)
	{
		if (!cUsuariosPermisos::TienePermiso("004002")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para modificar un tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(!$this->AgregarCamposJson($datos))
			return false;

		$datos['IdRegistro'] = $datos['IdRegistroTipoDocumento'];
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		$arrayCampos = json_decode($datosRegistro['DatosCamposJson'],true);




		$valor = false;
		if(isset($datos['valor']) && $datos['valor']==1)
			$valor = true;


		if(array_key_exists($datos['Campo'],$arrayCampos))
		{
			$arrayCampos[$datos['Campo']]['obligatorio']= $valor;
		}

		$datos['DatosCamposJson'] = json_encode($arrayCampos);

		if(!$this->ModificarDatosCamposJson($datos))
			return false;

		return true;
	}


	public function EliminarCamposJson($datos)
	{
		if (!cUsuariosPermisos::TienePermiso("004002")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para modificar un tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datos['IdRegistro'] = $datos['IdRegistroTipoDocumento'];
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		$arrayCampos = json_decode($datosRegistro['DatosCamposJson'],true);



		if(array_key_exists($datos['Campo'],$arrayCampos))
		{
			unset($arrayCampos[$datos['Campo']]);
		}

		$datos['DatosCamposJson'] = json_encode($arrayCampos);

		if(!$this->ModificarDatosCamposJson($datos))
			return false;

		return true;
	}






	public function ModificarTituloCamposJson($datos)
	{
		if (!cUsuariosPermisos::TienePermiso("004002")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para modificar un tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(!$this->AgregarCamposJson($datos))
			return false;

		$datos['IdRegistro'] = $datos['IdRegistroTipoDocumento'];
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		$arrayCampos = json_decode($datosRegistro['DatosCamposJson'],true);




		if(array_key_exists($datos['Campo'],$arrayCampos))
		{
			$arrayCampos[$datos['Campo']]['titulo']= utf8_encode($datos['titulo']);
			$arrayCampos[$datos['Campo']]['descripcion']= utf8_encode($datos['descripcion']);


			if(!isset($datos['orden']) || $datos['orden']=="")
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, debe ingresar un orden.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;

			}


			if(isset($datos['orden']) && $datos['orden']!="" && !is_numeric($datos['orden']))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, el campo orden debe ser un número entero.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;

			}

			$arrayCampos[$datos['Campo']]['orden']= utf8_encode($datos['orden']);
		}

		$datos['DatosCamposJson'] = json_encode($arrayCampos);
		if(!$this->ModificarDatosCamposJson($datos))
			return false;

		return true;
	}



	public function ModificarEvento($datos)
	{
		if (!cUsuariosPermisos::TienePermiso("004002")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para modificar un tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!$this->_ValidarModificarEvento($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);

		if (!parent::ModificarEvento($datos))
			return false;


		$oAuditoriasDocumentosTipos = new cAuditoriasDocumentosTipos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTipos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;

		return true;
	}


	public function ModificarDatosCamposJson($datos)
	{
		if (!cUsuariosPermisos::TienePermiso("004002")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para modificar un tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!$this->_ValidarModificarEvento($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);

		if (!parent::ModificarDatosCamposJson($datos))
			return false;


		/*$oAuditoriasDocumentosTipos = new cAuditoriasDocumentosTipos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTipos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;*/

		return true;
	}


	public function AgregarNuevaVigencia($datos,&$codigoinsertado,&$IdTipoDocumento)
	{
		if (!cUsuariosPermisos::TienePermiso("004002")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para agregar una nueva vigencia al tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarAgregarNuevaVigencia($datos,$datosRegistro))
			return false;

		$this->_SetearNull($datos);
		$IdTipoDocumento = $datos['IdTipoDocumento'];
		$datos['Nombre'] = $datos['Nombre'];
		$datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['AltaUsuario'] = $_SESSION['usuariocod'];
		$datos['AltaFecha'] = date("Y/m/d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::InsertarDB($datos,$codigoinsertado))
			return false;


		$oCircuitos = new cCircuitos($this->conexion,$this->formato);

		$datosCircuito['Nombre'] = $datos['Nombre'];
		$datosCircuito['NombreCorto'] = $datos['Nombre'];
		$datosCircuito['IdCircuito'] = $codigoinsertado;
		if(!$oCircuitos->Insertar($datosCircuito))
			return false;


		$datosCircuito['IdRegistro'] = $codigoinsertado;
		$datosCircuito['IdCircuito'] = $codigoinsertado;
		$datosCircuito['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
		if(!parent::ModificarCircuitoDB($datosCircuito))
			return false;

		$oAuditoriasDocumentosTipos = new cAuditoriasDocumentosTipos($this->conexion,$this->formato);
		$datos['IdRegistro'] = $codigoinsertado;
		$datos['IdTipoDocumento'] = $datos['IdTipoDocumento'];
		$datos['Nombre'] = $datos['Nombre'];
		$datos['Accion'] = INSERTARNUEVAVIGENCIA;
		if(!$oAuditoriasDocumentosTipos->InsertarLog($datos,$codigoInsertadolog))
			return false;

		return true;
	}


	public function ModificarVigencia($datos)
	{
		if (!cUsuariosPermisos::TienePermiso("004002")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para modificar la vigencia un tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarModificarVigencia($datos,$datosRegistro))
			return false;

		$this->_SetearNull($datos);
		$datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

		if (!parent::ModificarVigencia($datos))
			return false;

		$oAuditoriasDocumentosTipos = new cAuditoriasDocumentosTipos($this->conexion,$this->formato);
		$datosLog = $datosRegistro;
		$datosLog['VigenciaDesde'] = $datos['VigenciaDesde'];
		$datosLog['VigenciaHasta'] = $datos['VigenciaHasta'];
		$datosLog['Accion'] = MODIFICARVIGENCIA;
		$datosLog['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
		$datosLog['UltimaModificacionUsuario'] = $datos['UltimaModificacionUsuario'];
		if(!$oAuditoriasDocumentosTipos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{

		if (!cUsuariosPermisos::TienePermiso("004003")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para eliminar el tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$IdTipoDocumento = $datosRegistro['IdTipoDocumento'];
		$Anio = $_SESSION['Anio'];
		$Mes = $_SESSION['Mes'];
		$arregloCodigos = array();
		if(!$this->ArregloCodigosHijosVigente($IdTipoDocumento,$arregloCodigos,$Anio,$Mes))
			return false;


		$datos['IdRegistroTipoDocumento'] = $datos['IdRegistro'];
		$oDocumentosTiposDependientes = new cDocumentosTiposDependientes($this->conexion,$this->formato);
		if(!$oDocumentosTiposDependientes->EliminarxIdRegistroTipoDocumento($datos))
			return false;
		/*
		$oDocumentosTiposAsociados = new cDocumentosTiposAsociados($this->conexion,$this->formato);
		if(!$oDocumentosTiposAsociados->EliminarxIdRegistroTipoDocumento($datos))
			return false;


		$oDocumentosTiposIndices = new cDocumentosTiposIndices($this->conexion,$this->formato);
		if(!$oDocumentosTiposIndices->EliminarxIdRegistroTipoDocumento($datos))
			return false;


		$oDocumentosTiposCamposBusqueda = new cDocumentosTiposCamposBusqueda($this->conexion,$this->formato);
		if(!$oDocumentosTiposCamposBusqueda->EliminarxIdRegistroTipoDocumento($datos))
			return false;	*/

		$datos['Estado'] = $datosRegistro['Estado'] = ELIMINADO;
		$oAuditoriasDocumentosTipos = new cAuditoriasDocumentosTipos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosTipos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;


		if (!$this->ModificarEstado($datos))
			return false;


		if(count($arregloCodigos) >0)
		{
			foreach($arregloCodigos as $IdRegistro)
			{
				$datos['IdRegistro'] = $IdRegistro;
				if (!$this->_ValidarEliminar($datos,$datosRegistro))
					return false;


				$datos['Estado'] = $datosRegistro['Estado'] = ELIMINADO;
				$oAuditoriasDocumentosTipos = new cAuditoriasDocumentosTipos($this->conexion,$this->formato);
				$datosLog =$datosRegistro;
				$datosLog['Accion'] = ELIMINAR;
				if(!$oAuditoriasDocumentosTipos->InsertarLog($datosLog,$codigoInsertadolog))
					return false;


				if (!$this->ModificarEstado($datos))
					return false;

			}


		}

        if (!$this->Publicar($datos))
            return false;

		return true;
	}



	public function ModificarEstado($datos)
	{
		$datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}



	public function Activar($datos)
	{
		$datosmodif['IdRegistro'] = $datos['IdRegistro'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;

        if (!$this->Publicar($datos))
            return false;

		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdRegistro'] = $datos['IdRegistro'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;

        if (!$this->Publicar($datos))
            return false;
		return true;
	}






//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function ObtenerSiguienteIdTipoDocumento()
	{
		$idSiguiente = 0;
		if (!parent::BuscarUltimoIdTipoDocumento($resultado,$numfilas))
			return false;

		if ($numfilas>0)
		{
			$datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
			$idSiguiente = $datos['UltimoIdTipoDocumento'];

		}
		$idSiguiente = $idSiguiente+1;

		return $idSiguiente;
	}



	private function _ValidarInsertar(&$datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		if (!$this->_ValidarVigencia($datos))
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

		if (!isset($datos['EstadoPadreDependiente']) || ($datos['EstadoPadreDependiente']!=="1" && $datos['EstadoPadreDependiente']!=="0"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe carga el documento en todos los estados o solo los personalizados.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	private function _ValidarModificarEvento($datos,&$datosRegistro)
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

		/*$datosBusqueda['IdTipoDocumentoPadre'] = $datosRegistro['IdTipoDocumento'];
		if(!$this->BuscarTipoDocumentoxTipoDocumentoPadre($datosBusqueda,$resultado_hijos,$numfilas_hijos))
			return false;

		if ($numfilas_hijos>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error tiene tipos de documento dependientes.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		unset($datosBusqueda);

		$DocumentosTiposMacros = new cDocumentosTiposMacros($this->conexion,$this->formato);
		$datosBusqueda['IdRegistroTipoDocumento'] = $datosRegistro['IdRegistro'];
		if(!$DocumentosTiposMacros->BuscarPasosMacros($datosBusqueda,$resultado,$numfilas))
			return false;

		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, tiene macros asociados a eliminar.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		$oDocumentosTiposCamposProgramables = new cDocumentosTiposCamposProgramables($this->conexion);
		$datosBusqueda['IdRegistroTipoDocumento'] = $datosRegistro['IdRegistro'];
		if(!$oDocumentosTiposCamposProgramables->BuscarxIdRegistroTipoDocumento($datosBusqueda,$resultModulosInsertados,$numfilasModulosInsertados))
			die();

		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, tiene campos programables asociados a eliminar.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		unset($datosBusqueda);

		$oDocumentos = new cElastic($this->conexion,$this->formato);
		$datosBusqueda['TipoDocumento'] = $datosRegistro['IdTipoDocumento'];
		if(!$oDocumentos->BuscarCantidadDocumentos($datosBusqueda,$result_cant))
			return false;

		if($result_cant['count'] > 0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, tiene documentos asociados a eliminar.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		return true;
	}


	private function _ValidarModificarVigencia(&$datos,&$datosRegistro)
	{

		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un codigo de tipo de documento valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarVigencia($datos))
			return false;


		$datosBusqueda['IdTipoDocumento'] = $datosRegistro['IdTipoDocumento'];
		$datosBusqueda['IdRegistro'] = $datosRegistro['IdRegistro'];
		$datosBusqueda['VigenciaDesde'] = $datos['VigenciaDesde'];
		$datosBusqueda['VigenciaHasta'] = $datos['VigenciaHasta'];
		if (!$this->BuscarValidacionVigencia($datosBusqueda,$resultado,$numfilas))
			return false;

		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe una vigencia del mismo tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		return true;
	}

	private function _ValidarAgregarNuevaVigencia(&$datos,&$datosRegistro)
	{
		if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tipo de documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTipoDocumento'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo de codigo Tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		if (!$this->BuscarxIdTipoDocumento($datos,$resultado,$numfilas))
			return false;

		if ($numfilas==0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un codigo de tipo de documento valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarVigencia($datos))
			return false;

		$datosBusqueda['IdTipoDocumento'] = $datosRegistro['IdTipoDocumento'];
		$datosBusqueda['VigenciaDesde'] = $datos['VigenciaDesde'];
		$datosBusqueda['VigenciaHasta'] = $datos['VigenciaHasta'];
		if (!$this->BuscarValidacionVigencia($datosBusqueda,$resultado,$numfilas))
			return false;

		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe una vigencia del mismo tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	private function _ValidarVigencia(&$datos)
	{

		if (!isset($datos['VigenciaDesde']) || $datos['VigenciaDesde']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una vigencia desde",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$datos['VigenciaDesde'] = "01/".$datos['VigenciaDesde'];
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['VigenciaDesde'],"FechaDDMMAAAA"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo fecha (Desde) valido (mm/aaaa).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datos['VigenciaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['VigenciaDesde'],"dd/mm/aaaa","aaaammdd");

		if ($datos['VigenciaHasta']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,"15/".$datos['VigenciaHasta'],"FechaDDMMAAAA"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo fecha (Hasta) valido (mm/aaaa).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}

			if (isset($datos['VigenciaHasta']) && $datos['VigenciaHasta']!="")
			{
				$dia = FuncionesPHPLocal::ObtenerUltimoDiaMes(substr($datos['VigenciaHasta'],3,4),substr($datos['VigenciaHasta'],0,2));
				$datos['VigenciaHasta'] = $dia."/".$datos['VigenciaHasta'];
			}

			$datos['VigenciaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['VigenciaHasta'],"dd/mm/aaaa","aaaammdd");
		}

		if (!isset($_SESSION['Anio']) || $_SESSION['Anio']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe seleccionar un año (barra periodos).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!isset($_SESSION['Mes']) || $_SESSION['Mes']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe seleccionar un mes (barra periodos).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$anioMesInicio = $_SESSION['Anio'].str_pad($_SESSION['Mes'],2,"0")."01";
		$anioMesFin = $_SESSION['Anio'].str_pad($_SESSION['Mes'],2,"0").FuncionesPHPLocal::ObtenerUltimoDiaMes($_SESSION['Anio'],$_SESSION['Mes']);

		if ($datos['VigenciaHasta']!="")
		{
			if ($datos['VigenciaDesde']>$datos['VigenciaHasta'])
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, la vigencia (desde) debe ser menor que la vigencia (Hasta).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if ($datos['VigenciaHasta']<$anioMesFin)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, la vigencia (hasta) debe encontrarse dentro del periodo seleccionado en la barra superior.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if ($datos['VigenciaDesde']>$anioMesInicio)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, la vigencia (desde) debe encontrarse dentro del periodo seleccionado en la barra superior.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		return true;
	}


	private function _SetearNull(&$datos)
	{

		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		if (!isset($datos['NombreCorto']) || $datos['NombreCorto']=="")
			$datos['NombreCorto']="NULL";


		if (!isset($datos['DescripcionCorta']) || $datos['DescripcionCorta']=="")
			$datos['DescripcionCorta']="NULL";

		if (!isset($datos['DescripcionLarga']) || $datos['DescripcionLarga']=="")
			$datos['DescripcionLarga']="NULL";

		if (!isset($datos['IdTipoDocumentoPadre']) || $datos['IdTipoDocumentoPadre']=="")
			$datos['IdTipoDocumentoPadre']="NULL";


		if (!isset($datos['IdCategoria']) || $datos['IdCategoria']=="")
			$datos['IdCategoria']="NULL";

        if (!isset($datos['IdNivel']) || $datos['IdNivel']=="")
			$datos['IdNivel']="NULL";

		if (!isset($datos['IdCircuito']) || $datos['IdCircuito']=="")
			$datos['IdCircuito']="NULL";

        if (!isset($datos['IdNivel']) || $datos['IdNivel']=="")
            $datos['IdNivel']="NULL";

		if (!isset($datos['TieneCircuito']) || $datos['TieneCircuito']!="1")
		{
			$datos['TieneCircuito']="0";
			$datos['IdCircuito']="NULL";
		}

		if (!isset($datos['TieneAdjunto']) || $datos['TieneAdjunto']!="1")
			$datos['TieneAdjunto']="0";

		if (!isset($datos['DocumentoProgramado']) || $datos['DocumentoProgramado']=="")
			$datos['DocumentoProgramado']="0";

		if (!isset($datos['CargaPopup']) || $datos['CargaPopup']=="")
			$datos['CargaPopup']="0";

		if (!isset($datos['Clase']) || $datos['Clase']=="")
			$datos['Clase']="NULL";

		if (!isset($datos['Metodo']) || $datos['Metodo']=="")
			$datos['Metodo']="NULL";

		if (!isset($datos['DocumentoAgrupador']) || $datos['DocumentoAgrupador']=="")
			$datos['DocumentoAgrupador']="0";

		if (!isset($datos['Class']) || $datos['Class']=="")
			$datos['Class']="NULL";

		if (!isset($datos['LoadTipoDocumento']) || $datos['LoadTipoDocumento']=="")
			$datos['LoadTipoDocumento']="NULL";

		if (!isset($datos['UnLoadTipoDocumento']) || $datos['UnLoadTipoDocumento']=="")
			$datos['UnLoadTipoDocumento']="NULL";

		if (!isset($datos['ClassEjecutar']) || $datos['ClassEjecutar']=="")
			$datos['ClassEjecutar']="NULL";

		if (!isset($datos['AltaProgramada']) || $datos['AltaProgramada']!="1")
			$datos['AltaProgramada']="0";


		if (!isset($datos['UrlAltaProgramada']) || $datos['UrlAltaProgramada']=="")
			$datos['UrlAltaProgramada']="NULL";

		if($datos['AltaProgramada']==0)
			$datos['UrlAltaProgramada']="NULL";

		if (!isset($datos['DocumentoExterno']) || $datos['DocumentoExterno']!="1")
			$datos['DocumentoExterno']="0";

		if (!isset($datos['DocumentosIlimitados']) || $datos['DocumentosIlimitados']!="1")
			$datos['DocumentosIlimitados']="0";


		if (!isset($datos['CantidadDocumentos']) || $datos['CantidadDocumentos']=="")
			$datos['CantidadDocumentos']="NULL";


		if($datos['DocumentosIlimitados']==1)
			$datos['CantidadDocumentos']="NULL";

		if (!isset($datos['EstadoPadreDependiente']) || $datos['EstadoPadreDependiente']=="")
			$datos['EstadoPadreDependiente']="1";

		if (!isset($datos['MuestraTitulo']) || $datos['MuestraTitulo']=="")
			$datos['MuestraTitulo']="0";

		if (!isset($datos['VigenciaHasta']) || $datos['VigenciaHasta']=="")
			$datos['VigenciaHasta']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";

		if (!isset($datos['AreaRecepcion']) || $datos['AreaRecepcion']=="")
			$datos['AreaRecepcion']="NULL";

		if (!isset($datos['EstadoRecepcion']) || $datos['EstadoRecepcion']=="")
			$datos['EstadoRecepcion']="NULL";


		if (!isset($datos['IdClasificacion']) || $datos['IdClasificacion']=="")
			$datos['IdClasificacion']="NULL";

		if (!isset($datos['MostrarAlta']) || $datos['MostrarAlta']!="1")
			$datos['MostrarAlta']="0";

        if (!isset($datos['MostrarAltaPON']) || $datos['MostrarAltaPON']!="1")
            $datos['MostrarAltaPON']="0";

        if (!isset($datos['TituloAgrupadorDependientes']) || $datos['TituloAgrupadorDependientes']=="")
			$datos['TituloAgrupadorDependientes']="NULL";

		if (!isset($datos['CodigoHost']) || $datos['CodigoHost']=="")
			$datos['CodigoHost']="NULL";

		return true;
	}



	private function _ValidarDatosVacios($datos)
	{

		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		/*if (!isset($datos['NombreCorto']) || $datos['NombreCorto']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre corto",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/



		if(isset($datos['IdTipoDocumentoPadre']) && $datos['IdTipoDocumentoPadre']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTipoDocumentoPadre'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if(isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento']!="")
			{
				if($datos['IdTipoDocumento'] == $datos['IdTipoDocumentoPadre'])
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error el tipo de documento superior no puede ser igual al tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
				}
			}
		}

		/*if (!isset($datos['IdCategoria']) || $datos['IdCategoria']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una categoria",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		/*if (!isset($datos['DocumentoAgrupador']) || $datos['DocumentoAgrupador']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar si es agrupador o no.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['TieneAdjunto']) || $datos['TieneAdjunto']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar si tiene o no adjuntos.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['DocumentoProgramado']) || $datos['DocumentoProgramado']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar si el documento es o no programado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['DocumentoExterno']) || ($datos['DocumentoExterno']!="1" && $datos['DocumentoExterno']!="0") )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar si el documento es o no externo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['DocumentosIlimitados']) || ($datos['DocumentosIlimitados']!="1" && $datos['DocumentosIlimitados']!="0"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar si el documento es o no ilimitado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if ($datos['DocumentosIlimitados']=="0")
		{

			if (!isset($datos['CantidadDocumentos']) || $datos['CantidadDocumentos']=="")
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar la cantidad de documentos.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}


		if (!isset($datos['AltaProgramada']) || ($datos['AltaProgramada']!="1" && $datos['AltaProgramada']!="0"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar si el documento es o no alta programada.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if ($datos['AltaProgramada']=="1")
		{
			if (!isset($datos['UrlAltaProgramada']) || $datos['UrlAltaProgramada']=="")
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar la url.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}





		if (!isset($datos['CargaPopup']) || $datos['CargaPopup']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar si el documento se carga a traves de un popup.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/


		/*if (!isset($datos['DescripcionCorta']) || $datos['DescripcionCorta']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una descripción corta",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		/*if (!isset($datos['DescripcionLarga']) || $datos['DescripcionLarga']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una descripción larga",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		/*if (!isset($datos['IdClasificacion']) || $datos['IdClasificacion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una clasificación",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		if (isset($datos['IdClasificacion']) && $datos['IdClasificacion']!="")
		{
			$oDocumentosClasificiacion = new cDocumentosClasificiacion($this->conexion,$this->formato);
			if(!$oDocumentosClasificiacion->BuscarxCodigo($datos,$resultado_clasificiacion,$numfilas_clasificiacion))
				return false;
			if($numfilas_clasificiacion!=1)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar una clasificación válida",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		return true;
	}


    public function Publicar($datos)
    {
        if (!$this->PublicarListadoJson())
            return false;
        return true;
    }


    public function PublicarListadoJson()
    {
        $nombrearchivo = "DocumentosTipos";
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
                $array[$fila['IdTipoDocumento']]['Id'] = $fila['IdTipoDocumento'];
                $array[$fila['IdTipoDocumento']]['Nombre'] = $fila['Nombre'];
            }
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

	private function ValidarConstante($datos)
    {
		$sparam = array(
            'xIdTipoDocumento'   => 0,
            'IdTipoDocumento'    => "",
        );

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento']!="") {
            $sparam['IdTipoDocumento']  = $datos['IdTipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        }

		$sparam['Constante']  = $datos['Constante'];

        if (!parent::BusquedaConstante($sparam, $resultado, $numfilas))
            return false;

		if ($numfilas > 0) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, esta constante ya esta asociada a un tipo de documento.", array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
    }

	public function BusquedaConstante($datos, &$resultado, &$numfilas)
    {
        if (!parent::BusquedaConstante($datos, $resultado, $numfilas))
            return false;

        return true;
    }



}
?>
