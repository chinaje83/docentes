<?php 
include(DIR_CLASES_DB."cAreas.db.php");

class cAreas extends cAreasdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	public function BuscarxIdRegistro($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdRegistro($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarxCodigosActivos($datos,&$resultado,&$numfilas)
	{
		if (!isset($datos['Anio']) || $datos['Anio']=="" || !is_numeric($datos['Anio']))
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) || $datos['Mes']=="" || !is_numeric($datos['Mes']))
			$datos['Mes'] = date("m");


		$datos['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";
		if (!parent::BuscarxCodigosActivos($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BuscarxIdAreaVigente($datos,&$resultado,&$numfilas)
	{
		if (!isset($datos['Anio']) && $datos['Anio']!="" && is_numeric($datos['Anio']))
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) && $datos['Mes']!="" && is_numeric($datos['Mes']))
			$datos['Mes'] = date("m");

		$datos['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";

		if (!parent::BuscarxIdAreaVigente($datos,$resultado,$numfilas))
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
	
	
	public function BuscarAreasxAreaSuperior($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'IdAreaSuperior'=> $datos['IdAreaSuperior'],
			'xEstado' => 0,
			'Estado' => "-1" 
			);
			if (isset($datos['Estado']) && $datos['Estado']!="")
			{
				$sparam['Estado']= $datos['Estado'];
				$sparam['xEstado']= 1;
			}
			
		if (!parent::BuscarAreasxAreaSuperior($sparam,$resultado,$numfilas))
			return false;
		
		return true;
	}
	
	
	public function BuscaAreasRaiz($datos,&$resultado,&$numfilas)
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
		
		if (!parent::BuscaAreasRaiz($sparam,$resultado,$numfilas))
			return false;
		
		return true;
	}
	
	
	public function BuscarAreasxAreaSuperiorVigente($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'IdAreaSuperior'=> $datos['IdAreaSuperior'],
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
			
		if (!parent::BuscarAreasxAreaSuperiorVigente($sparam,$resultado,$numfilas))
			return false;
		
		return true;
	}
	
	
	public function BuscaAreasRaizVigente($datos,&$resultado,&$numfilas)
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
		if (!parent::BuscaAreasRaizVigente($sparam,$resultado,$numfilas))
			return false;
		
		return true;
	}
	
	



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdRegistro'=> 0,
			'IdRegistro'=> "",
			'xIdArea'=> 0,
			'IdArea'=> "",
			'xIdTipo'=> 0,
			'IdTipo'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'xIdAreaSuperior'=> 0,
			'IdAreaSuperior'=> "",
			'xRecepcionAutomatica'=> 0,
			'RecepcionAutomatica'=> "",
			'xTieneBandejaEntrada'=> 0,
			'TieneBandejaEntrada'=> "",
			'xTieneBandejaSalida'=> 0,
			'TieneBandejaSalida'=> "",
			'xModificaCircuito'=> 0,
			'ModificaCircuito'=> "",
			'limit'=> '',
			'orderby'=> "a.IdRegistro DESC"
		);

		if(isset($datos['IdRegistro']) && $datos['IdRegistro']!="")
		{
			$sparam['IdRegistro']= $datos['IdRegistro'];
			$sparam['xIdRegistro']= 1;
		}
		if(isset($datos['IdArea']) && $datos['IdArea']!="")
		{
			$sparam['IdArea']= $datos['IdArea'];
			$sparam['xIdArea']= 1;
		}
		if(isset($datos['Estado']) && $datos['Estado']!="")
		{
			$sparam['Estado']= $datos['Estado'];
			$sparam['xEstado']= 1;
		}
		if(isset($datos['IdTipo']) && $datos['IdTipo']!="")
		{
			$sparam['IdTipo']= $datos['IdTipo'];
			$sparam['xIdTipo']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['IdAreaSuperior']) && $datos['IdAreaSuperior']!="")
		{
			$sparam['IdAreaSuperior']= $datos['IdAreaSuperior'];
			$sparam['xIdAreaSuperior']= 1;
		}
		if(isset($datos['RecepcionAutomatica']) && $datos['RecepcionAutomatica']!="")
		{
			$sparam['RecepcionAutomatica']= $datos['RecepcionAutomatica'];
			$sparam['xRecepcionAutomatica']= 1;
		}
		if(isset($datos['TieneBandejaEntrada']) && $datos['TieneBandejaEntrada']!="")
		{
			$sparam['TieneBandejaEntrada']= $datos['TieneBandejaEntrada'];
			$sparam['xTieneBandejaEntrada']= 1;
		}
		if(isset($datos['TieneBandejaSalida']) && $datos['TieneBandejaSalida']!="")
		{
			$sparam['TieneBandejaSalida']= $datos['TieneBandejaSalida'];
			$sparam['xTieneBandejaSalida']= 1;
		}
		if(isset($datos['ModificaCircuito']) && $datos['ModificaCircuito']!="")
		{
			$sparam['ModificaCircuito']= $datos['ModificaCircuito'];
			$sparam['xModificaCircuito']= 1;
		}

		if (!isset($datos['Anio']) || ($datos['Anio']!="" && is_numeric($datos['Anio'])))
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) || ($datos['Mes']!="" && is_numeric($datos['Mes'])))
			$datos['Mes'] = date("m");

		$sparam['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	
	public function BusquedaAvanzadaVigenciaHastaNull($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdRegistro'=> 0,
			'IdRegistro'=> "",
			'xIdArea'=> 0,
			'IdArea'=> "",
			'xIdTipo'=> 0,
			'IdTipo'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'xIdAreaSuperior'=> 0,
			'IdAreaSuperior'=> "",
			'xRecepcionAutomatica'=> 0,
			'RecepcionAutomatica'=> "",
			'xTieneBandejaEntrada'=> 0,
			'TieneBandejaEntrada'=> "",
			'xTieneBandejaSalida'=> 0,
			'TieneBandejaSalida'=> "",
			'xModificaCircuito'=> 0,
			'ModificaCircuito'=> "",
			'limit'=> '',
			'orderby'=> "a.IdRegistro DESC"
		);

		if(isset($datos['IdRegistro']) && $datos['IdRegistro']!="")
		{
			$sparam['IdRegistro']= $datos['IdRegistro'];
			$sparam['xIdRegistro']= 1;
		}
		if(isset($datos['IdArea']) && $datos['IdArea']!="")
		{
			$sparam['IdArea']= $datos['IdArea'];
			$sparam['xIdArea']= 1;
		}
		if(isset($datos['IdTipo']) && $datos['IdTipo']!="")
		{
			$sparam['IdTipo']= $datos['IdTipo'];
			$sparam['xIdTipo']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['IdAreaSuperior']) && $datos['IdAreaSuperior']!="")
		{
			$sparam['IdAreaSuperior']= $datos['IdAreaSuperior'];
			$sparam['xIdAreaSuperior']= 1;
		}
		if(isset($datos['RecepcionAutomatica']) && $datos['RecepcionAutomatica']!="")
		{
			$sparam['RecepcionAutomatica']= $datos['RecepcionAutomatica'];
			$sparam['xRecepcionAutomatica']= 1;
		}
		if(isset($datos['TieneBandejaEntrada']) && $datos['TieneBandejaEntrada']!="")
		{
			$sparam['TieneBandejaEntrada']= $datos['TieneBandejaEntrada'];
			$sparam['xTieneBandejaEntrada']= 1;
		}
		if(isset($datos['TieneBandejaSalida']) && $datos['TieneBandejaSalida']!="")
		{
			$sparam['TieneBandejaSalida']= $datos['TieneBandejaSalida'];
			$sparam['xTieneBandejaSalida']= 1;
		}
		if(isset($datos['ModificaCircuito']) && $datos['ModificaCircuito']!="")
		{
			$sparam['ModificaCircuito']= $datos['ModificaCircuito'];
			$sparam['xModificaCircuito']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzadaVigenciaHastaNull($sparam,$resultado,$numfilas))
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

	public function ArmarArbolAreas($IdArea,&$arbol,$string_estado_cat = "")
	{
		//traigo primero todos los hijos del categoria solicitado
		$total=0;
		if(!$this->ArregloHijos($IdArea,$arbol,$total,$string_estado_cat))
			return false;
		
		//ordeno por nombre los categorias
/*		$arbol=FuncionesPHPLocal::array_column_sort($arbol,"Nombre");*/
		
		//recorro todos los categorias para asignar la Ruta y armar el SubArbol dependiente
		foreach($arbol as $indice => $datos)
		{
			$arbol[$indice]["SubArbol"]=array();
	
			if(!$this->MostrarArbolJerarquia($datos["IdArea"],$jerarquia,$string_estado_cat))
				return false;
			$arbol[$indice]["Ruta"]=$jerarquia;
			
			//si tiene hijos entonces llamo a la funcion recursivamente para armar el SubArbol dependiente
			if($this->TieneHijos($datos["IdArea"],$ok,$string_estado_cat) && $ok)
			{
				if(!$this->ArmarArbolAreas($datos["IdArea"],$arbol[$indice]["SubArbol"],$string_estado_cat))
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
 
 
 	public function ArregloPadres($IdArea,&$arrcat,&$nivelarbol,$string_estado_cat="")
	{
		if ($IdArea!="")
		{
			$datoscat['IdArea'] = $IdArea;
			$datoscat['Estado'] = $string_estado_cat;
			if (!$this->BuscarxCodigo($datoscat,$resultado,$numfilas))
				return false;
			$result=true;
		
			if ($numfilas==0)
				$result=false;
				

			if ($result)
			{		
				while ($filasub=$this->conexion->ObtenerSiguienteRegistro($resultado))
				{
					$padre=$filasub['IdAreaSuperior'];
					$arrcat[]=$filasub;
				}
				$nivelarbol++;
				
				if ($padre!="")
					if (!$this->ArregloPadres($padre,$arrcat,$nivelarbol,$string_estado_cat))
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
	
	public function ArregloHijos($IdArea,&$arrcat,&$cantidadarreglo,$string_estado_cat="")
	{

		$arrcat = array();
		if ($IdArea!="")
		{
			$datoscat['IdAreaSuperior'] = $IdArea;
			$datoscat['Estado'] = $string_estado_cat;
			if (!$this->BuscarAreasxAreaSuperior($datoscat,$resultado,$numfilas))
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
			if (!$this->BuscaAreasRaiz($datoscat,$resultado,$numfilas))
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
//		IdArea: IdArea a buscar

// Retorna:
//		errcat: el error en caso de que se produzca
//		ok: devulve verdadero en caso de que tenga hijos, falso si no tiene.
//		la función retorna true o false si se pudo ejecutar con éxito o no

	
	public function TieneHijos($IdArea,&$ok,$string_estado_cat="")
	{
		
		$datoscat['IdAreaSuperior'] = $IdArea;
		$datoscat['Estado'] = $string_estado_cat;
		if (!$this->BuscarAreasxAreaSuperior($datoscat,$resultado,$numfilas))
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

	public function MostrarJerarquia($IdArea,&$jerarquia,&$nivel)
	{
		$i=1;
		$jerarquia="";
		$nivel=0;
		$arrjerarquia = array();
		if(!$this->ArregloPadres($IdArea,$arrjerarquia,$nivel))
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
				$jerarquia.="<a href='exp_areas.php?IdAreaSuperior=";
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
	
	public function MostrarArbolJerarquia($IdArea,&$jerarquia,$estilos=true,$string_estado_cat="")
	{
		$arrcat=array();
		if(!$this->ArregloPadres($IdArea,$arrjerarquia,$nivel,$string_estado_cat))
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

	public function ArmarArbolAreasVigente($IdArea,&$arbol,$Anio = "",$Mes = "",$string_estado_cat = "")
	{
		//traigo primero todos los hijos del categoria solicitado
		if ($Anio=="")
			$Anio = date("Y");
		if ($Mes=="")
			$Mes = date("m");
		
		$total=0;
		if(!$this->ArregloHijosVigente($IdArea,$arbol,$total,$Anio,$Mes,$string_estado_cat))
			return false;
		
		//ordeno por nombre los categorias
/*		$arbol=FuncionesPHPLocal::array_column_sort($arbol,"Nombre");*/
		
		//recorro todos los categorias para asignar la Ruta y armar el SubArbol dependiente
		foreach($arbol as $indice => $datos)
		{
			$arbol[$indice]["SubArbol"]=array();
	
			if(!$this->MostrarArbolJerarquiaVigente($datos["IdArea"],$jerarquia,$Anio,$Mes,$string_estado_cat))
				return false;
			$arbol[$indice]["Ruta"]=$jerarquia;
			
			//si tiene hijos entonces llamo a la funcion recursivamente para armar el SubArbol dependiente
			if($this->TieneHijosVigente($datos["IdArea"],$ok,$Anio,$Mes,$string_estado_cat) && $ok)
			{
				if(!$this->ArmarArbolAreasVigente($datos["IdArea"],$arbol[$indice]["SubArbol"],$Anio,$Mes,$string_estado_cat))
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
 
 
 	public function ArregloPadresVigente($IdArea,&$arrcat,&$nivelarbol,$Anio = "",$Mes = "",$string_estado_cat = "")
	{
		if ($IdArea!="")
		{
			$datoscat['IdArea'] = $IdArea;
			$datoscat['Estado'] = $string_estado_cat;
			$datoscat['Anio'] = $Anio;
			$datoscat['Mes'] = $Mes;
			if (!$this->BuscarxIdAreaVigente($datoscat,$resultado,$numfilas))
				return false;
			$result=true;
		
			if ($numfilas==0)
				$result=false;
				

			if ($result)
			{		
				while ($filasub=$this->conexion->ObtenerSiguienteRegistro($resultado))
				{
					$padre=$filasub['IdAreaSuperior'];
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
	
	public function ArregloHijosVigente($IdArea,&$arrcat,&$cantidadarreglo,$Anio,$Mes,$string_estado_cat)
	{

		$arrcat = array();
		if ($IdArea!="")
		{
			$datoscat['IdAreaSuperior'] = $IdArea;
			$datoscat['Estado'] = $string_estado_cat;
			$datoscat['Anio'] = $Anio;
			$datoscat['Mes'] = $Mes;
			if (!$this->BuscarAreasxAreaSuperiorVigente($datoscat,$resultado,$numfilas))
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
			if (!$this->BuscaAreasRaizVigente($datoscat,$resultado,$numfilas))
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
//		IdArea: IdArea a buscar

// Retorna:
//		errcat: el error en caso de que se produzca
//		ok: devulve verdadero en caso de que tenga hijos, falso si no tiene.
//		la función retorna true o false si se pudo ejecutar con éxito o no

	
	public function TieneHijosVigente($IdArea,&$ok,$Anio = "",$Mes = "",$string_estado_cat = "")
	{
		
		$datoscat['IdAreaSuperior'] = $IdArea;
		$datoscat['Estado'] = $string_estado_cat;
		$datoscat['Anio'] = $Anio;
		$datoscat['Mes'] = $Mes;
		if (!$this->BuscarAreasxAreaSuperiorVigente($datoscat,$resultado,$numfilas))
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

	public function MostrarJerarquiaVigente($IdArea,&$jerarquia,&$nivel,$Anio = "",$Mes = "",$string_estado_cat = "")
	{
		$i=1;
		$jerarquia="";
		$nivel=0;
		$arrjerarquia = array();
		if(!$this->ArregloPadresVigente($IdArea,$arrjerarquia,$nivel,$Anio,$Mes,$string_estado_cat))
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
				$jerarquia.="<a href='exp_areas.php?IdAreaSuperior=";
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




	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function Insertar($datos,&$IdArea,&$codigoinsertado)
	{
		
		
		if (!$this->_ValidarInsertar($datos))
			return false;
			

				
		$this->_SetearNull($datos);
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		$datos['IdArea'] = $IdArea =  $this->ObtenerSiguienteIdArea();
		
		if (!parent::InsertarDB($datos,$codigoinsertado))
			return false;

		$datos['IdRegistro'] = $codigoinsertado;
		
		if(!$this->ArregloPadres($datos['IdArea'],$arrcat,$nivelarbol))
			return false;
		
		$PadreSuperior = current($arrcat);
		$datos['IdAreaRaiz'] = $PadreSuperior['IdArea'];	
		if (!parent::ModificarAreaRaiz($datos))
			return false;		

		$oAuditoriasAreas = new cAuditoriasAreas($this->conexion,$this->formato);
		$datos['IdRegistro'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		if(!$oAuditoriasAreas->InsertarLog($datos,$codigoInsertadolog))
			return false;

		if (!$this->PublicarListadoJson())
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
			
		$oAuditoriasAreas = new cAuditoriasAreas($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasAreas->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;

		if (!$this->PublicarListadoJson())
			return false;
		
		return true;
	}
	
	
	public function Actualizar($datos,&$codigoinsertado)
	{
		$codigoinsertado ="";
		
		$cambio = false;
			
		if(!$this->BuscarxIdRegistro($datos,$resulatado,$numfilas))
			return false;
			
		$filaArea = $this->conexion->ObtenerSiguienteRegistro($resulatado);
		
		if($filaArea['Nombre']!=$datos['Nombre'])
			$cambio = true;		
			
		
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		
		if($cambio)
		{
			
			// modifico vigenciahasta del registro
			$filaArea['VigenciaHasta'] = date("Ymd");
			if (!parent::ModificarVigenciaHasta($filaArea))
				return false;	
			
			$oAuditoriasAreas = new cAuditoriasAreas($this->conexion,$this->formato);
			$filaArea['Accion'] = MODIFICACION;
			
			if(!$oAuditoriasAreas->InsertarLog($filaArea,$codigoInsertadolog))
				return false;	
			
			
			//inserto un nuevo registro con los datos 
			$datos['VigenciaHasta'] = "";	
			if(!$this->Insertar($datos,$codigoinsertado))
				return false;		

			
		}
		else
		{
			
			// modifico datos
			if (!$this->_ValidarModificar($datos,$datosRegistro))
				return false;
			
			$this->_SetearNull($datos);
			if (!parent::Modificar($datos))
				return false;

			
			$oAuditoriasAreas = new cAuditoriasAreas($this->conexion,$this->formato);
			$datosRegistro['Accion'] = MODIFICACION;
			if(!$oAuditoriasAreas->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;	
			
		}	
		if (!$this->PublicarListadoJson())
			return false;
		
		return true;
		
	}
	
	public function AgregarNuevaVigencia($datos,&$codigoinsertado,&$IdArea)
	{
		/*if (!cUsuariosPermisos::TienePermiso("004002")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para agregar una nueva vigencia al tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		if (!$this->_ValidarAgregarNuevaVigencia($datos,$datosRegistro))
			return false;

		$this->_SetearNull($datos);
		$IdArea = $datos['IdArea'];
		$datos['Nombre'] = $datos['Nombre'];
		$datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['AltaUsuario'] = $_SESSION['usuariocod'];
		$datos['AltaFecha'] = date("Y/m/d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::InsertarDB($datos,$codigoinsertado))
			return false;
			
			
		$oAuditoriasAreas = new cAuditoriasAreas($this->conexion,$this->formato);
		$datos['IdRegistro'] = $codigoinsertado;
		$datos['IdArea'] = $datos['IdArea'];
		$datos['Nombre'] = $datos['Nombre'];
		$datos['Accion'] = INSERTARNUEVAVIGENCIA;
		if(!$oAuditoriasAreas->InsertarLog($datos,$codigoInsertadolog))
			return false;
			
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


	
	public function PublicarListadoJson()
	{
		$nombrearchivo = "areas";
		$carpeta = PUBLICA."json/";
		if(!$this->GerenarArrayDatosJsonListado($array))
			return false;
		if(count($array)>0)
		{
			if(!$this->GuardarDatosJson($nombrearchivo,$carpeta,$array))
				return false;
		}
		return true;
	}



	public function GerenarArrayDatosJsonListado(&$array)
	{
		$array = array();
		$datos['Estado'] = ACTIVO;
		$datos['orderby'] = "Nombre ASC";
		if(!$this->BusquedaAvanzada($datos,$resultados,$numfilas))
			return false;
		if($numfilas>0)
		{
			while($fila = $this->conexion->ObtenerSiguienteRegistro($resultados))
			{
				$array[$fila['IdArea']] = $fila;
			}
		}
		return true;
	}

	
	
	public function ModificarVigencia($datos)
	{
		/*if (!cUsuariosPermisos::TienePermiso("004002")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para modificar la vigencia un tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		
		
		if (!$this->_ValidarModificarVigencia($datos,$datosRegistro))
			return false;

		$this->_SetearNull($datos);
		$datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

		if (!parent::ModificarVigencia($datos))
			return false;
			
		

		$oAuditoriasAreas = new cAuditoriasAreas($this->conexion,$this->formato);
		$datosLog = $datosRegistro;
		$datosLog['VigenciaDesde'] = $datos['VigenciaDesde'];
		$datosLog['VigenciaHasta'] = $datos['VigenciaHasta'];
		$datosLog['Accion'] = MODIFICARVIGENCIA;
		$datosLog['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
		$datosLog['UltimaModificacionUsuario'] = $datos['UltimaModificacionUsuario'];
		if(!$oAuditoriasAreas->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
			
		$oAreasTiposDocumentos = new cAreasTiposDocumentos($this->conexion,$this->formato);	
		$datosEliminar['IdRegistroArea'] = $datos['IdRegistro'];
		if(!$oAreasTiposDocumentos->EliminarxIdRegistroArea($datosEliminar))
			return false;	
			

		$oAuditoriasAreas = new cAuditoriasAreas($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasAreas->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		$datosmodif['IdRegistro'] = $datos['IdRegistro'];
		if (!parent::Eliminar($datosmodif))
			return false;
		
		if (!$this->PublicarListadoJson())
			return false;
			
		return true;
	}



	public function ModificarEstado($datos)
	{
		
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		
		if (!parent::ModificarEstado($datos))
			return false;
		
		if (!$this->PublicarListadoJson())
			return false;
			
		$datosRegistro['Estado'] = $datos['Estado'];
		$oAuditoriasAreas = new cAuditoriasAreas($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasAreas->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
				
		return true;
	}
	
	
	
	public function ModificarBandejaEntrada($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		
		if (!parent::ModificarBandejaEntrada($datos))
			return false;
		
		$datosRegistro['TieneBandejaEntrada'] = $datos['TieneBandejaEntrada'];	
		$oAuditoriasAreas = new cAuditoriasAreas($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasAreas->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
				
		return true;
	}
	
	
	public function ModificarBandejaSalida($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		
		if (!parent::ModificarBandejaSalida($datos))
			return false;
		
		$datosRegistro['TieneBandejaSalida'] = $datos['TieneBandejaSalida'];	
		$oAuditoriasAreas = new cAuditoriasAreas($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasAreas->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
				
		return true;
	}
	
	public function ModificarRecepcionAutomatica($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		
		if (!parent::ModificarRecepcionAutomatica($datos))
			return false;
		
		$datosRegistro['RecepcionAutomatica'] = $datos['RecepcionAutomatica'];	
		$oAuditoriasAreas = new cAuditoriasAreas($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasAreas->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
				
		return true;
	}
	
	
	public function ModificarModificaCircuito($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		
		if (!parent::ModificarModificaCircuito($datos))
			return false;
		
		$datosRegistro['ModificaCircuito'] = $datos['ModificaCircuito'];	
		$oAuditoriasAreas = new cAuditoriasAreas($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasAreas->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
				
		return true;
	}
	
	
	



	public function Activar($datos)
	{
		$datosmodif['IdRegistro'] = $datos['IdRegistro'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdRegistro'] = $datos['IdRegistro'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}
	
	
	public function ActivarBandejaEntrada($datos)
	{
		$datosmodif['IdRegistro'] = $datos['IdRegistro'];
		$datosmodif['TieneBandejaEntrada'] = 1;
		if (!$this->ModificarBandejaEntrada($datosmodif))
			return false;
		return true;
	}



	public function DesActivarBandejaEntrada($datos)
	{
		$datosmodif['IdRegistro'] = $datos['IdRegistro'];
		$datosmodif['TieneBandejaEntrada'] = 0;
		if (!$this->ModificarBandejaEntrada($datosmodif))
			return false;
		return true;
	}
	
	
	public function ActivarBandejaSalida($datos)
	{
		$datosmodif['IdRegistro'] = $datos['IdRegistro'];
		$datosmodif['TieneBandejaSalida'] = 1;
		if (!$this->ModificarBandejaSalida($datosmodif))
			return false;
		return true;
	}



	public function DesActivarBandejaSalida($datos)
	{
		$datosmodif['IdRegistro'] = $datos['IdRegistro'];
		$datosmodif['TieneBandejaSalida'] = 0;
		if (!$this->ModificarBandejaSalida($datosmodif))
			return false;
		return true;
	}
	
	
	public function ActivarRecepcionAutomatica($datos)
	{
		$datosmodif['IdRegistro'] = $datos['IdRegistro'];
		$datosmodif['RecepcionAutomatica'] = 1;
		if (!$this->ModificarRecepcionAutomatica($datosmodif))
			return false;
		return true;
	}



	public function DesActivarRecepcionAutomatica($datos)
	{
		$datosmodif['IdRegistro'] = $datos['IdRegistro'];
		$datosmodif['RecepcionAutomatica'] = 0;
		if (!$this->ModificarRecepcionAutomatica($datosmodif))
			return false;
		return true;
	}
	
	
	public function ActivarModificaCircuito($datos)
	{
		$datosmodif['IdRegistro'] = $datos['IdRegistro'];
		$datosmodif['ModificaCircuito'] = 1;
		if (!$this->ModificarModificaCircuito($datosmodif))
			return false;
		return true;
	}



	public function DesActivarModificaCircuito($datos)
	{
		$datosmodif['IdRegistro'] = $datos['IdRegistro'];
		$datosmodif['ModificaCircuito'] = 0;
		if (!$this->ModificarModificaCircuito($datosmodif))
			return false;
		return true;
	}




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function ObtenerSiguienteIdArea()
	{
		$idSiguiente = 0;
		if (!parent::BuscarUltimoIdArea($resultado,$numfilas))
			return false;
		
		if ($numfilas>0)
		{
			$datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
			$idSiguiente = $datos['UltimoIdArea'];	
			
		}	
		$idSiguiente = $idSiguiente+1;
			
		return $idSiguiente;	
	}
	
	
	
	private function _ValidarAgregarNuevaVigencia(&$datos,&$datosRegistro)
	{
		if (!isset($datos['IdArea']) || $datos['IdArea']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un area",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdArea'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo de codigo de area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas==0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un codigo de area valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarVigencia($datos))
			return false;

		$datosBusqueda['IdArea'] = $datosRegistro['IdArea'];
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
	
	
	private function _ValidarModificarVigencia(&$datos,&$datosRegistro)
	{
		//print_r($datos);die;	
		if (!$this->BuscarxIdRegistro($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un codigo de area valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarVigencia($datos))
			return false;


		$datosBusqueda['IdArea'] = $datosRegistro['IdArea'];
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
		if (!$this->BuscarxIdRegistro($datos,$resultado,$numfilas))
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
		if (!$this->BuscarxIdRegistro($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un cÃ³digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
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


		if (!isset($datos['IdArea']) || $datos['IdArea']=="")
			$datos['IdArea']="NULL";

		if (!isset($datos['IdTipo']) || $datos['IdTipo']=="")
			$datos['IdTipo']="NULL";

		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";

		if (!isset($datos['IdAreaSuperior']) || $datos['IdAreaSuperior']=="")
			$datos['IdAreaSuperior']="NULL";

		if (!isset($datos['IdAreaRaiz']) || $datos['IdAreaRaiz']=="")
			$datos['IdAreaRaiz']="NULL";

		if (!isset($datos['Deriva']) || $datos['Deriva']=="")
			$datos['Deriva']="NULL";

		if (!isset($datos['RecepcionAutomatica']) || $datos['RecepcionAutomatica']!="1")
			$datos['RecepcionAutomatica']="0";

		if (!isset($datos['TieneBandejaEntrada']) || $datos['TieneBandejaEntrada']!="1")
			$datos['TieneBandejaEntrada']="0";

		if (!isset($datos['TieneBandejaSalida']) || $datos['TieneBandejaSalida']!="1")
			$datos['TieneBandejaSalida']="0";

		if (!isset($datos['ModificaCircuito']) || $datos['ModificaCircuito']!="1")
			$datos['ModificaCircuito']="0";

		if (!isset($datos['VigenciaDesde']) || $datos['VigenciaDesde']=="")
			$datos['VigenciaDesde']="NULL";

		if (!isset($datos['VigenciaHasta']) || $datos['VigenciaHasta']=="")
			$datos['VigenciaHasta']="NULL";

		if (!isset($datos['JsonPadres']) || $datos['JsonPadres']=="")
			$datos['JsonPadres']="NULL";

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


		/*if (!isset($datos['IdArea']) || $datos['IdArea']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un código",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdArea'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		if (!isset($datos['IdTipo']) || $datos['IdTipo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tipo de área",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTipo'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		/*if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una descripción",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		
		
		if(isset($datos['IdAreaSuperior']) && $datos['IdAreaSuperior']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdAreaSuperior'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if(isset($datos['IdArea']) && $datos['IdArea']!="")
			{
				if($datos['IdArea'] == $datos['IdAreaSuperior'])
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error el area superior no puede ser igual al area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;	
				}	
			}
		}
		
		/*if (!isset($datos['IdAreaRaiz']) || $datos['IdAreaRaiz']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un área raiz",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdAreaRaiz'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/


		if(isset($datos['Deriva']) && $datos['Deriva']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Deriva'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if(isset($datos['IdArea']) && $datos['IdArea']!="")
			{
				if($datos['IdArea'] == $datos['Deriva'])
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error el area deriva no puede ser igual al area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;	
				}	
			}
		}
		

		if (!isset($datos['RecepcionAutomatica']) || $datos['RecepcionAutomatica']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar si tiene recepción automática",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['RecepcionAutomatica'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['TieneBandejaEntrada']) || $datos['TieneBandejaEntrada']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar si tiene bandeja de entrada",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['TieneBandejaEntrada'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['TieneBandejaSalida']) || $datos['TieneBandejaSalida']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar si tiene bandeja de salida",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['TieneBandejaSalida'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['ModificaCircuito']) || $datos['ModificaCircuito']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar si modifica Circuito",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['ModificaCircuito'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		

		
		/*if (!isset($datos['VigenciaDesde']) || $datos['VigenciaDesde']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una vigencia desde",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['VigenciaDesde'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['VigenciaHasta']) || $datos['VigenciaHasta']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una vigencia hasta",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['VigenciaHasta'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['JsonPadres']) || $datos['JsonPadres']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un json de padres",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		return true;
	}





}
?>