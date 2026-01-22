<?php 
include(DIR_CLASES_DB."cDocumentosCategorias.db.php");

class cDocumentosCategorias extends cDocumentosCategoriasdb
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
			'xIdCategoria'=> 0,
			'IdCategoria'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdCategoria DESC"
			);

		
		if(isset($datos['IdCategoria']) && $datos['IdCategoria']!="")
		{
			$sparam['IdCategoria']= $datos['IdCategoria'];
			$sparam['xIdCategoria']= 1;
		}
		
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		
		if(isset($datos['Estado']) && $datos['Estado']!="")
		{
			$sparam['Estado']= $datos['Estado'];
			$sparam['xEstado']= 1;
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