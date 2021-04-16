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
 * @subpackage Classe Controladora de Titulo Cobranca
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */

namespace module\TituloCobranca;

use infra\ComumController,
    infra\Helper\Response,
    infra\Helper\Mascara,
    infra\Helper\Validacao,
    module\TituloCobranca\TituloCobrancaModel as Modelo,
    module\Boleto\BoletoService as Boleto;
use module\BoletoRegistrado\BoletoRegistradoModel;
use module\RoteadorBoleto\RoteadorBoletoService;

include _SITEDIR_ . 'boleto_funcoes.php';

require_once _MODULEDIR_ . 'Principal/Action/PrnBoletoSeco.class.php';
require_once _MODULEDIR_ . 'Principal/Action/PrnManutencaoFormaCobrancaCliente.php';
require_once _MODULEDIR_ . 'Financas/Action/FinFaturamentoCartaoCredito.class.php';
require_once _MODULEDIR_ . 'Principal/Action/PrnRelacionamentoCliente.php';

class TituloCobrancaController extends ComumController {

    public $model;
    public $response;
    public $classeBoletoExterna;
    public $classeManutencaoCobrancaExterna;
    public $classeFaturamentoCartaoExterna;
    public $classeRelacionamentoClienteExterna;

    /**
     * Contrutor da classe
     * 
     * @author Leandro A. Ivanaga <leandroivanaga@brq.com>
     * @version 25/11/2013
     * @param none
     * @return none
     */
    public function __construct() {
        $this->model = new Modelo();
        $this->response = new Response();

        // Classe de geração do arquivo boleto e envio
        $classeBoleto = "PrnBoletoSeco";
        $this->classeBoletoExterna = new $classeBoleto();

        // Classe de busca de informações referentes a forma de cobranca do cliente
        $classeManutencaoCobranca = "PrnManutencaoFormaCobrancaCliente";
        $this->classeManutencaoCobrancaExterna = new $classeManutencaoCobranca();

        // Classe realiza pagamento da taxa para a forma de pagamento como cartão de crédito
        $classeFaturamentoCartao = "FinFaturamentoCartaoCredito";
        $this->classeFaturamentoCartaoExterna = new $classeFaturamentoCartao();

        // Classe que realiza envio da confirmação do pagamento quando forma como cartão de credito
        $classeRelacionamentoCliente = "PrnRelacionamentoCliente";
        $this->classeRelacionamentoClienteExterna = new $classeRelacionamentoCliente();
    }

