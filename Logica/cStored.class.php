<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con la lógica para el manejo de stored procedures

include(DIR_CLASES_DB."cStored.db.php");

class cStored extends cStoreddb	
{
	protected $conexion;
	protected $formato;
	
	
	// Constructor de la clase
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		parent::__construct(); 
    } 
	
	// Destructor de la clase
	function __destruct() {	
		parent::__destruct(); 
    } 	

	
//-----------------------------------------------------------------------------------------
//							 PUBLICAS	
//----------------------------------------------------------------------------------------- 

//----------------------------------------------------------------------------------------- 
// Retorna una consulta con todos los usuarios que cumplan con las condiciones

// Parámetros de Entrada:
//		ArregloDatos: array asociativo con los filtros. Claves: usuarionombre, usuarioapellido, usuariocuit, usuarioemail

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no



	public function Buscar ($ArregloDatos,&$numfilas,&$resultado)
	{
		if (!parent::Buscar($ArregloDatos,$numfilas,$resultado))
			return false;

		return true;
	}



	public function Insertar ($ArregloDatos,&$codigoinsertado)
	{
		if (!$this->Buscar ($ArregloDatos,$numfilas,$resultado))
			return false;
		
		if ($numfilas==1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, El Stored ya se encuentra insertado en la tabla de Stored Procedures.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!parent::Insertar($ArregloDatos,$codigoinsertado))
			return false;

		return true;
	}


	public function Modificar ($ArregloDatos)
	{
		
		if (!parent::Modificar($ArregloDatos))
			return false;
			
		return true;
	}

	public function Eliminar ($ArregloDatos)
	{
		if (!isset ($ArregloDatos['spcod']) || ($ArregloDatos['spcod']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error Código de Stored Procedure Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!$this->Buscar ($ArregloDatos,$numfilas,$resultado))
			return false;
		
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error Código de Stored Procedure Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!parent::Eliminar($ArregloDatos))
			return false;

		return true;
	}




	public function VerificarExistenciaStore ($spnombre,$carpetas,&$encontrado,&$texto)
		{			
			$encontrado=false;
			$texto="<span class='textonombredatos'>El Stored Procedure a Borrar se encuentra usandose en o los Archivos: </span><br />";
			foreach ($carpetas as $datos)
			{
				if (is_dir($datos))
				{
					$primeroencontradocarpeta=0;
					$archivosdir=array();
					chdir ($datos);
					
					if ($dh = opendir($datos)) 
					{
						while (($file = readdir($dh)) !== false) 
						{
							if($file!='.' && $file!='..' && array_pop(explode('.', $file))=="php")
								$archivosdir[]=$file;
						} // fin while
						closedir($dh);
					}
					natcasesort($archivosdir);
					foreach ($archivosdir as $file)
					{
						$Textoarchivofile=file_get_contents($file,0);
						$poslines = strpos($Textoarchivofile,$spnombre);
						
						if ($poslines!="")
						{	
							$lineacodigo=file($file);
							$comillasanteriores=$poslines-1;
							$ok=false;
							if (($Textoarchivofile[$comillasanteriores]=='"') || ($Textoarchivofile[$comillasanteriores]=="'"))
							{	
								$comilla=$Textoarchivofile[$comillasanteriores];
								$cantidadletras=strlen($spnombre);
								$comillasposteriores=$poslines+$cantidadletras;
								if ($Textoarchivofile[$comillasposteriores]==$comilla)
									$ok=true;
							}	
							if ($ok)
							{
								for($i=1;$i<count($lineacodigo);$i++){
									
									if (strpos($lineacodigo[$i],$spnombre))
									{
										$i++;
										if(!$primeroencontradocarpeta)
										{
											$texto.="<br /><span class='negrita'>".$datos."</span><br /><br />";
											$primeroencontradocarpeta=1;
										}	
										$texto.="<span class='textonombreclave'>'".$file."'</span> en la linea: <span class='textonombreclave'>".$i."</span><br />";
										$encontrado=true;
									}
								}
							}
								
						}
						
						
					}
					$texto.="<br /><hr width='50%' align='center'>";	
				}	
			}	
			return true;

		}
		
		
	public function TraerTablas($bd,&$resultado) 
	{
		if (!parent::TraerTablas($bd,$resultado))
			return false;

		return true;
	}	
	
	
	public function TraerCampos($tabla,&$resultado)
	{
		if (!parent::TraerCampos($tabla,$resultado))
			return false;
		return true;
	}	
		
		
}//FIN CLASE

?>