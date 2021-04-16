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
 * @subpackage Classe Model de Contrato (Modelo Antigo)
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\ContratoAntigo;

use infra\Validacao,
module\ContratoAntigo\PropostaAntigoDAO as PropostaDAO,
module\ContratoAntigo\ContratoAntigoDAO as ContratoDAO,
module\Contrato\ContratoModel;

class ContratoAntigoModel extends ContratoModel{
	
	public $prpDAO;
	public $cttDAO;
	
	public function __construct() {
        $this->prpDAO = new PropostaDAO();
        $this->cttDAO = new ContratoDAO();
    }    
}