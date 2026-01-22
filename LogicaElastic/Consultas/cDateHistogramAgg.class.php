<?php


namespace Elastic\Consultas;


use Bigtree\ExcepcionES;
use stdClass;

class DateHistogramAgg extends Agg {

    public $date_histogram;

    public const CALENDAR = 'calendar';
    public const FIXED = 'fixed';

    private const ACCEPTED_CALENDAR_INTERVALS = ['minute', '1m', 'hour', '1h', 'day', '1d', 'week', '1w', 'month', '1M', 'quarter', '1q', 'year', '1y'];
    private const ACCEPTED_FIXED_INTERVALS = /** @lang PhpRegExp */ '/\d+(?:ms|s|m|h|d)/';


    /**
     * @param string $campo
     * @param string $interval
     * @param string $interval_type
     *
     * @throws \Bigtree\ExcepcionES
     */
    public function __construct(string $campo, string $interval, string $interval_type) {
        $this->date_histogram = new stdClass();
        $this->date_histogram->field = $campo;
        switch($interval_type) {
            case self::CALENDAR:
                if (!in_array($interval, self::ACCEPTED_CALENDAR_INTERVALS))
                    throw new ExcepcionES('Intervalo de calendario incorrecto');
                $this->date_histogram->calendar_interval = $interval;
                break;
            case self::FIXED:
                if (!preg_match(self::ACCEPTED_FIXED_INTERVALS, $interval))
                    throw new ExcepcionES('Intervalo fijo incorrecto');
                $this->date_histogram->fixed_interval = $interval;
                break;
            default:
                throw new ExcepcionES('Tipo de intervalo incorrecto');
        }

    }

    /**
     * @param string $formato
     *
     * @return $this
     */
    public function setFormat(string $formato): self {
        $this->date_histogram->format = $formato;
        return $this;
    }
}