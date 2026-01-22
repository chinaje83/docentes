<?php


namespace Elastic\Tipos;


use stdClass;

class Entero
{
	use CamposDinamicos;
	use SubCampo;
	public $type = 'integer';
}