    // MÉTODOS RELACIONADOS A TITULO DE COBRANCA (GERACAO DE TITULO, ENVIOS)

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
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
     * 
     */
    public function geraTaxaBoleto($prpoid = 0, $usuoid = 0, $clioid = 0, $numContratos = array(), $dadosTaxa = array()) {

        $teveErro = false;

        $camposObrigatoriosTaxa = array('taxa_valor_total', 'taxa_valor_item', 'taxa_qntd_parcelas',
            'taxa_id_obrigacao', 'taxa_descricao_obrigacao', 'taxa_forma_pagamento',
            'taxa_data_vencimento', 'taxa_num_parcela');

        $prpoid = Mascara::inteiro($prpoid);
        $usuoid = Mascara::inteiro($usuoid);
        $clioid = Mascara::inteiro($clioid);

        if ($prpoid > 0 && $usuoid > 0 && $clioid > 0) {

            // Verifica se foi informado o(s) contrato(s)
            if (!is_array($numContratos) || count($numContratos) < 1) {
                $this->response->setResult(false, 'INF005');
                $teveErro = true;
            }

            // Verifica se foi informado todos os dados referente a taxa
            $exists = $this->verificaCampos($camposObrigatoriosTaxa, $dadosTaxa);
            if (!is_array($dadosTaxa) || $exists === false) {
                $this->response->setResult(false, 'INF005');
                $teveErro = true;
            }

            // Armazenar o titulo cobranca (tabela titulo_retencao)
            $retTitoid = $this->model->insertTituloRetencao($prpoid, $usuoid, $clioid, $numContratos, $dadosTaxa);

            // Se o retorno foi false, houve erro na criação do titulo
            if ($retTitoid === false) {
                $this->response->setResult(false, 'TAX001');
                $teveErro = true;
            } else {
                $codigoOrigem = BoletoRegistradoModel::CODIGO_ORIGEM_SALESFORCE;
                $boletoObj = new BoletoRegistradoModel();
                $boletoObj->setTituloId($retTitoid);
                $boletoObj->setCodigoOrigem($codigoOrigem);
                $boletoObj->setDataVencimento($dadosTaxa['taxa_data_vencimento_bd']);
                $boletoObj->setValorFace($dadosTaxa['taxa_valor_total']);
                $boletoObj->setValorDescontoNegociadoDescritivo(0);
                $boletoObj->setValorAbatimentoDescritivo(0);
                $boletoObj->setValorMoraNegociadaDescritivo(0);
                $boletoObj->setValorOutrosAcrescimosDescritivo(0);
                $boletoObj->setValorNominal($dadosTaxa['taxa_valor_total']);

                $urlBoleto = null;
                try {
                    $boletoObj->registrarBoletoOnline();
                    $urlBoleto = BoletoRegistradoModel::getLinkExibirBoleto($retTitoid, $codigoOrigem);
                    $uri = explode('/', $_SERVER['REQUEST_URI']);
                    $uri = $_SERVER['HTTP_HOST'] . '/' . $uri[1] . '/';
                    $urlBoleto = _PROTOCOLO_ . $uri . $urlBoleto;
                } catch (\Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }

            // Armazenar o registro da taxa na tabela de controle de envio (tabela titulo_controle_envio)
            $retTituloControle = $this->model->insertTituloControle($retTitoid, $numContratos[0]);

            // Verifica se houve erro ao salvar o controle para o titulo
            if ($retTituloControle === false) {
                $this->response->setResult(false, 'TAX003');
                $teveErro = true;
            }
            $htmlBoleto = file_get_contents($urlBoleto);
            $arquivoBoleto = RoteadorBoletoService::getArquivoBoleto($retTitoid, '', $htmlBoleto);

            if ($arquivoBoleto instanceof Response) {
                $this->response = $arquivoBoleto;
                $teveErro = true;
            } elseif (strlen($arquivoBoleto) == 0) {
                $this->response->setResult(false, 'TAX004');
                $teveErro = true;
            }

            if (!$teveErro) {
                // Realiza envio do boleto
                $retEnvio = $this->classeBoletoExterna->enviarBoleto($prpoid, $retTitoid, $arquivoBoleto);
                $this->response->setResult($retTitoid, '0');
            }
        } else {
            $this->response->setResult(false, 'INF005');
            $teveErro = true;
        }

        // Código e mensagem de retorno
        return $this->response;
    }

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
    public function geraTaxaCartao($prpoid = 0, $usuoid = 0, $clioid = 0, $numContratos = array(), $dadosTaxa = array()) {

        $teveErro = false;
        $resposta = '';

        $tx = fopen(_SITEDIR_ . 'arq_financeiro/geraTaxaCartao.txt', 'a+');
        $gr = '--->$prpoid:' . $prpoid . "\r\n";
        $gr .= '--->$usuoid:' . $usuoid . "\r\n";
        $gr .= '--->$clioid:' . $clioid . "\r\n";
        $gr .= '--->contrato:' . $numContratos[0] . "\r\n";
        $gr .= '--->taxa_valor_total:' . $dadosTaxa['taxa_valor_total'] . "\r\n";
        $gr .= '--->taxa_valor_item:' . $dadosTaxa['taxa_valor_item'] . "\r\n";
        $gr .= '--->taxa_qntd_parcelas:' . $dadosTaxa['taxa_qntd_parcelas'] . "\r\n";
        $gr .= '--->taxa_id_obrigacao:' . $dadosTaxa['taxa_id_obrigacao'] . "\r\n";
        $gr .= '--->taxa_descricao_obrigacao:' . $dadosTaxa['taxa_descricao_obrigacao'] . "\r\n";
        $gr .= '--->taxa_forma_pagamento:' . $dadosTaxa['taxa_forma_pagamento'] . "\r\n";
        $gr .= '--->taxa_data_vencimento:' . $dadosTaxa['taxa_data_vencimento'] . "\r\n";
        $gr .= '--->taxa_num_parcela:' . $dadosTaxa['taxa_num_parcela'] . "\r\n";
        $gr .= '--->taxa_num_cartao:' . $dadosTaxa['taxa_num_cartao'] . "\r\n";
        $gr .= '--->taxa_data_validade_cartao:' . $dadosTaxa['taxa_data_validade_cartao'] . "\r\n";
        $gr .= '--->taxa_codigo_seguranca:' . $dadosTaxa['taxa_codigo_seguranca'] . "\r\n";
        fwrite($tx, $gr);
        fclose($tx);




        //$this->model->tituloCobrancaDAO->startTransaction();
        file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . 'entrei no geraTaxaCartao **** :' . $prpoid . ' conn:' . $this->conn, FILE_APPEND);

        $camposObrigatoriosTaxa = array('taxa_valor_total', 'taxa_valor_item', 'taxa_qntd_parcelas',
            'taxa_id_obrigacao', 'taxa_descricao_obrigacao', 'taxa_forma_pagamento',
            'taxa_data_vencimento', 'taxa_num_parcela', 'taxa_num_cartao',
            'taxa_data_validade_cartao', 'taxa_codigo_seguranca'
        );

        $prpoid = Mascara::inteiro($prpoid);
        $usuoid = Mascara::inteiro($usuoid);
        $clioid = Mascara::inteiro($clioid);

        if ($prpoid > 0 && $usuoid > 0 && $clioid > 0) {
            file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . 'entrei no if($prpoid > 0 && $usuoid > 0 && $clioid > 0) ' . $prpoid . ' conn:' . pg_transaction_status($this->conn), FILE_APPEND);

            // Verifica se foi informado o(s) contrato(s)
            if (!is_array($numContratos) || count($numContratos) < 1) {
                $this->response->setResult(false, 'INF005');
                $teveErro = true;
            }

            // Verifica se foi informado todos os dados referente a taxa
            $exists = $this->verificaCampos($camposObrigatoriosTaxa, $dadosTaxa);
            if (!is_array($dadosTaxa) || $exists === false) {
                $this->response->setResult(false, 'INF005');
                $teveErro = true;
            }
            file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . 'antes classeManutencaoCobrancaExterna' . $prpoid . ' conn:' . pg_transaction_status($this->conn), FILE_APPEND);

