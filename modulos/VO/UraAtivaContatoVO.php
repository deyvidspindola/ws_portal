<?php

require_once _MODULEDIR_ . 'Atendimento/VO/AtendimentoVO.php';

/**
 * Armazena os dados do contato para envio ao discador
 * 
 * @author 	Alex S. Médice <alex.medice@meta.com.br>
 * @version 28/03/2013
 * @version 28/03/2013
 */
class UraAtivaContatoVO extends AtendimentoVO {
	
	public $connumero; 				# Contrato
	public $id_campanha; 			# [campanhas_ura_ativa.cuaidcampanha]
	public $complemento; 			# [panicos_pendentes.oid]#[panicos_pendentes.papveioid]#[telefoneContato]
	public $id_contato_externo; 	# [panicos_pendentes.papveioid]
	public $nome; 					# Nome do contato ou da gerenciadora
	public $data_agendamento; 		# [NULL]
	public $hora_ini_agendamento; 	# [VAZIO]
	public $hora_fim_agendamento; 	# [VAZIO]
	public $ramal_conta; 			# [VAZIO]
	public $id_telefone_externo; 	# [2[telefone_contato.tctoid]] || [1[gerenciadora.geroid]]
	public $telefone; 				# Numero do telefone
	public $tipo; 					# 1 – Gerenciadora 2 – Emergência 3 - Assistência	
	public $num_prioridade = 0; 	# Numero da prioridade Deve ser....
	
}