<?php
require_once DIR_CLASES_DB . 'cSolicitudSC.db.php';

class cSolicitudSC extends cSolicitudSCDB
{
    /**
     * cParentescos constructor.
     *
     * @param accesoBDLocal $conexion
     * @param               $formato
     */
    public function __construct(accesoBDLocal $conexion, $formato=FMT_ARRAY) {
        parent::__construct($conexion, $formato);
    }

    /**
     * @inheritDoc
     */
    public function __destruct()
    {
        parent::__destruct();
    }


    public function buscarListado($datos,&$resultado,&$numfilas)
    {
        $sparam = array(
            'xTipo_cargo'   => 0,
            'Tipo_cargo'    => "",
            'xNiveles'   => 0,
            'Niveles'    => "",
            'xVacante'   => 0,
            'Vacante'    => "",
            'xUrgente'   => 0,
            'Urgente'    => "",
            'xCampo_constante'   => 0,
            'Campo_constante'    => "",
            'limit'=> "",
            'orderby'=> "",
        );

        if (isset($datos['Tipo_cargo']) && $datos['Tipo_cargo']!="") {
            $sparam['Tipo_cargo']  = $datos['Tipo_cargo'];
            $sparam['xTipo_cargo'] = 1;
        }

        if (isset($datos['Niveles']) && $datos['Niveles']!="") {
            $sparam['Niveles']  = $datos['Niveles'];
            $sparam['xNiveles'] = 1;
        }

        if (isset($datos['Vacante']) && $datos['Vacante']!="") {
            $sparam['Vacante']  = $datos['Vacante'];
            $sparam['xVacante'] = 1;
        }

        if (isset($datos['Urgente']) && $datos['Urgente']!="") {
            $sparam['Urgente']  = $datos['Urgente'];
            $sparam['xUrgente'] = 1;
        }

        if (isset($datos['Campo_constante']) && $datos['Campo_constante']!="") {
            $sparam['Campo_constante']  = $datos['Campo_constante'];
            $sparam['xCampo_constante'] = 1;
        }

        if (isset ($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

        if (isset ($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

        if (!parent::buscarListado($sparam,$resultado,$numfilas))
            return false;
        return true;
    }
	
	public function buscarXCodigo($datos, &$resultado, &$numfilas)
    {
	    return parent::buscarXCodigo($datos, $resultado, $numfilas);
    }


    public function listarConstantes($datos, &$resultado, &$numfilas)
    {
	    return parent::listarConstantes($datos, $resultado, $numfilas);
    }


    public function Modificar($datos)
    {
        if (!parent::Modificar($datos))
            return false;

        return true;
    }


    public function Insertar($datos, &$codigoinsertado)
    {
        if (!parent::Insertar($datos, $codigoinsertado))
            return false;

        return true;
    }

}