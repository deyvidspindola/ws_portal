<?php

namespace module\EscritorRemessaRPSBarueri;

class EscritorComumRPSBarueriModel {

    public function formatNumeric($value, $size, $decimals = null, $padZeros = true){

		if(!empty($value) && !is_null($decimals)){
			$value = number_format($value, $decimals, "", "");
		}

		return empty($value) && $padZeros == false ? str_repeat(" ", $size) : str_pad($value, $size, "0", STR_PAD_LEFT);

	}

	public function formatString($value, $size){
		return str_pad(substr($value, 0, $size), $size);
	}

    public function formatAlphanumeric($value, $size){

		$value = preg_replace("/[^a-zA-Z0-9 ]/", "", strtr($value, "АЮЦБИЙМСТУЗЭГаюцбиймстузэг", "aaaaeeiooouucAAAAEEIOOOUUC"));
		$value = strtoupper($value);

		return str_pad(substr($value, 0, $size), $size);
	}

	public function formatDate($value){
		return !empty($value) ? $this->formatNumeric(date('Ymd', strtotime($value)), 8) : $this->formatString($value, 8);
    }
	
	public function formatTime($value){
		return $this->formatNumeric(date('His', strtotime($value)), 6);
	}
}

?>