<?php

require_once _MODULEDIR_ . 'Atendimento/VO/AtendimentoVO.php';

/**
 * Armazena os dados do contato para envio ao discador
 * 
 * @author 	Alex S. Médice <alex.medice@meta.com.br>
 * @version 28/03/2013
 * @version 28/03/2013
 */
class UraAtivaContratoVO extends AtendimentoVO {
	/**
	 * Código identificador do registro
	 * Panico => OID do panico
	 * Estatistica => Placa|Contrato do veículo
	 * Assistencia => OID da OS
	 * @var int
	 */
	public $codigo;
	public $connumero;
	public $conno_tipo;
	public $concsioid;
	public $conclioid;
	public $veipdata;
	public $conveioid; 
	public $conequoid;
	public $vegstatus;
	public $conegaoid;
}