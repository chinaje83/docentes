<?php  
abstract class cUsuariosAccesosdb
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


	protected function BuscarUltimoAcceso($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_usuariosaccesos_xanteultimoacceso_xIdUsuario";
		$sparam=array(
			'pBase'=> BASEDATOS, 
			'pBaseAuditorias'=> BASEDATOSAUDITORIAS, 
			'pIdUsuario'=> $datos['IdUsuario']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar usuario el ultimo acceso. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		
		return true;	
	}
	

	protected function InsertarDB($datos)
	{
		$spnombre="ins_usuariosaccesos";
		$sparam=array(
			'pBaseAuditorias'=> BASEDATOSAUDITORIAS, 
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIp'=> $datos['Ip'],
			'pSistemaOperativo'=> $datos['SistemaOperativo'],
			'pNavegador'=> $datos['Navegador'],
			'pClaveEscuela'=> $datos['ClaveEscuela'],
			'pIdEscalafon'=> $datos['IdEscalafon'],
			'prolcod'=> $datos['rolcod'],
            'pIdArea'=> $datos['IdArea'],
            'pNumeroDistrito'=> $datos['NumeroDistrito'],
            'pMetapuestos' =>  $datos['Metapuestos'],
            'pTipoLogin'=> $datos['TipoLogin'],
			'pFechaMovimiento'=> $datos['FechaMovimiento']
			);	
		
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) || $numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al dar de alta el acceso del usuario. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
			return false;
		}
		
		return true;
	
	}
	

}


?>