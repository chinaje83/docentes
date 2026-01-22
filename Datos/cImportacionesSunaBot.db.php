<?php 
abstract class cImportacionesSunaBotdb
{


	function __construct(){}

	function __destruct(){}

	
	

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
        $spnombre="sel_ImportacionesSUNABOT_xIdRegistro";
        $sparam=array(
            'pIdRegistro'=> $datos['IdRegistro']
        );
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	

	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
        $spnombre="sel_ImportacionesSUNABOT_busqueda_avanzada";
        $sparam=array(
            'pxIdRegistro'=> $datos['xIdRegistro'],
            'pIdRegistro'=> $datos['IdRegistro'],
            'pxIdDocumento'=> $datos['xIdDocumento'],
            'pIdDocumento'=> $datos['IdDocumento'],
            'pxSecuencia'=> $datos['xSecuencia'],
            'pSecuencia'=> $datos['Secuencia'],
            'pxIdArea'=> $datos['xIdArea'],
            'pIdArea'=> $datos['IdArea'],
            'pxIdEstado'=> $datos['xIdEstado'],
            'pIdEstado'=> $datos['IdEstado'],
            'pxIdEstadoImportacion'=> $datos['xIdEstadoImportacion'],
            'pIdEstadoImportacion'=> $datos['IdEstadoImportacion'],
            'pxObservacionesHOST'=> $datos['xObservacionesHOST'],
            'pObservacionesHOST'=> $datos['ObservacionesHOST'],
            'porderby'=> $datos['orderby'],
            'plimit'=> $datos['limit']
        );

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





	protected function Insertar($datos,&$codigoinsertado)
	{
        $spnombre="ins_ImportacionesSUNABOT";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento'],
            'pIdArea'=> $datos['IdArea'],
            'pIdEstado'=> $datos['IdEstado'],
            'pObservacionesHOST'=> $datos['ObservacionesHOST'],
            'pAltaFecha'=> $datos['AltaFecha'],
            'pSecuencia'=> $datos['Secuencia']
        );
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		$datosModif['RegistroSeguridad'] = md5($codigoinsertado.CLAVEENCRIPTACION.md5($datos['Password']));
		$datosModif["IdUsuario"]=$codigoinsertado;
		if (!$this->ModificarCodigoSeguridad($datosModif))
			return false;
		
		return true;
	}




	
	protected function Eliminar($datos)
	{
        $spnombre="del_ImportacionesSUNABOT_xIdRegistro";
        $sparam=array(
            'pIdRegistro'=> $datos['IdRegistro']
        );
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
		    echo "aca";die;
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





// Retorna una consulta con todos los usuarios que cumplan con las condiciones

// Parámetros de Entrada:
//		datosbuscar: array asociativo con los filtros. Claves: usuarionombre, usuarioapellido, usuariocuit, usuarioemail

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no






	protected function BuscarUsuarios ($ArregloDatos,&$resultado,&$numfilas)
	{

		
		$sparam=array('pestadocod' =>0);
		$sparam+=array('pestadopass' =>0);
		$sparam+=array('pestadonom' =>0);
		$sparam+=array('pestadoape' =>0);
		$sparam+=array('pestadoemail' =>0);
		$sparam+=array('pestadoestado' =>0);
		
		$sparam+=array('pIdUsuario' =>"");
		$sparam+=array('pPassword' =>"");
		$sparam+=array('pNombre' =>"");
		$sparam+=array('pApellido' =>"");
		$sparam+=array('pEmail' =>"");
		$sparam+=array('pIdEstado' =>"");



		if (isset ($ArregloDatos['pIdUsuario']))
		{
			if ($ArregloDatos['pIdUsuario']!="")
			{	
				$sparam['pIdUsuario']= $ArregloDatos['pIdUsuario'];
				$sparam['pestadocod']= 1;
			}
		}	
		if (isset ($ArregloDatos['pPassword']))
		{
			if ($ArregloDatos['pPassword']!="")
			{	
				$sparam['pPassword']= $ArregloDatos['pPassword'];
				$sparam['pestadopass']= 1;
			}
		}	
		if (isset ($ArregloDatos['pNombre']))
		{
			if ($ArregloDatos['pNombre']!="")
			{	
				$sparam['pNombre']= $ArregloDatos['pNombre'];
				$sparam['pestadonom']= 1;
			}
		}
		if (isset ($ArregloDatos['pApellido']))
		{
			if ($ArregloDatos['pApellido']!="")
			{	
				$sparam['pApellido']= $ArregloDatos['pApellido'];
				$sparam['pestadoape']= 1;
			}
		}
		if (isset ($ArregloDatos['pEmail']))
		{
			if ($ArregloDatos['pEmail']!="")
			{	
				$sparam['pEmail']= $ArregloDatos['pEmail'];
				$sparam['pestadoemail']= 1;
			}
		}
		if (isset ($ArregloDatos['pIdEstado']))
		{
			if ($ArregloDatos['pIdEstado']!="")
			{	
				$sparam['pIdEstado']= $ArregloDatos['pIdEstado'];
				$sparam['pestadoestado']= 1;
			}
		}
		

		$spnombre="sel_usuarios";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar una busqueda de usuario. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}
}
?>