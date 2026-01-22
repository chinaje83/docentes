<?php

include(DIR_CLASES_DB."cMovimientosTmp.db.php");
class cMovimientosTmp extends cMovimientosTmpdb
{
	/**
	 * Constructor de la clase cAmbitos.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion,$formato=FMT_TEXTO){
		parent::__construct($conexion,$formato);
	}
	/**
	 * Destructor de la clase cAmbitos.
	 */
	function __destruct(){
		parent::__destruct();
	}
	/**
	 * Devuelve el mensaje de error almacenado
	 *
	 * @return array
	 */
	public function getError(): array {
		return $this->error;
	}

    public function BuscarxIdLogMovimientos($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarxIdLogMovimientos($datos, $resultado, $numfilas);
    }


    public function InsertarLogNovedadTmp($datos, &$codigoInsertado): bool {

        self::_setearFechas($datos);
        self::_setearNull($datos);
        return parent::InsertarLogNovedadTmp($datos, $codigoInsertado);
    }

    public function EliminarxIdLogMovimientos($datos):bool {
        return parent::EliminarxIdLogMovimientos($datos);
    }


    private static function _setearFechas(&$datos): void {

        if (!\FuncionesPHPLocal::isEmpty($datos['FechaAlta']))
            $datos['FechaAlta'] = \FuncionesPHPLocal::ConvertirFecha($datos['FechaAlta'], 'dd-mm-aaaa', 'aaaa/mm/dd');

        if (!\FuncionesPHPLocal::isEmpty($datos['FechaBaja']))
            $datos['FechaBaja'] = \FuncionesPHPLocal::ConvertirFecha($datos['FechaBaja'], 'dd-mm-aaaa', 'aaaa/mm/dd');

        if (!\FuncionesPHPLocal::isEmpty($datos['FechaMovimiento']))
            $datos['FechaMovimiento'] = \FuncionesPHPLocal::ConvertirFecha($datos['FechaMovimiento'], 'dd-mm-aaaa', 'aaaa/mm/dd');

        if (!\FuncionesPHPLocal::isEmpty($datos['FechaLiquidacion']))
            $datos['FechaLiquidacion'] = \FuncionesPHPLocal::ConvertirFecha($datos['FechaLiquidacion'], 'dd-mm-aaaa', 'aaaa/mm/dd');


        $fecha_carga = explode('.', $datos['FechaCarga']);
        $fecha_carga = explode(' ', $fecha_carga[0] );
        $datos['FechaCarga'] = \FuncionesPHPLocal::ConvertirFecha($fecha_carga[0], 'dd-mm-aaaa', 'aaaa/mm/dd').' '.$fecha_carga[1];
    }

    private static function _setearNull(&$datos): void {

        if (\FuncionesPHPLocal::isEmpty($datos['Orden']))
            $datos['Orden'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['idSubServicioNovTGE']))
            $datos['idSubServicioNovTGE'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['FechaAlta']))
            $datos['FechaAlta'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['FechaBaja']))
            $datos['FechaBaja'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['FechaReintegro']))
            $datos['FechaReintegro'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['idLicencia']))
            $datos['idLicencia'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['idServicioTGEQueSuple']))
            $datos['idServicioTGEQueSuple'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['idServicioTGERelacionado']))
            $datos['idServicioTGERelacionado'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['CausaAlta']))
            $datos['CausaAlta'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['CausaBaja']))
            $datos['CausaBaja'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdRevistaAntigua']))
            $datos['IdRevistaAntigua'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdRevistaNueva']))
            $datos['IdRevistaNueva'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['idSubServicioLicTGE']))
            $datos['idSubServicioLicTGE'] = 'NULL';


        if (\FuncionesPHPLocal::isEmpty($datos['IdPersona']))
            $datos['IdPersona'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdPuesto']))
            $datos['IdPuesto'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdArticulo']))
            $datos['IdArticulo'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['BajaLiquidacion']))
            $datos['BajaLiquidacion'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdTipoDocumento']))
            $datos['IdTipoDocumento'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdPuestoDestino']))
            $datos['IdPuestoDestino'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['idPlaza']) || $datos["idPlaza"]=="")
            $datos['idPlaza'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdEscuela']) || $datos["IdEscuela"]=="")
            $datos['IdEscuela'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdServicioTGE']) || $datos["IdServicioTGE"]=="")
            $datos['IdServicioTGE'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdSubServicioNovTGE']) || $datos["IdSubServicioNovTGE"]=="")
            $datos['IdSubServicioNovTGE'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['EstadoNovedad']) || $datos["EstadoNovedad"]=="")
            $datos['EstadoNovedad'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdEstadoNovedad']) || $datos["IdEstadoNovedad"]=="")
            $datos['IdEstadoNovedad'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['FechaLiquidacion']) || $datos["FechaLiquidacion"]=="")
            $datos['FechaLiquidacion'] = 'NULL';

    }


}