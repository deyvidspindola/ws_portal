<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Rafael Dias <rafael.dias@meta.com.br>
 * @version 13/11/2013
 * @since 13/11/2013
 * @package Core
 * @subpackage Classe de Acesso a Dados de Contrato (Modelo Antigo)
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\ContratoAntigo;

use infra\ComumDAO;
use module\Contrato\PropostaDAO;

class PropostaAntigoDAO extends PropostaDAO{
	
	/**
	 * Insere proposta_endereco_cob
	 *
	 * @author Rafael Dias <rafael.dias@meta.com.br>
	 * @version 22/11/2013
	 * @param int $prpoid
     * @param int $usuoid
     * @param string $campos
     * @param string $valores
     * @return array / false
	 */	
	public function propostaEnderecoCobInsert($campos, $valores){
		$sqlString = "INSERT INTO proposta_endereco_cob (".$campos.")
    					VALUES (".$valores.") RETURNING prpcoid;";
		$this->queryExec($sqlString);
	
		if($this->getAffectedRows() > 0){
			return $this->getAssoc();
		} else{
			return false;
		}
	}
	
}