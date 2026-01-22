<?php

/**
 * Class cProcesarLiquidacionDB
 */
class cProcesarLiquidacionDB {
	use ManejoErrores;
	
	protected $conexion;
	
	public function __construct(accesoBDLocal $conexion) {
		$this->conexion = &$conexion;
	}
}