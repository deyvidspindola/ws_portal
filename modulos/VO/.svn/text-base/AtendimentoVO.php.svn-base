<?php
/**
 * VO padrão do Atendimento
 * 
 * @author 	Alex S. Médice <alex.medice@meta.com.br>
 * @since   22/04/2013
 */
class AtendimentoVO {
	public function __construct($data=array()) {
		$data = (array) $data;
		
		foreach ($data as $key => $val) {
			$this->$key = is_string($data[$key]) ? trim($data[$key]) : $data[$key];
		}
	}
}