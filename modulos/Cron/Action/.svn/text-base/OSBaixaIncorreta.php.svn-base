<?php

/**
 * Classe responsável pelas ações de busca de dados 
 * de OS cujas baixas de estoque são consideradas 
 * incorretas e armazena em BD.
 * @author Marcello Borrmann <marcello.b.ext@sascar.com.br>
 * @since 01/10/2015
 * @category Class
 * @package OSBaixaIncorreta
 *
 */

require_once _MODULEDIR_ . 'Cron/DAO/OSBaixaIncorretaDAO.php';

class OSBaixaIncorreta {

    private $dao;
	
	/**
	 * Realiza as ações de busca de dados 
	 * de OS com baixas de estoque incorretas
	 * 
	 * @param
	 * @return boolean
	 */	
	public function gerarDadosOSIncorreta() {
		try{
			
			
			$dt_base = date('Y-m-d', strtotime("-1 day"));
			//$dt_base = '2015-08-01';
			
			// Inicia transação
			$this->dao->begin();
			
			// Busca quantidades baixadas			
			if (!$this->dao->gerarDadosQtdBaixada($dt_base)) {
				throw new Exception('Erro ao buscar dados da quantidade baixada.') ;
			}
			// Busca quantidades necessárias de acordo com o projeto
			if (!$this->dao->gerarDadosQtdNecessariaEpp($dt_base)) {
				throw new Exception('Erro ao buscar dados da quantidade necessária de acordo com o projeto.') ;
			}
			// Busca quantidades necessárias de acordo com o motivo
			$this->gerarDadosQtdNecessariaMpm($dt_base);
			 
			// Consolida os dados das quantidades baixadas e quantidades necessárias
			if (!$this->dao->consolidarDadosOS()) {
				throw new Exception('Erro ao consolidar dados de quantidades baixadas e quantidades necessárias.') ;
			}
			// Consolida os dados das quantidades mínima e máxima
			if (!$this->dao->gerarDadosOSIncorreta()) {
				throw new Exception('Erro ao consolidar dados de quantidades mínima e máxima.') ;
			}
			// Aplica a regra de similaridade e alimenta a tabela os_baixa_incorreta com os dados consolidados
			$retorno = $this->dao->gerarDadosOSIncorretaSemSimilares();
			if ($retorno === null) {
				throw new Exception('Erro ao alimentar a tabela os_baixa_incorreta.') ;
			}
			
			// Finaliza transação
			$this->dao->commit();
		}
		
		catch(Exception $e) {
			// Reverte ações na transação
    		$this->dao->rollback();
            echo $e->getMessage();
			$retorno = null;

    	}
		
		return $retorno;
		
	}	
	
	/**
	 * Busca quantidades necessárias de acordo com o motivo
	 *
	 * @param stdClass $filtros Filtros da pesquisa
	 * @return array
	 */
	private function gerarDadosQtdNecessariaMpm($dt_base) {
		
		// Busca quantidades necessárias 1 e 3 de acordo com o motivo
		$resultadoPesquisa = $this->dao->buscarQtdNecessariaMpm1_3($dt_base);
		if (! is_array($resultadoPesquisa)) {
			throw new Exception('Erro ao buscar dados das quantidades necessárias 1 e 3 de acordo com o motivo.') ;
		}
        	
		// Cria tabela temporária de quantidades necessárias de acordo com o motivo
		if (!$this->dao->criarTabQtdNecessariaMpm()) {
			throw new Exception('Erro ao criar tabela temporária de quantidades necessárias de acordo com o motivo.') ;
		}
		
		//Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) > 0) {			
        	
        	// Percorre resultado
        	foreach ($resultadoPesquisa as $resultado){
        		$qtd_necessaria = 0;
        		
        		// Atribui a quantidade necessária de acordo com motivo/ produto
        		$qtd_necessaria = $resultado->qtd_necessaria1;
        		
        		// Se quantidade necessária 1 for 0, busca a quantidade necessária de acordo com o motivo/ produto/ material
        		if ($qtd_necessaria == 0 && $resultado->cmiotioid != "" && $resultado->prdoid != "" && $resultado->oftcprefixo != "" && $resultado->oftctabela != "" && $resultado->ordconnumero != "" && $resultado->otiobroid != "") {
  					//buscar quantidade necessária 2
        			$qtd_necessaria = $this->dao->buscarQtdNecessariaMpm2($resultado->cmiotioid,$resultado->prdoid,$resultado->oftcprefixo,$resultado->oftctabela,$resultado->ordconnumero,$resultado->otiobroid);
        		}
        		
        		// Se quantidade necessária 2 for 0, atribui a quantidade necessária de acordo com motivo
        		if ($qtd_necessaria == 0) {
        			// Para atribuir a quantidade necessária 3 o motivo não pode ter nenhum material relacionado 
        			if ($this->dao->verificarQtdeNecessaria1($resultado->cmiotioid) == 0){
        				$qtd_necessaria = $resultado->qtd_necessaria3;
        			}			
        		}
        		
        		$resultado->prdgrsoid = isset($resultado->prdgrsoid) && !empty($resultado->prdgrsoid) ? $resultado->prdgrsoid : "null";
				
        		// Atribui os resultados à tabela temporária
        		if (!$this->dao->gerarDadosQtdNecessariaMpm($resultado->cmidata,$resultado->ordoid,$resultado->orddt_ordem,$resultado->ordconnumero,$resultado->ordusuoid_concl,$resultado->ordrelroid,$resultado->itloid,$resultado->itlrepoid,$resultado->prdoid,$resultado->prdgrsoid,0,$qtd_necessaria)) {
        			throw new Exception('Erro ao gerar tabela de quantidades necessárias de acordo com o motivo.');
        		}
        	
        	}	
        
        } 
	
	}
	

    /**
     * Metodo Construtor
     */
    public function __construct() {
        $this->dao = new OSBaixaIncorretaDAO();
    }

}