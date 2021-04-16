<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Leandro A. Ivanaga <leandroivanaga@brq.com>
 * @version 25/11/2013
 * @since 25/11/2013
 * @package Core
 * @subpackage Classe Core de Titulo Cobranca
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */

namespace module\TituloCobranca;
use module\TituloCobranca\TituloCobrancaController as Controlador;

class TituloCobrancaService{
    
	/**
     * Gera a taxa (ex: taxa de instalacao) (BOLETO)
     *
     * @author Leandro A. Ivanaga <leandroivanaga@brq.com>
     * @version 26/11/2013
     * @param int $prpoid (ID da proposta)
	 * @param int $usuoid (ID do usuario)
	 * @param int $clioid (ID do cliente)
	 * @param array $numContratos (array associativo tipo chave -> valor, numero dos contratos)
     *     OBS-> campos obrigatórios do $numContratos[]: 
     *     		int contrato -> numero do contrato
     * @param array $dadosTaxa (array associativo tipo chave -> valor, dados da taxa titulo_retencao, titulo_retencao_item)
     *     OBS-> campos obrigatórios do $dadosTaxa[]: 
     *     	 float taxa_valor_total -> valor total do titulo ou valor total da parcela (tabela titulo_rentecao)
     *     	 float taxa_valor_item -> valor para cada item (tabela titulo_retencao_item)
     *     	 int taxa_qntd_parcelas -> quantidade total de parcelas
     *       int taxa_id_obrigacao -> ID da obrigação financeira
     *       string taxa_descricao_obrigacao -> descricao da obrigacao financeira
     *       int taxa_forma_pagamento -> ID da forma de pagamento
     *       string taxa_data_vencimento -> data de vencimento do titulo formato (dd-mm-YYYY, ex: 03-09-2014)
     *       int taxa_num_parcela -> numero da parcela em questão
     *     OBS-> campos NÃO obrigatórios do $dadosTaxa[]: 
     *       N/A
     *  @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
     * 
     */
    public function geraTaxaBoleto($prpoid=0, $usuoid=0, $clioid=0, $numContratos=array(), $dadosTaxa=array()) {
    	$tituloCobranca = new Controlador();
    	return $tituloCobranca->geraTaxaBoleto($prpoid, $usuoid, $clioid, $numContratos, $dadosTaxa);
    }
    
	/**
     * Gera a taxa (ex: taxa de instalacao) (Cartão de crédito)
     *
     * @author Leandro A. Ivanaga <leandroivanaga@brq.com>
     * @version 26/11/2013
     * @param int $prpoid (ID da proposta)
	 * @param int $usuoid (ID do usuario)
	 * @param int $clioid (ID do cliente)
	 * @param array $numContratos (array associativo tipo chave -> valor, numero dos contratos)
     *     OBS-> campos obrigatórios do $numContratos[]: 
     *     		int contrato -> numero do contrato
     * @param array $dadosTaxa (array associativo tipo chave -> valor, dados da taxa nota_fical, nota_fiscal_item, titulo)
     *     OBS-> campos obrigatórios do $dadosTaxa[]: 
     *     	 float taxa_valor_total -> valor total do titulo ou valor total da parcela (tabela titulo, nota_fiscal)
     *     	 float taxa_valor_item -> valor para cada item (tabela nota_fiscal_item)
     *     	 int taxa_qntd_parcelas -> quantidade total de parcelas
     *       int taxa_id_obrigacao -> ID da obrigação financeira
     *       string taxa_descricao_obrigacao -> descricao da obrigacao financeira
     *       int taxa_forma_pagamento -> ID da forma de pagamento
     *       string taxa_data_vencimento -> data de vencimento do titulo formato (dd-mm-YYYY, ex: 03-09-2014)
     *       int taxa_num_parcela -> numero da parcela em questão
     *       string taxa_num_cartao -> numero do cartão de crédito do cliente
     *       string taxa_data_validade_cartao -> mes e ano de vencimento do cartão (mm/YY, ex: 03/15) 
     *       int taxa_codigo_seguranca -> numero do codigo de segurança do cartão
     *     OBS-> campos NÃO obrigatórios do $dadosTaxa[]: 
     *       N/A
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
     */
    public function geraTaxaCartao($prpoid=0, $usuoid=0, $clioid=0, $numContratos=array(), $dadosTaxa=array()) {
    	$tituloCobranca = new Controlador();
        
    	return $tituloCobranca->geraTaxaCartao($prpoid, $usuoid, $clioid, $numContratos, $dadosTaxa);
    }
    
}