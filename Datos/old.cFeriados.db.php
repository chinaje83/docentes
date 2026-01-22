<?php

/**
 * Class old_cFeriadosdb
 *
 * @deprecated
 */
abstract class old_cFeriadosdb
{
	
	// Constructor de la clase
	function __construct(){


    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	

	
//-----------------------------------------------------------------------------------------
//							 PUBLICAS	
//----------------------------------------------------------------------------------------- 

//----------------------------------------------------------------------------------------- 

//----------------------------------------------------------------------------------------- 
// Retorna el SP y los parametros para cargar los roles del sistema

	protected function Buscar(&$resultado,&$numfilas)
	{
		$this->FeriadosSP($spnombre,$sparam);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los feriados.",array("archivo" => __FILE__,									"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
	
		return true;
	
	}

	protected function FeriadosSP(&$spnombre,&$sparam)
	{
		//echo "print parametros pasados a FeriadosSP en cFeriados.db:  ";
		//print_r($sparam);
		$spnombre="sel_feriados";
		$sparam=array(
			'porderby'=> "feriadodia"
			);
	
		return true;
	}


	protected function BuscarFeriadosxFechasxConfiguracion($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_feriados_busqueda";
		$sparam=array(
			'pferiadoestado'=> $datos['feriadoestado'],
			'pfechainicio'=> $datos['fechainicio'],
			'pfechafin'=> $datos['fechafin']
			);		
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los feriados.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
	
		return true;
	
	}


	function BusquedaFeriados ($datos,&$numfilas,&$resultado)
	{
	
		$spnombre="sel_feriado_busqueda_filtros";
		$sparam=array(
			'pxferiadosmes'=> $datos['xferiadosmes'],
			'pferiadosmes'=> $datos['feriadosmes'],
			'pxferiadosano'=> $datos['xferiadosano'],
			'pferiadosano'=> $datos['feriadosano'],
			'porderby'=> $datos['orderby'],
			'plimit'=> $datos['limit']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la busqueda filtrada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		return true;
	}

	protected function Insertar($datos,&$codigoinsertado)
	{
        
		$spnombre="ins_feriados";
		$sparam=array(
			'pferiadodesc'=> $datos['feriadodesc'],
			'pferiadodia'=> $datos['feriadodia'],
			'pferiadoestado'=> NOACTIVO,
			'pultmodfecha'=> date("Y/m/d H:i:s"),
			'pultmodusuario'=> $_SESSION['usuariocod']
			);
			
			if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) && $numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el feriado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();
		return true;
	}

	protected function BuscarFeriadosxCodigo($datos,&$resultado,&$numfilas)
	{
		
		$spnombre="sel_feriados_xferiadocod";
		$sparam=array(
			'pferiadocod'=> $datos['feriadocod']
			);	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el feriado por código.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
	
		return true;
	}
	
	
	protected function Modificar($datos)
	{
		//print_r($datos);
		$spnombre="upd_feriados_xferiadocod";
		$sparam=array(
			'pferiadocod'=> $datos['feriadocod'],
			'pferiadodesc'=> $datos['feriadodesc'],
			'pferiadodia'=> $datos['feriadodia'],
			'pultmodfecha'=> date("Y/m/d H:i:s"),
			'pultmodusuario'=> $_SESSION['usuariocod']
			);
	
		//print_r($sparam);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el feriado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}

	protected function ActivarDesactivar($datos)
	{
		
		$spnombre="upd_feriado_estado_xferiadocod";
		$sparam=array(
			'pferiadocod'=> $datos['feriadocod'],
			'pultmodfecha'=> date("Y/m/d H:i:s"),
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pferiadoestado'=> $datos['feriadoestado']
			);
	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al activar / desactivar el feriado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	protected function Eliminar($datos)
	{
		
		$spnombre="del_feriados_xferiadocod";
		$sparam=array(
			'pferiadocod'=> $datos['feriadocod']
			);		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) && $numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar el feriado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}

	
}
?>