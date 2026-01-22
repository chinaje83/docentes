<?php


namespace Elastic\Tipos;


use stdClass;

class Doble
{
	use CamposDinamicos;
	use SubCampo;
	public $type = 'double';
}