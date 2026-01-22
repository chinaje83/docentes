<?php 
class cDashboardProcesar
{
	
	protected $conexion;
	protected $formato;
	protected $previsualizar;
	// Constructor de la clase
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		$this->previsualizar = false;
		$this->editarModulos = false;
		
    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	

//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 

	public function RecargarModulo($datos,&$html_generado)
	{
		//print_r($datos);die;
		$html_generado='';
		$oObjeto= new cUsuariosZonasDashboard($this->conexion);
		$datosbusqueda['IdUsuarioDashboard'] = $datos['IdUsuarioDashboard'];
		if(!$oObjeto->BuscarxCodigo($datosbusqueda,$resultado,$numfilas))
			return false;
	
		if($numfilas>0){
			$datosmodulo = $this->conexion->ObtenerSiguienteRegistro($resultado);
			if(!$this->CargarModulo($datosmodulo,$html_generado))
				return false;		
		}
		
		return true;
	}
	
	
	private function CargarModulo($datosModulo,&$html_generado)
	{
		$datosModulo['conexion'] = $this->conexion;
		if (!$this->previsualizar)
		{
			$datosModulo['htmledit'] = '<div class="modules_header" id="tools_'.$datosModulo['IdUsuarioDashboard'].'"> <div class="pull-left"><i class="fa fa-arrows"></i></div>';
			if($this->editarModulos)
				$datosModulo['htmledit'] .= '	<div class="modules_edit pull-right"><a href="#Top" onclick="AbrirEditarModulos('.$datosModulo['IdUsuarioDashboard'].')" title="Editar" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i>&nbsp;Editar</a></div>';
			$datosModulo['htmledit'] .= '	<div class="modules_delete pull-right"><a class="btn btn-xs btn-danger" href="javascript:void(0)" onclick="EliminarModulo('.$datosModulo['IdUsuarioDashboard'].')" title="Eliminar"><i class="fa fa-trash"></i>&nbsp;Eliminar</a></div>';		
			//$datosModulo['htmledit'] .= '	<div class="modules_move"><a href="javascript:void(0)" title="Mover">Mover</a></div>';
			$datosModulo['htmledit'] .= '</div><div style="clear:both"></div>';
			//$datosModulo['mouseaction'] = 'onmouseout="hideTools(\'tools_'.$datosModulo['IdUsuarioDashboard'].'\');" onmouseover="viewTools(\'tools_'.$datosModulo['IdUsuarioDashboard'].'\');"';
			$datosModulo['mouseaction'] = "";
			
		}else
		{
			$datosModulo['mouseaction'] = "";
			$datosModulo['htmledit'] = "";
		}
		//print_r($datosModulo);die;
		$htmlModuleRender = FuncionesPHPLocal::RenderFile("modulos_dashboard/html/{$datosModulo['Archivo']}.php",$datosModulo);
		$html = $this->ProcesarHtmlInterno($htmlModuleRender);
		$html_generado .= $html;
		
		return true;
	}



	function Procesar($datosregistro,&$html)
	{
		$arreglozonas = array();
		
		$oFormulariosPasosMacros = new cUsuariosMacrosDashboard($this->conexion,$this->formato);
		$oUsuariosZonasDashboard = new cUsuariosZonasDashboard($this->conexion,$this->formato);
		
		$oFormulariosPasosMacrosZonas = new cUsuariosMacrosDashboardZonas($this->conexion,$this->formato);
		if(!$oFormulariosPasosMacros->BuscarMacros($datosregistro,$resultado,$numfilas))
			return false;
			
		$html = "";
		$html .= '<div class="macrossite">';
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			//print_r($fila);continue;
			if(!$oFormulariosPasosMacrosZonas->BuscarZonasxMacros($fila,$resultadoZona,$numfilasZona))
				return false;
			$html .= '<div class="macros '.$fila['Clase'].'" id="macro_'.$fila['IdUsuarioMacro'].'">';
			$html .= '<div class="macros_header">';
			$html .= '<div class="col-md-6">';
			$html .= '<div class="moverMacro"><i class="fa fa-arrows"></i></div>';
			$html .= '</div>';
			$html .= '<div class="col-md-6">';
			$html .= '<div class="eliminarMacro"><a class="btn btn-xs btn-danger" href="javascript:void(0)" onclick="EliminarMacro('.$fila['IdUsuarioMacro'].')" title="Eliminar"><i class="fa fa-trash"></i>&nbsp;Eliminar</a></div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="clearboth">&nbsp;</div>';
			while($filaZona = $this->conexion->ObtenerSiguienteRegistro($resultadoZona))
			{
			    $html .= '<div class="estructuras '.$filaZona['Clase'].'" id="zona_'.$filaZona['IdZona'].'">';
				$datosbusquedaZona['IdZona'] = $filaZona['IdZona'];
				if(!$oUsuariosZonasDashboard->BusquedaAvanzada($datosbusquedaZona,$resultadoModulos,$numfilasModulos))
					return false;
				while ($datosModulo = $this->conexion->ObtenerSiguienteRegistro($resultadoModulos))
				{
					if(!$this->CargarModulo($datosModulo,$html))
						return false;										
				}
				$html .= '</div>';
			}
			$html .= '<div class="clearboth">&nbsp;</div></div>';
			
		}//die;
			$html .= '<div class="clearboth">&nbsp;</div></div>';
		return true;
		
	}
	
	private function ProcesarHtmlInterno($htmlModuleRender)
	{
		$html = "";
		cSepararHTML::ProcesarHTML($htmlModuleRender,$partes);
		foreach($partes as $partehtml)
		{
			if(is_array($partehtml))
			{
			}else
				$html .= $partehtml;
		}

		return $html;
	}



	function MostrarDashboard($datosregistro,&$html)
	{
		$this->previsualizar = true;
		$arreglozonas = array();
		
		$oFormulariosPasosMacros = new cUsuariosMacrosDashboard($this->conexion,$this->formato);
		$oUsuariosZonasDashboard = new cUsuariosZonasDashboard($this->conexion,$this->formato);
		
		$oFormulariosPasosMacrosZonas = new cUsuariosMacrosDashboardZonas($this->conexion,$this->formato);
		if(!$oFormulariosPasosMacros->BuscarMacros($datosregistro,$resultado,$numfilas))
			return false;
			
		$html = "";
		$html .= '<div class="macrossite">';
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			//print_r($fila);continue;
			if(!$oFormulariosPasosMacrosZonas->BuscarZonasxMacros($fila,$resultadoZona,$numfilasZona))
				return false;
			while($filaZona = $this->conexion->ObtenerSiguienteRegistro($resultadoZona))
			{
			    $html .= '<div class="estructuras '.$filaZona['Clase'].'" id="zona_'.$filaZona['IdZona'].'">';
				$datosbusquedaZona['IdZona'] = $filaZona['IdZona'];
				if(!$oUsuariosZonasDashboard->BusquedaAvanzada($datosbusquedaZona,$resultadoModulos,$numfilasModulos))
					return false;
				while ($datosModulo = $this->conexion->ObtenerSiguienteRegistro($resultadoModulos))
				{
					if(!$this->CargarModulo($datosModulo,$html))
						return false;										
				}
				$html .= '</div>';
			}
			$html .= '<div class="clearboth">&nbsp;</div></div>';
			
		}//die;
			$html .= '<div class="clearboth">&nbsp;</div></div>';
		return true;
		
	}
	
}//FIN CLASE

?>