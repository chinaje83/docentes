<?php  
include(DIR_CLASES_DB . "old.cFeriados.db.php");

/**
 * Class oldCFeriados
 *
 * @deprecated
 */
class oldCFeriados extends old_cFeriadosdb
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

	public function BuscarFeriadosxFechasxConfiguracion ($datos,&$resultado,&$numfilas)
	{
		$datosenviar['feriadoestado'] = ACTIVO;
		$datosenviar['fechainicio'] = $datos['fechainicio'];
		$datosenviar['fechafin'] = $datos['fechafin'];
		
		if (!parent::BuscarFeriadosxFechasxConfiguracion ($datosenviar,$resultado,$numfilas))
			return false;
		return true;	
	}
	
	public function Buscar (&$resultado,&$numfilas)
	{
		if (!parent::Buscar ($resultado,$numfilas))
			return false;
		return true;	
	}
	
	
	
	public function BusquedaFeriados ($datos,&$numfilas,&$resultado)
	{
		$sparam=array(
			'xferiadosmes'=> 0,
			'feriadosmes'=> "",
			'xferiadosano'=> 0,
			'feriadosano'=> "",
			'orderby'=> "feriadodia ASC",
			'limit'=> ""
		);	
	
		if (isset ($datos['feriadosmes']))
		{
			if ($datos['feriadosmes']!="")
			{	
				$sparam['feriadosmes']= $datos['feriadosmes'];
				$sparam['xferiadosmes']= 1;
			}
		}
		if (isset ($datos['feriadosano']))
		{
			if ($datos['feriadosano']!="")
			{	
				$sparam['feriadosano']= $datos['feriadosano'];
				$sparam['xferiadosano']= 1;
			}
		}
		if (isset ($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];
			
		if (isset ($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];
			
		if (!parent::BusquedaFeriados ($sparam,$numfilas,$resultado))
			return false;
		return true;
	}
	
	
	public function Insertar ($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;
		$datos['feriadodia'] = FuncionesPHPLocal::ConvertirFecha($datos['feriadodia'],"dd/mm/aaaa","aaaa-mm-dd");
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

        if (!$this->PublicarTodos())
            return false;
			
		return true;
	}	
	
	
	public function BuscarFeriadosxCodigo ($datos,&$numfilas,&$resultado)
	{
		if (!parent::BuscarFeriadosxCodigo ($datos,$numfilas,$resultado))
			return false;
		return true;	
	}
	
	public function Modificar ($datos)
	{
		if (!$this->_ValidarModificar($datos))
			return false;

		$datos['feriadodia'] = FuncionesPHPLocal::ConvertirFecha($datos['feriadodia'],"dd/mm/aaaa","aaaa-mm-dd");
		if (!parent::Modificar($datos))
			return false;

        if (!$this->PublicarTodos())
            return false;
			
		return true;
	}	

	public function ActivarDesactivar ($datos)
	{
		
		
		if (!$this->_ValidarActivarDesactivar($datos))
			return false;
	
		if (!parent::ActivarDesactivar($datos))
			return false;

        if (!$this->PublicarTodos())
            return false;
		
		return true;
	}



	public function Activar($datos)
	{
		$datos['feriadoestado'] = ACTIVO;
		if (!$this->ActivarDesactivar($datos))
			return false;

		return true;	
	} 

	public function DesActivar($datos)
	{
		$datos['feriadoestado'] = NOACTIVO;
		if (!$this->ActivarDesactivar($datos))
			return false;
	
		return true;	
	} 

    private function _ValidarActivarDesactivar($datos)
	{
		
		if (!$this->BuscarFeriadosxCodigo($datos,$resultado,$numfilas))
			return false;
	
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error feriado inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
			
		return true;
	}
	
	public function Eliminar ($datos)
	{
		if (!$this->_ValidarEliminar($datos))
			return false;
	
		if (!parent::Eliminar($datos))
			return false;
		
		return true;
	}

	private function _ValidarEliminar($datos)
	{
		
		if (!$this->BuscarFeriadosxCodigo($datos,$resultado,$numfilas))
			return false;
	
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, feriado inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
		
		
	}
	
	private function _ValidarModificar ($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		
		return true;
	}	
	
	private function _ValidarInsertar ($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		
		return true;
	}
	
	private function _ValidarDatosVacios($datos)
	{
		if (!isset ($datos['feriadodesc']) || ($datos['feriadodesc']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar una descripción de feriado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!isset ($datos['feriadodia']) || ($datos['feriadodia']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar una fecha de feriado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if(!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['feriadodia'],"FechaDDMMAAAA"))
		{	
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar una fecha valida.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;		
		
	}


    public function PublicarTodos()
    {
        $array = array();
        if(!$this->Buscar($resultado,$numfilas))
            return false;

        if($numfilas>0)
        {
            while($fila= $this->conexion->ObtenerSiguienteRegistro($resultado))
            {
                $array[$fila['feriadocod']]= FuncionesPHPLocal::ConvertiraUtf8($fila);
            }
            $json = json_encode($array);
            file_put_contents(PUBLICA."json/feriados.json" , $json);

        }
        return true;
    }


}

?>