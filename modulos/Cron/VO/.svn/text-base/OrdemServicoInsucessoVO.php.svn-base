<?php
require_once _MODULEDIR_ . 'Cron/Lib/CronDate.php';

/**
 * VO OrdemServicoInsucesso
 * 
 * @author 	Alex S. Médice <alex.medice@meta.com.br>
 * @version 21/11/2012
 * @since   21/11/2012
 */
class OrdemServicoInsucessoVO extends CronVO {
	public $dia;
	public $mes;
	public $ano;
	public $cliente;
	public $ordem;
	public $ordemservico_tipo;
	public $contrato;
	public $placa;
	public $chassi;
	public $modelo;
	public $ultcontato;
	public $qtdinsucesso;
	
	public function __construct($data=array()) {
		
		$this->dia = date('d');
		$this->mes = CronDate::getTranslationMonth(date('m'));
		$this->ano = date('Y');
		
		parent::__construct($data);
	}
}