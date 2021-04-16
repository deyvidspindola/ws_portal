<?php

require_once _MODULEDIR_ . 'Atendimento/VO/AtendimentoVO.php';

/**
 * Armazena os dados do cliente
 * 
 * @author 	Renato Teixeira Bueno <renato.bueno@meta.com.br>
 * @version 28/03/2013
 * @version 28/03/2013
 */
class UraAtivaClienteVO extends AtendimentoVO {
	
	public $nome;
	public $email;
	public $email_nfe;
}