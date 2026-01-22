<?php


namespace Elastic\Tipos;


use stdClass;

class Coordenadas
{
	use CamposDinamicos;
	use SubCampo;
	public $type = 'geo_point';
}