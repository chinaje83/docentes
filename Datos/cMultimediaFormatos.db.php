<?php  
abstract class cMultimediaFormatosdb
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

// Parámetros de Entrada:
//		datos: arreglo de datos
//			formatocod = codigo del formato

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	protected function BuscarMultimadiaFormatoxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_formatos_xformatocod";
		$sparam=array(
			'pformatocod'=> $datos['formatocod']
			);
	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el formato multimedia por código.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;	
	}
	
// Parámetros de Entrada:
//		datos: arreglo de datos
//			formatocod = codigo del formato

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	protected function BuscarMultimadiaFormatosSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_mul_multimedia_formatos";
		$sparam=array(
			);
		
		return true;	
	}
	
	
// Parámetros de Entrada:
//		datos: arreglo de datos
//			formatocod = codigo del formato

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	protected function BuscarMultimadiaFormatoxEstado($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_formatos_xformatoestado";
		$sparam=array(
			'pformatoestado'=> $datos['formatoestado']
			);
	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los formatos por estado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;	
	}
	
	
	
//------------------------------------------------------------------------------------------	
// Retorna en un arreglo con los datos de un formato multimedia

// Parámetros de Entrada:

// Retorna:
//		resultado= Arreglo con todos los datos de un formato multimedia.
//		numfilas= cantidad de filas 
//		la función retorna true o false si se pudo ejecutar con éxito o no	
	

	protected function BusquedaAvanzada ($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_formatos_busqueda";
		$sparam=array(
			'pxformatodesc'=> $datos['xformatodesc'],
			'pformatodesc'=> $datos['formatodesc'],
			'pxformatoancho'=> $datos['xformatoancho'],
			'pformatoancho'=> $datos['formatoancho'],
			'pxformatoalto'=> $datos['xformatoalto'],
			'pformatoalto'=> $datos['formatoalto'],
			'pxformatocarpeta'=> $datos['xformatocarpeta'],
			'pformatocarpeta'=> strtoupper($datos['formatocarpeta']),
			'porderby'=> $datos['orderby'],
			'plimit'=> $datos['limit']
			);
	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el formato multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
//----------------------------------------------------------------------------------------- 
// Inserta nuevo formato

// Parámetros de Entrada:
//			formatodesc: descripción del formato
//			formatoancho: ancho del formato
//			fomrnatoalto: alto del formato
//			formatocarpeta: formato de la carpeta
//			formatocropea: si se cropea el formato vale 1 si no vale 0
// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no
	
	protected function Insertar($datos,&$codigoinsertado)
	{

		$spnombre="ins_mul_multimedia_formatos";
		$sparam=array(
			'pformatodesc'=> $datos['formatodesc'],
			'pformatoancho'=> $datos['formatoancho'],
			'pformatoalto'=> $datos['formatoalto'],
			'pformatocarpeta'=> strtoupper($datos['formatocarpeta']),
			'pformatocrop'=> $datos['formatocrop'],
			'pformatoestado'=> $datos['formatoestado'],
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pultmodfecha'=> date("Y/m/d H:i:s")
			);
	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el formato multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	} 

//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Modifica los datos de un formato

// Parámetros de Entrada:
//			formatodesc: descripción del formato
//			formatoancho: ancho del formato
//			fomrnatoalto: alto del formato
//			formatocarpeta: formato de la carpeta
//			formatocropea: si se cropea el formato vale 1 si no vale 0
// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no
	
	protected function Modificar($datos)
	{
		$spnombre="upd_mul_multimedia_formatos_xformatocod";
		$sparam=array(
			'pformatodesc'=> $datos['formatodesc'],
			'pformatoancho'=> $datos['formatoancho'],
			'pformatoalto'=> $datos['formatoalto'],
			'pformatocarpeta'=> strtoupper($datos['formatocarpeta']),
			'pformatocrop'=> $datos['formatocrop'],
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pultmodfecha'=> date("Y/m/d H:i:s"),
			'pformatocod'=> $datos['formatocod']
			);
	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el formato multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	} 
	
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Eliminar un formato multimedia
// Parámetros de Entrada:
//		datos: arreglo de datos
//			formatocod = codigo del formato

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no

	protected function Eliminar($datos)
	{
		$spnombre="del_mul_multimedia_formatos_xformatocod";
		$sparam=array(
			'pformatocod'=> $datos['formatocod']
			);
	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar el formato multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	} 

//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Activar/Desactivar  de un banner cambiando el estado (ACTIVO/NOACTIVO)

// Parámetros de Entrada:
//		datos: arreglo de datos
//			bannercod = codigo del banner

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no	
	protected function ActivarDesactivar ($datos)
	{
		$spnombre="upd_mul_multimedia_formatos_estado_xformatocod";
		$sparam=array(
			'pformatoestado'=> $datos['formatoestado'],
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pultmodfecha'=> date("Y/m/d H:i:s"),
			'pformatocod'=> $datos['formatocod']
			);
	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al activar/desactivar el formato multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
			return false;
		}
		
		return true;
	}

}
?>