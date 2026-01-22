<?php 
class cSunaElastic
{
	
	protected $conexion;
	protected $formato;
	protected $previsualizar;
	// Constructor de la clase
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		$this->previsualizar = false;
		
    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	

//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 


	public function GenerarSuna(&$result)
	{
		
		$oMappingElastic = new cMappingElastic(INDICESUNA);
		if(!$oMappingElastic->CrearIndice())
			return false;

		$oMappingElasticAuditoria = new cMappingElastic(INDICEAUDITORIA);
		if(!$oMappingElasticAuditoria->CrearIndice())
			return false;

		$oMappingTablas = new cMappingElastic(INDICETABLAS);
		if(!$oMappingTablas->CrearIndice())
			return false;
		
		$oMappingTablas = new cMappingElastic(INDICELICENCIAS);
		if(!$oMappingTablas->CrearIndice())
			return false;
			

		return true;
	}
	

	public function EliminarSuna(&$result)
	{
		
		$oMappingElastic = new cMappingElastic(INDICESUNA);
		if(!$oMappingElastic->EliminarIndice())
			return false;

		$oMappingElasticAuditoria = new cMappingElastic(INDICEAUDITORIA);
		if(!$oMappingElasticAuditoria->EliminarIndice())
			return false;
			
		$oMappingTablas = new cMappingElastic(INDICETABLAS);
		if(!$oMappingTablas->EliminarIndice())
			return false;
		
		$oMappingTablas = new cMappingElastic(INDICELICENCIAS);
		if(!$oMappingTablas->EliminarIndice())
			return false;
			

		return true;
	}
	


	public function MappingSuna(&$result)
	{
		
		$oMappingElastic = new cMappingElastic(INDICESUNA);
		$oMappingElasticAuditoria = new cMappingElastic(INDICEAUDITORIA);
		$oMappingTablas = new cMappingElastic(INDICETABLAS);
		$oMappingLicencias = new cMappingElastic(INDICELICENCIAS);
		
		if(!$oMappingElastic->Mapping())
			return false;

		if(!$oMappingElasticAuditoria->Mapping())
			return false;

		if(!$oMappingTablas->Mapping())
			return false;
		
		if(!$oMappingLicencias->Mapping())
			return false;

		return true;
	}
	




}//FIN CLASE

?>