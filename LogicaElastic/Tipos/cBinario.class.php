<?php


namespace Elastic\Tipos;


use stdClass;

/**
 * @property bool doc_values
 * @property bool store
 */
class Binario
{
	use CamposDinamicos;
	public $type = 'binary';
}
