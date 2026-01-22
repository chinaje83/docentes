<?php


namespace Elastic\Tipos;


use stdClass;

class Booleano
{
	use CamposDinamicos;
	use SubCampo;
	public $type = 'boolean';
}