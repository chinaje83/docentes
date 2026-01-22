<?php  
abstract class cCircuitosAreasEstadosdb
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


	protected function BuscarxCodigo($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstados_xIdNodoWorkflow";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
			);		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	protected function BuscarAreasEstadosxCircuitoBd($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstados_xIdCircuito";
		$sparam=array(
			"pIdCircuito" => $datos['IdCircuito']
			);		
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	
	protected function BuscarAreasEstadosNodoInicialxCircuitoBd($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstados_NodoInicial_xIdCircuito";
		$sparam=array(
			"pIdCircuito" => $datos['IdCircuito']
			);		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	
	
	
	
	
	protected function BuscarAreasEstadosVigentesxCircuitoBd($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstadosVigentes_xIdCircuito";
		$sparam=array(
			"pVigencia" => $datos['Vigencia'],
			"pIdCircuito" => $datos['IdCircuito']
			);		
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	protected function BuscarEstadosxCircuitoBd($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstados_Estados_xIdCircuito";
		$sparam=array(
			"pIdCircuito" => $datos['IdCircuito']
			);		
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	protected function BuscarxAreaxEstadoxCircuito($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstados_xIdAreaxIdEstadoxIdCircuito";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pIdEstado'=> $datos['IdEstado'],
			'pIdCircuito'=> $datos['IdCircuito']
			);		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas por circuito y estado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	protected function BuscarxAreaxEstadoxCircuitoNodoGeneral($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstadosNodoGeneral_xIdEstadoxIdCircuito";
		$sparam=array(
			'pIdEstado'=> $datos['IdEstado'],
			'pIdCircuito'=> $datos['IdCircuito']
			);		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas por circuito y estado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	

	protected function BuscarxAreaxEstadoxCircuitoxEstado($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstados_xIdEstadoxIdCircuito";
		$sparam=array(
			'pIdEstado'=> $datos['IdEstado'],
			'pIdCircuito'=> $datos['IdCircuito']
			);		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas por circuito y estado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	

	protected function InsertarBD($datos,&$codigoinsertado)
	{			

		$spnombre="ins_CircuitosAreasEstados";
		$sparam=array(
			'pIdCircuito'=> $datos['IdCircuito'],
			'pIdEstado'=> $datos['IdEstado'],
			'pPosicionArriba'=> $datos['PosicionArriba'],
			'pPosicionIzquierda'=> $datos['PosicionIzquierda'],
			'pNodoInicial'=> $datos['NodoInicial'],
			'pNodoGeneral' => $datos['NodoGeneral'],
			'pFechaAlta'=> $datos['FechaAlta'],
			'pAltaUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
			);		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$query,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar un circuito de workflow. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}



	protected function ModificarPosicion ($datos)
	{
		$spnombre="upd_CircuitosAreasEstados_Posicion_xIdNodoWorkflow";
		$sparam=array(
			'pPosicionArriba'=> $datos['PosicionArriba'],
			'pPosicionIzquierda'=> $datos['PosicionIzquierda'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar la posicion de las areas.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}
	
	protected function Eliminar ($datos)
	{
		$spnombre="del_CircuitosAreasEstados_xIdNodoWorkflow";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar el area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function ModificarNodoGeneral ($datos)
	{
		$spnombre="upd_CircuitosAreasEstados_NodoGeneral_xIdNodoWorkflow";
		$sparam=array(
			'pNodoGeneral'=> $datos['NodoGeneral'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar la posicion de las areas.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}
	

	protected function ModificarNodoInicial ($datos)
	{
		$spnombre="upd_CircuitosAreasEstados_NodoInicial_xIdNodoWorkflow";
		$sparam=array(
			'pNodoInicial'=> $datos['NodoInicial'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar la posicion de las areas.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}
	


}
?>