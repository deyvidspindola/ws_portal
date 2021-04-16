<?php

/**
 * Arquivo DAO responsável pelas requisições ao banco de dados
 */
require _MODULEDIR_ . 'Manutencao/DAO/ManOcorrenciaDAO.php';

class ManOcorrencia {

	private $dao;
	
	public function buscaAcionamentosPorIdOcorrencia($id_ocorrencia) {
		
		$ocorrencia_acionamento = array();
	
		if(!empty($id_ocorrencia)) {
		
			$acionamentos = $this->dao->buscaAcionamentosPorIdOcorrencia($id_ocorrencia);
			
			if(count($acionamentos) > 0) {
			
				foreach($acionamentos as $acionamento) {
				
					$ocorrencia_acionamento[] = array(
						'id_pronta_resposta' => $acionamento['id_pronta_resposta'],
						'equipe' 			 => $acionamento['equipe'],
						'data' 				 => $acionamento['data'],
						'tipo' 				 => $acionamento['tipo'],
						'cliente' 			 => $acionamento['cliente'],
						'uf' 				 => $acionamento['uf'],
						'cidade' 			 => $acionamento['cidade'],
						'zona' 				 => $acionamento['zona'],
						'bairro' 			 => $acionamento['bairro'],
						'is_recuperado' 	 => ($acionamento['is_recuperado'] == 1) ? 'Sim' : 'Não'
					);
				}
			}
		}
		
		return $ocorrencia_acionamento;
		
	}
	
	public function __construct() {
        
        $this->dao = new ManOcorrenciaDAO();
        
    }

}