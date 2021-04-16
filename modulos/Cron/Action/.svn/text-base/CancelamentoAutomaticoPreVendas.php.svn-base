<?php

/**
 * Classe para persistência de dados deste modulo
 */
require _MODULEDIR_ . 'Cron/DAO/CancelamentoAutomaticoPreVendasDAO.php';


/**
 * @class MasterViagem
 * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
 * @since 31/08/2012
 * Camada de regras de negócio.
 */
class CancelamentoAutomaticoPreVendas{
	
	private $dao;

	function preVendasCancelamento() {		
		
		$requisicoes = array();
	
		$requisicoes = $this->dao->buscarHorasCancelamentoAgendamento();

		foreach($requisicoes as $row){
            $ragoid = $row['ragoid'];
			$ragordoid = $row['ragordoid'];
			$difHoras   = $row['horas'];
				
				
			if( $difHoras >= 1 ){						
				
				$this->dao->atualizarCancelamentoAgendamento($ragoid);
				
				$this->dao->gravarHistoricoCancelamento($ragordoid);
			
			}
				
		}

		$this->dao->atualizarCancelamentoItensEstoque();
				
	}

    public function __construct() {
        $this->dao = new CancelamentoAutomaticoPreReservaDAO();
    }

}