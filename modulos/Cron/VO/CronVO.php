<?php
/**
 * VO padrão do Cron
 * 
 * @author 	Alex S. Médice <alex.medice@meta.com.br>
 * @version 20/11/2012
 * @since   20/11/2012
 */
class CronVO {
	public function __construct($data=array()) {
		$data = (array) $data;
		
		foreach ($data as $key => $val) {
			$this->$key = is_string($data[$key]) ? trim($data[$key]) : $data[$key];
		}
	}
}