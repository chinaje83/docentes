<?php


namespace Elastic\Tipos;


use stdClass;

/**
 * @property stdClass fields
 * @property bool     index
 * @property string   normalizer
 * @property integer  ignore_above
 */
class Keyword
{
	use CamposDinamicos;
	use SubCampo;
	public $type = 'keyword';
	
	public function __construct(?int $ignore_above = NULL) {
		if(!empty($ignore_above))
			$this->ignore_above = $ignore_above;
	}

	public function setNormalizer(string $normalizer): self {
	    $this->normalizer = $normalizer;
	    return $this;
    }
}