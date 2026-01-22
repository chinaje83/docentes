<?php 
include(DIR_CLASES_DB."cGruposCategorias.db.php");

class cGruposCategorias extends cGruposCategoriasdb
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
			'xIdGrupoCategoria'=> 0,
			'IdGrupoCategoria'=> "",
			'xIdGrupoCategoriaSuperior'=> 0,
			'IdGrupoCategoriaSuperior'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'limit'=> '',
			'orderby'=> "IdGrupoCategoria DESC"
		);

		if(isset($datos['IdGrupoCategoria']) && $datos['IdGrupoCategoria']!="")
		{
			$sparam['IdGrupoCategoria']= $datos['IdGrupoCategoria'];
			$sparam['xIdGrupoCategoria']= 1;
		}
		if(isset($datos['IdGrupoCategoriaSuperior']) && $datos['IdGrupoCategoriaSuperior']!="")
		{
			$sparam['IdGrupoCategoriaSuperior']= $datos['IdGrupoCategoriaSuperior'];
			$sparam['xIdGrupoCategoriaSuperior']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}



	public function grupos_categoriasSP(&$spnombre,&$sparam)
	{
		if (!parent::grupos_categoriasSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function grupos_categoriasSPResult(&$resultado,&$numfilas)
	{
		if (!$this->grupos_categoriasSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


//------------------------------------------------------------------------------------------	
// Retorna en un arreglo con los datos de un categoria

// Parámetros de Entrada:
//		IdGrupoCategoriaSuperior: categoria superior a buscar

// Retorna:
//		resultado= Arreglo con todos los datos de un categoria.
//		numfilas= cantidad de filas 
//		la función retorna true o false si se pudo ejecutar con éxito o no	
	public function BuscarGruposCategoriasxGrupoCategoriaSuperior($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarGruposCategoriasxGrupoCategoriaSuperior($datos,$resultado,$numfilas))
			return false;
		
		return true;
	}
//------------------------------------------------------------------------------------------	
// Retorna en un arreglo con los datos de un categoria de página

// Parámetros de Entrada:
//		IdGrupoCategoriaSuperior: categoria superior a buscar.Si vale "", entonces retorna el raiz de la categorias

// Retorna:
//		resultado= Arreglo con todos los datos de un categoria de página.
//		numfilas= cantidad de filas 
//		la función retorna true o false si se pudo ejecutar con éxito o no	
	public function BuscarAvanzadaxGrupoCategoriaSuperior($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdGrupoCategoriaSuperior'=> 0,
			'xIdGrupoCategoriaSuperior1'=> 0,
			'IdGrupoCategoriaSuperior1'=> "",
			'orderby'=> "Nombre ASC",
			'limit'=> ""
			);
			
		if (isset ($datos['IdGrupoCategoriaSuperior']) && $datos['IdGrupoCategoriaSuperior']!="")
		{
			$sparam['IdGrupoCategoriaSuperior1']= $datos['IdGrupoCategoriaSuperior'];
			$sparam['xIdGrupoCategoriaSuperior1']= 1;
		}
		else
			$sparam['xIdGrupoCategoriaSuperior']= 1;
	
		if (isset ($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];
		if (isset ($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

	
		if (!parent::BuscarAvanzadaxGrupoCategoriaSuperior($sparam,$resultado,$numfilas))
			return false;
		
		return true;
	}
	
//------------------------------------------------------------------------------------------	
// Retorna en un arreglo con los datos del raiz de una categoria 


// Retorna:
//		resultado= Arreglo con todos los datos de una categoria de páginas.
//		numfilas= cantidad de filas 
//		la función retorna true o false si se pudo ejecutar con éxito o no		

	public function BuscaGrupoCategoriaRaiz($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscaGrupoCategoriaRaiz($datos,$resultado,$numfilas))
			return false;
		
		return true;
	}

//----------------------------------------------------------------------------------------- 
// Retorna un array con todo el arbol dependiente del catcod ingresado

// Parámetros de Entrada:
//		catcod: raiz del arbol a retornar. Si vale "", entonces retorna el arbol completo de categorias de paginas

// Retorna:
//		arbol: array con el resultado de la consulta.
//					Además de la información del categoria, se agregan los subindices:
//						subarbol: arbol con los categorias dependientes del categoria 
//						ruta: jerarquia ascendente desde el categoria actual hasta la raiz
//		la función retorna true o false si se pudo ejecutar con éxito o no


	public function ArmarArbolGruposCategorias($IdGrupoCategoria,&$arbol)
	{
		//traigo primero todos los hijos del categoria solicitado
		$total=0;
		if(!$this->ArregloHijos($IdGrupoCategoria,$arbol,$total))
			return false;
		
		//ordeno por nombre los categorias
/*		$arbol=FuncionesPHPLocal::array_column_sort($arbol,"Nombre");*/
		
		//recorro todos los categorias para asignar la ruta y armar el subarbol dependiente
		foreach($arbol as $indice => $datos)
		{
			$arbol[$indice]["subarbol"]=array();
	
			if(!$this->MostrarArbolJerarquia($datos["IdGrupoCategoria"],$jerarquia))
				return false;
			$arbol[$indice]["ruta"]=$jerarquia;
			
			//si tiene hijos entonces llamo a la funcion recursivamente para armar el subarbol dependiente
			if($this->TieneHijos($datos["IdGrupoCategoria"],$ok) && $ok)
			{
				if(!$this->ArmarArbolGruposCategorias($datos["IdGrupoCategoria"],$arbol[$indice]["subarbol"]))
					return false;
			}
		}
		
		return true;
	}
	
	
	
	function ArmarArbolCategoriasxRol($datos,&$arbol)
	{
		$datos['xIdRol']=1;
		$oRoles = new cRoles($this->conexion,$this->formato);

		if ($oRoles->RolAdministrador($_SESSION['rolcod']))
			$datos['xIdRol']=0;

		if(!$this->BuscarGrupoCategoriaArbolxRol($datos,$resultado,$numfilas))
			return false;
			
		$arbol = array();	
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			$arbol[$fila['IdGrupoCategoriaSuperior']]['Nombre'] = $fila['NombreGrupoSuperior'];
			$arbol[$fila['IdGrupoCategoriaSuperior']]['IdGrupoCategoria'] = $fila['IdGrupoCategoriaSuperior'];
			$arbol[$fila['IdGrupoCategoriaSuperior']]['subarbol'][$fila['IdGrupoCategoria']]['Nombre'] = $fila['NombreGrupo'];
			$arbol[$fila['IdGrupoCategoriaSuperior']]['subarbol'][$fila['IdGrupoCategoria']]['IdGrupoCategoria'] = $fila['IdGrupoCategoria'];
			
		}
		
		return true;
	}
	
	
	//----------------------------------------------------------------------------------------- 
// Retorna un arreglo con todos los padres de una categoria

// Parámetros de Entrada:
//		IdGrupoCategoria: IdGrupoCategoria a buscar
//		nivelarbol= Se inicializa en 0.

// Retorna:
//		arrIdGrupoCategoria: devuelve el arreglo con todos los padres de la categoria de la página
//		nivelarbol: Devuelve el nivel en que se encuentra el categoria.
//		la función retorna true o false si se pudo ejecutar con éxito o no
 
 
 	public function ArregloPadres($IdGrupoCategoria,&$arrcat,&$nivelarbol)
	{
		if ($IdGrupoCategoria!="")
		{
			$datosrol['IdGrupoCategoria'] = $IdGrupoCategoria;
			if (!$this->BuscarxCodigo($datosrol,$resultado,$numfilas))
				return false;
			$result=true;
		
			if ($numfilas==0)
				$result=false;

			if ($result)
			{		
				while ($filasub=$this->conexion->ObtenerSiguienteRegistro($resultado))
				{
					$padre=$filasub['IdGrupoCategoriaSuperior'];
					
					$arrcat[]=$filasub;
				}
				$nivelarbol++;
				if ($padre!="")
					if (!$this->ArregloPadres($padre,$arrcat,$nivelarbol))
						return false;

				$darvueltaarreglo=asort($arrcat);
			}
		}
		return true;
	} 


//----------------------------------------------------------------------------------------- 
// Retorna un arreglo con todos los hijos de un categoria

// Parámetros de Entrada:
//		IdGrupoCategoria: categoria a buscar
//		cantidadarreglo: Se inicializa en 0.

// Retorna:
//		arrcat: devuelve el arreglo con todos los hijos de la categoria de páginas.
//		errcat: el error en caso de que se produzca
//		cantidadarreglo: La cantidad total del arreglo.
//		la función retorna true o false si se pudo ejecutar con éxito o no
	
	public function ArregloHijos($IdGrupoCategoria,&$arrcat,&$cantidadarreglo)
	{

		$arrcat = array();
		if ($IdGrupoCategoria!="")
		{
			$datosrol['IdGrupoCategoriaSuperior'] = $IdGrupoCategoria;
			if (!$this->BuscarGruposCategoriasxGrupoCategoriaSuperior($datosrol,$resultado,$numfilas))
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
			$datosrol = array();
			if (!$this->BuscaGrupoCategoriaRaiz($datosrol,$resultado,$numfilas))
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
// Retorna un ok si tiene hijos

// Parámetros de Entrada:
//		IdGrupoCategoria: IdGrupoCategoria a buscar

// Retorna:
//		errcat: el error en caso de que se produzca
//		ok: devulve verdadero en caso de que tenga hijos, falso si no tiene.
//		la función retorna true o false si se pudo ejecutar con éxito o no

	
	public function TieneHijos($IdGrupoCategoria,&$ok)
	{
		
		$datosrol['IdGrupoCategoriaSuperior'] = $IdGrupoCategoria;
		if (!$this->BuscarGruposCategoriasxGrupoCategoriaSuperior($datosrol,$resultado,$numfilas))
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
// Retorna la rama ascendente de un categoria

// Parámetros de Entrada:
//		IdGrupoCategoria: categoria de página a buscar

// Retorna:
//		jerarquia: un string con la ruta
//		errcat: el error en caso de que se produzca
//		la función retorna true o false si se pudo ejecutar con éxito o no
	
	public function MostrarArbolJerarquia($IdGrupoCategoria,&$jerarquia,$estilos=true)
	{
		$arrcat=array();
		if(!$this->ArregloPadres($IdGrupoCategoria,$arrjerarquia,$nivel))
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
	
	
	


	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos))
			return false;

		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
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

	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		return true;
	}



	private function _ValidarModificar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarDatosVacios($datos))
			return false;
			
		if($datos['IdGrupoCategoria']==$datos['IdGrupoCategoriaSuperior'])
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el grupo categoria superior no puede el mismo que el grupo categoria.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
			
		}	

		return true;
	}



	private function _ValidarEliminar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!$this->ArregloHijos($datos['IdGrupoCategoria'],$arrcat,$cantidadarreglo))
			return false;
	
		if ($cantidadarreglo>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el grupo categoria contiene subgrupos categorias asociadas. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
			return false;
		}
		return true;
	}



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['IdGrupoCategoriaSuperior']) || $datos['IdGrupoCategoriaSuperior']=="")
			$datos['IdGrupoCategoriaSuperior']="NULL";

		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		/*if (!isset($datos['IdGrupoCategoriaSuperior']) || $datos['IdGrupoCategoriaSuperior']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id de grupo superior",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		if (isset($datos['IdGrupoCategoriaSuperior']) && $datos['IdGrupoCategoriaSuperior']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdGrupoCategoriaSuperior'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			
			if (!$this->conexion->TraerCampo('GruposCategorias','IdGrupoCategoria',array('IdGrupoCategoria='.$datos['IdGrupoCategoriaSuperior']),$dato,$numfilas,$errno))
				return false;

			if ($numfilas!=1)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}





}
?>