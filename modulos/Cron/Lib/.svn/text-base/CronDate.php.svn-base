<?php
/**
 * @CronDate.php
 * 
 * Classe com métodos úteis para manipulação de datas
 * 
 * @author 	Alex S. Médice <email   alex.medice@meta.com.br>
 * @version 21/11/2012
 * @since   21/11/2012
 */
class CronDate {
	public static $months = array(
		'br' => array(1=>"Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro")
	);
		
	public static function getTranslationMonth($month, $lang='br') {
		$month = (int) $month;
		
    	if (!isset(self::$months[$lang])) {
    		throw new Exception('Não existe tradução para: ' . $lang);
    	}
    	if (!isset(self::$months[$lang][$month])) {
    		throw new Exception('Não existe tradução para o mês: ' . $lang);
    	}
    	
    	return self::$months[$lang][$month];
	}
}