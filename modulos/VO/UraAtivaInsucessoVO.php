<?php

require_once _MODULEDIR_ . 'Atendimento/VO/AtendimentoVO.php';

/**
 * Armazena os dados do contato para envio ao discador
 * 
 * @author 	Alex S. Médice <alex.medice@meta.com.br>
 * @version 28/03/2013
 * @version 28/03/2013
 */
class UraAtivaInsucessoVO extends AtendimentoVO {
	public $ID_CAMPANHA;
	public $ID_CONTATO_EXTERNO;
	public $DT_INICIAL;
	public $MOTIVO_ENCERRAM;
	public $ID_TELEFONE_EXTERNO;
	public $TELEFONE;
}