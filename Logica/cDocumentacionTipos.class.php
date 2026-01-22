<?php 
include(DIR_CLASES_DB."cDocumentacionTipos.db.php");

class cDocumentacionTipos extends cDocumentacionTiposdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdDocumentoTipo'=> 0,
			'IdDocumentoTipo'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'xExtension'=> 0,
			'Extension'=> "",
			'limit'=> "",
			'orderby'=> "IdDocumentoTipo ASC"
		);


		if(isset($datos['IdDocumentoTipo']) && $datos['IdDocumentoTipo']!="")
		{
			$sparam['IdDocumentoTipo']= $datos['IdDocumentoTipo'];
			$sparam['xIdDocumentoTipo']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['Extension']) && $datos['Extension']!="")
		{
			$sparam['Extension']= $datos['Extension'];
			$sparam['xExtension']= 1;
		}
		
		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}


}
?>