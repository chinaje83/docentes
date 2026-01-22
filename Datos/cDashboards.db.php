<?php

abstract class cDashboardsDB {
    use ManejoErrores;

    protected $conexion;
    protected $formato;
    protected $conexionES;

    function __construct($conexion, $formato = FMT_TEXTO) {
        $this->conexion = &$conexion;
        $this->formato = &$formato;

    }

    function __destruct() {}
}