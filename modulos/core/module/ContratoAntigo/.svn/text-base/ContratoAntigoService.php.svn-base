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
 * @subpackage Classe Core de Contrato (Modelo Antigo)
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */

namespace module\ContratoAntigo;

use module\ContratoAntigo\ContratoAntigoController as Controlador;

class ContratoAntigoService{
	    
    /**
     * Método estático para criar uma proposta com status Z = em elaboração
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 14/11/2013
	 * @param array $propostaDadosArray ()
	 * @param array $propostaPagamentoArray ()
	 * @param array $propostaContato1Array ()
	 * @param array $propostaContato2Array ()
	 * @param array $propostaContato3Array ()
	 * @param array $propostaComercialArray ()
	 * @param array $propostaServicoList ()
	 * @param array $propostaGerenciadoraArray ()
	 * @param bool $transaction ()
     * @return Response $this->response:
     *     mixed $this->response->dados ($prpoid=OK /false = falha)
     *     string $this->response->codigo (código do erro)
     *     string $this->response->mensagem (mensagem emitida)
    */
    public static function propostaCria(
		$propostaDadosArray=array(),
		$propostaPagamentoArray=array(),
		$propostaContato1Array=array(),
		$propostaContato2Array=array(),
		$propostaContato3Array=array(),
		$propostaComercialArray=array(),
		$propostaServicoList=array(),
		$propostaGerenciadoraList=array(),
		$propostaItemArray=array(),
		$transaction=true
	) {
        return Controlador::propostaCria(
			$propostaDadosArray,
			$propostaPagamentoArray,
			$propostaContato1Array,
			$propostaContato2Array,
			$propostaContato3Array,
			$propostaComercialArray,
			$propostaServicoList,
			$propostaGerenciadoraList,
        	$propostaItemArray,
			$transaction
		);
    }
        
}