            // Salva os dados de pagamento
            $retSalvaDadosPagamento = $this->classeManutencaoCobrancaExterna->confirmarFormaPagamento();
            
            $rs = fopen(_SITEDIR_ . 'arq_financeiro/retSalvaDadosPagamento.txt', 'w+');
            fwrite($rs, 'é agora:'.$retSalvaDadosPagamento);
            fclose($rs);
            
            file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . 'depois classeManutencaoCobrancaExterna error:' . $retSalvaDadosPagamento['error'] . ' conn:' . pg_transaction_status($this->conn), FILE_APPEND);
            if ($retSalvaDadosPagamento['error'] === true) {
                // Erro para armazenar os dados de pagamento

                $this->response->setResult(false, 'TAX005');
                $teveErro = true;
            } else {

                file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . 'antes insertTitulo. ', FILE_APPEND);
                // Armazenar o titulo cobranca (tabela titulo)
                $retTitoid = $this->model->insertTitulo($prpoid, $usuoid, $clioid, $numContratos, $dadosTaxa);

                file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . 'depois insertTitulo. ' . $retTitoid, FILE_APPEND);

                if ($retTitoid === false) {
                    // Se o retorno foi false, houve erro na criação do titulo
                    $this->response->setResult(false, 'TAX001');
                    $teveErro = true;
                } else {
                    // Realizar chamada para pagamento do titulo com cartao de credito
                    $authorizedId = $this->classeManutencaoCobrancaExterna->buscarAutorizadora($dadosTaxa['taxa_forma_pagamento']);
                    file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . 'antes  $pagaTituloCartao. ', FILE_APPEND);

                    $pagaTituloCartao = $this->classeFaturamentoCartaoExterna->processarPagamentoParcelado
                            ($clioid, $retTitoid, $dadosTaxa['taxa_valor_total'], $dadosTaxa['taxa_data_vencimento'], $dadosTaxa['taxa_qntd_parcelas'], $authorizedId, $dadosTaxa['taxa_num_cartao'], $dadosTaxa['taxa_data_validade_cartao'], $dadosTaxa['taxa_codigo_seguranca'], NULL, "CORE", $dadosTaxa['taxa_nome_portador']
                    );


                    file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . ' depois  $pagaTituloCartao. ' . $pagaTituloCartao['resposta'], FILE_APPEND);

                    if ($pagaTituloCartao['resposta'] != 'OK' || empty($pagaTituloCartao)) {
                    file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . ' NAO FOI POSSIVEL REALIZAR O PAGAMENTO.' , FILE_APPEND);
                        // Não foi possivel realizar pagamento do titulo, rollback no titulo
                        // Não deve ficar armazenado o titulo quando não foi possivel realizar o pagamento
                        // Resposta para log detalhado de falha 
                        $resposta .= $retTitoid . '|';
                        $resposta .= 'Resposta:false|';


                        if (isset($pagaTituloCartao['code'])) {
                            $resposta .= (string) $pagaTituloCartao['code'] . '|';
                        }


                        if (isset($pagaTituloCartao['acao'])) {
                            $resposta .= (string) $pagaTituloCartao['acao'];
                        }

                        $this->response->setResult(false, 'TAX006', $resposta);

                        $teveErro = true;
                    } else {

                        // Enviar email para o cliente informando o sucesso no pagamento da taxa de instalação
                        $retornoRelacionamento = $this->classeRelacionamentoClienteExterna->enviaEmailConfirmacaoPagamento($prpoid, $numContratos[0]);

                        if ($retornoRelacionamento == true) {
                            $emailRelacionamento = true;
                        }
                    }
                }

                if ($teveErro == false) {
                    $this->response->setResult($retTitoid, '0');
                }
            }
        } else {
            $this->response->setResult(false, 'INF005');
            $teveErro = true;
        }


        if ($teveErro) {
            // Rollback Transaction e rollback do titulo, garantindo que nao fique na base sem ter realizado o pagamento

            if ($retTitoid > 0) {
                file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . 'entrei $rollbackTitulo. ' . $retTitoid, FILE_APPEND);

                $rollbackTitulo = $this->model->rollbackTitulo($retTitoid);
            }
            //$this->model->tituloCobrancaDAO->rollbackTransaction();
            // Código e mensagem de retorno
        } else {
            // Commit Transaction
            //$this->model->tituloCobrancaDAO->commitTransaction();
        }

        // Código e mensagem de retorno
        return $this->response;
    }

}
