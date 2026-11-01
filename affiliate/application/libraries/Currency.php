<?php
class Currency {
	private $currencies = array();
	public function __construct() {
		$this->CI =& get_instance();
		$query = $this->CI->db->query("SELECT * FROM currency")->result_array();
		foreach ($query as $result) {
			$this->currencies[$result['code']] = array(
				'currency_id'   => $result['currency_id'],
				'title'         => $result['title'],
				'symbol_left'   => $result['symbol_left'],
				'symbol_right'  => $result['symbol_right'],
				'decimal_place' => $result['decimal_place'],
				'value'         => $result['value']
			);
		}
	}

	public function format($number, $format = true, $currency, $_decimal_point = false,$replace_comma_symbol,$decimal_symbol) {
		$symbol_left = $this->currencies[$currency]['symbol_left'];
		$symbol_right = $this->currencies[$currency]['symbol_right'];
		$decimal_place = $this->currencies[$currency]['decimal_place'];

		if($_decimal_point !== false){
			$decimal_place = $_decimal_point;
		}

		$value = '';
		if(!$value)
			$value = $this->currencies[$currency]['value'];

		$amount = $value ? (float) $number * $value : (float) $number;
  
  		if(is_int($amount)==false)

		if (!$format) {
			$amount = number_format($amount, (int)$decimal_place);
			return $amount;
		}
		else
			$amount = number_format($amount, (int)$decimal_place,$decimal_symbol,$replace_comma_symbol);

		//echo "ss:".$decimal_place.":ss";
		
		if($decimal_place>0)
		{
			$value=explode($decimal_symbol,$amount);
			$ccountvalue=count($value); 
			if($ccountvalue>1)
			{
				$lastvalue=end($value);
				$lenlast=strlen($lastvalue);
				if($lastvalue>0)
				{}
				else
				{
					$value1=array_pop($value);
					$amount=substr($amount,0,(-1-$lenlast));
				}
			}
		}
		
		$string = '';
		if ($symbol_left) {
			$string .= $symbol_left;
		}
		$string .= $amount;
		 
		if ($symbol_right) {
			$string .= $symbol_right;
		}
		return $string;
	}

	 

	public function convert($value, $from, $to) {
		if (isset($this->currencies[$from])) {
			$from = $this->currencies[$from]['value'];
		} else {
			$from = 1;
		}
		if (isset($this->currencies[$to])) {
			$to = $this->currencies[$to]['value'];
		} else {
			$to = 1;
		}
		return $value * ($to / $from);
	}
	
	public function getId($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['currency_id'];
		} else {
			return 0;
		}
	}
	public function getSymbolLeft($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_left'];
		} else {
			return '';
		}
	}
	public function getSymbolRight($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_right'];
		} else {
			return '';
		}
	}
	public function getSymbol() {
		$currency = $_SESSION['userCurrency'];

		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_left'] ? $this->currencies[$currency]['symbol_left'] : $this->currencies[$currency]['symbol_right'];
		} else {
			return '';
		}
	}
	public function getDecimalPlace($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['decimal_place'];
		} else {
			return 0;
		}
	}
	public function getValue($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['value'];
		} else {
			return 0;
		}
	}
	public function has($currency) {
		return isset($this->currencies[$currency]);
	}
}

	function c_format($amount = 0, $format = true, $decimal_place = false) {
	    $CI =& get_instance();
	    $currency = $_SESSION['userCurrency'];
	    $replace_comma_symbol = ",";
	    $decimal_symbol = ".";

	    // Fetch setting for shortening numbers
	    $enable_shorten_row = $CI->db->query("SELECT * FROM setting WHERE setting_key='enable_shorten_numbers' AND setting_type='site'")->row();
	    $enable_shorten = isset($enable_shorten_row->setting_value) ? $enable_shorten_row->setting_value : 0;

	    if ($currency != "") {
	        $default_currency = $CI->db->query("SELECT * FROM currency WHERE code='".$currency."'")->row_array();
	        if ($default_currency) {
	            $decimal_symbol = $default_currency['decimal_symbol'];
	            $replace_comma_symbol = $default_currency['replace_comma_symbol'];
	        }
	    } else {
	        $default_currency = $CI->db->query("SELECT * FROM currency WHERE is_default=1")->row_array();
	        if ($default_currency) {
	            $currency = $_SESSION['userCurrency'] = $default_currency['code'];
	            $replace_comma_symbol = $default_currency['replace_comma_symbol'];
	            $decimal_symbol = $default_currency['decimal_symbol'];
	        }
	    }

	    $shorten_suffix = "";
	    $shorten_amount = $amount;

	    if ($enable_shorten == 1) {
	        if ($amount >= 1000 && $amount < 1000000) {
	            $shorten_amount = round($amount / 1000, 1);
	            $shorten_suffix = "k";
	        } elseif ($amount >= 1000000) {
	            $shorten_amount = round($amount / 1000000, 1);
	            $shorten_suffix = "M";
	        }
	    }

	    $formatted_amount = $CI->currency->format($shorten_amount, $format, $currency, $decimal_place, $replace_comma_symbol, $decimal_symbol, false);

	    return ($shorten_suffix === "") ? $formatted_amount : $formatted_amount . $shorten_suffix;
	}

	function c_format_nosym($amount = 0){
		return !empty($amount) ? number_format($amount, 2) : 0;
	}

	function getPercentage_format($number){
		$decimal_place = $this->currencies[$currency]['decimal_place'];
		return number_format($number, (int)$decimal_place);
	}