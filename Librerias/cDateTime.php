<?php 

if(!function_exists('date_diff')) {
	class DateInterval {
        public $y;
        public $m;
        public $d;
        public $h;
        public $i;
        public $s;
        public $invert;
       
        public function format($format) {
            $format = str_replace('%R%y', ($this->invert ? '-' : '+') . $this->y, $format);
            $format = str_replace('%R%m', ($this->invert ? '-' : '+') . $this->m, $format);
            $format = str_replace('%R%d', ($this->invert ? '-' : '+') . $this->d, $format);
            $format = str_replace('%R%h', ($this->invert ? '-' : '+') . $this->h, $format);
            $format = str_replace('%R%i', ($this->invert ? '-' : '+') . $this->i, $format);
            $format = str_replace('%R%s', ($this->invert ? '-' : '+') . $this->s, $format);
           
            $format = str_replace('%y', $this->y, $format);
            $format = str_replace('%m', $this->m, $format);
            $format = str_replace('%d', $this->d, $format);
            $format = str_replace('%h', $this->h, $format);
            $format = str_replace('%i', $this->i, $format);
            $format = str_replace('%s', $this->s, $format);
           
            return $format;
        }
    }

    function date_diff(DateTime $date1, DateTime $date2) {
        $diff = new DateInterval();
        if($date1 > $date2) {
            $tmp = $date1;
            $date1 = $date2;
            $date2 = $tmp;
            $diff->invert = true;
        }
       
        $diff->y = ((int) $date2->format('Y')) - ((int) $date1->format('Y'));
        $diff->m = ((int) $date2->format('n')) - ((int) $date1->format('n'));
        if($diff->m < 0) {
            $diff->y -= 1;
            $diff->m = $diff->m + 12;
        }
        $diff->d = ((int) $date2->format('j')) - ((int) $date1->format('j'));
        if($diff->d < 0) {
            $diff->m -= 1;
            $diff->d = $diff->d + ((int) $date1->format('t'));
        }
        $diff->h = ((int) $date2->format('G')) - ((int) $date1->format('G'));
        if($diff->h < 0) {
            $diff->d -= 1;
            $diff->h = $diff->h + 24;
        }
        $diff->i = ((int) $date2->format('i')) - ((int) $date1->format('i'));
        if($diff->i < 0) {
            $diff->h -= 1;
            $diff->i = $diff->i + 60;
        }
        $diff->s = ((int) $date2->format('s')) - ((int) $date1->format('s'));
        if($diff->s < 0) {
            $diff->i -= 1;
            $diff->s = $diff->s + 60;
        }
       
        return $diff;
    }
}

class cDateTime
{
	
	
	// Constructor de la clase
	function __construct(){


    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	

	
	function EsFechaMayor($datetime1,$datetime2)
	{
		$datetime1 = new DateTime($datetime1);
		$datetime2 = new DateTime($datetime2);
		//$intervalo = $datetime1->diff($datetime2);
		$intervalo = date_diff($datetime1,$datetime2);
		if ($intervalo->invert==1)
			return true;
		else
			return false;	


		
		return false;
	}


	function EsHoraMayor($time1,$time2)
	{
		$datetime1 = new DateTime("0000-00-00 ".$time1);
		$datetime2 = new DateTime("0000-00-00 ".$time2);
		//$intervalo = $datetime1->diff($datetime2);
		$intervalo = date_diff($datetime1,$datetime2);
		if ($intervalo->invert==1)
			return true;
		else
			return false;	


		
		return false;
	}
	
	
	
	function ObtenerDiadelaSemanaxMeses($date1,$date2,$semana,$diasemana)
	{
		
		$diasemananombre = cDateTime::ObtenerDiaBuscado($diasemana);
		
		$datetime1 = new DateTime($date1);
		$datetime2 = new DateTime($date2);
		//$intervalo = $datetime1->diff($datetime2);
		$intervalo = date_diff($datetime1,$datetime2);
		
		
		$arreglodatos = array();
		$meses = ($intervalo->y*12) + $intervalo->m + 1;
		
		for ($i=0; $i<=$meses; $i++)
		{
			$date = date("Y-m-d",strtotime(date("Y-m-d", strtotime($date1)) . " +".$i." month"));

			$mesnumero = date("m",strtotime($date));
			$anionumero = date("Y",strtotime($date));
			$nombremes = cDateTime::ObtenerNombreMes($mesnumero);
			
			$dia = strftime("%Y-%m-%d", strtotime($nombremes." ".$anionumero." ".$semana." ".$diasemananombre));
			
			
			if ($i==0)
			{
				if (cDateTime::EsFechaMayor($dia,$date1) || $dia==$date1)
					$arreglodatos[]=$dia;
			}elseif ($i == $meses)
			{
				if (cDateTime::EsFechaMayor($date2,$dia) || $dia==$date1)
					$arreglodatos[]=$dia;
			}else
				$arreglodatos[]=$dia;
				
			//echo "\n";
		}
		
		return $arreglodatos;
	}
	
	
	function ObtenerDiaBuscado($dia)
	{
		switch ($dia)
		{
			case "Lu":
				return "monday";
			case "Ma":
				return "tuesday";
			case "Mi":
				return "wednesday";
			case "Ju":
				return "thursday";
			case "Vi":
				return "friday";
			case "Sa":
				return "saturday";
			case "Do":
				return "sunday";
		}
		
	}
	
	function ObtenerNombreMes($numeromes)
	{
		switch ($numeromes)
		{
			case 1:
				return "jan";
			case 2:
				return "feb";
			case 3:
				return "mar";
			case 4:
				return "apr";
			case 5:
				return "may";
			case 6:
				return "jun";
			case 7:
				return "jul";
			case 8:
				return "aug";
			case 9:
				return "sep";
			case 10:
				return "oct";
			case 11:
				return "nov";
			case 12:
				return "dec";
		}
		
	}
	
} // Fin clase DateTime

?>