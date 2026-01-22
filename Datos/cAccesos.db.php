<?php 
abstract class cAccesosdb
{


	function __construct(){}

	function __destruct(){}

	

	
	protected function Insertar($datos)
	{
		$spnombre="ins_Accesos";
		$sparam=array(
			'pBaseAccesos'=> BASEDATOSACCESOS,  
			'pUrlPagina'=> $datos['UrlPagina'],
			'pModuloCte'=> $datos['ModuloCte'],
			'pModulo'=> $datos['Modulo'],
			'pDatosAnexos'=> $datos['DatosAnexos'],
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIp'=> $datos['Ip'],
			'pSistemaOperativo'=> $datos['SistemaOperativo'],
			'pNavegador'=> $datos['Navegador'],
			'pLat'=> $datos['Lat'],
			'pLng'=> $datos['Lng'],
			'pFechaAcceso'=> $datos['FechaAcceso']
			);
		
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		
		return true;
	}

	

}
?>