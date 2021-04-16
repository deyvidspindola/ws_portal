<?php

require_once _MODULEDIR_ . 'Financas/DAO/FinRescisaoDAO.php';
require_once 'FinRescisaoHelpers.php';
use infra\Helper\Response;
use module\Parametro\ParametroIntegracaoTotvs;

class FinRescisao
{
    protected $_dao;
    protected $_viewPath;

    public function __construct()
    {
        $this->_dao = new FinRescisaoDAO();
        $this->_viewPath = _MODULEDIR_ . 'Financas/View/fin_rescisao/';
    }

    /**
     *  Recupera parâmetro recebido via POST
     * @param   string      $param
     * @return  string
     */
    protected function _getPostParam($param)
    {
        return $this->_getParamFromRequest($param, $_POST);
    }

    /**
     *  Recupera parâmetro recebido via GET
     * @param   string      $param
     * @return  string
     */
    protected function _getGetParam($param)
    {
        return $this->_getParamFromRequest($param, $_GET);
    }

    /**
     *  Recupera parâmetro recebido na requisição ($_REQUEST)
     * @param   string      $param
     * @return  string
     */
    protected function _getRequestParam($param)
    {
        return $this->_getParamFromRequest($param, $_REQUEST);
    }

    /**
     *  Recupera um parâmetro recebido em determinado tipo de requisição
     * @param   string      $param
     * @param   string      $param  Requisição: $_POST, $_GET, $_REQUEST
     * @return  string
     */
    protected function _getParamFromRequest($param, $requestType)
    {
        return isset($requestType[$param]) ? $requestType[$param] : '';
    }

    /**
     * Verifica se parâmetro existe na requisição
     * @param   string  $param
     * @return  boolean
     */
    protected function _hasParam($param)
    {
        $value = $this->_getRequestParam($param);
        return (bool) (strlen($value));
    }

    /**
     * Verifica se uma requisição foi efetuada via POST
     * @return  boolean
     */
    protected function _isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Verifica se uma requisição foi efetuada via GET
     * @return  boolean
     */
    protected function _isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Verifica se uma requisição foi efetuada via AJAX
     * @return  boolean
     */
    protected function _isAjax()
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH']
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * Redireciona para uma página do sistema
     * @param   string  $target
     * @return  void
     */
    protected function _redirect($target)
    {
        // Recupera o protocolo utilizado (HTTP ou HTTPS)
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off")
                        ? "https://" : "http://";

        // Recupera o endereço do servidor (IP ou URI)
        $server = $_SERVER['HTTP_HOST'] . '/';

        // Gambi para requisição local
        if (preg_match('/sistemaWeb/', $_SERVER['REQUEST_URI']))
        {
            $server .= 'sistemaWeb/';
        }

        $location = $protocol . $server . $target;

        header("Location: {$location}");
    }

    /**
     * Guarda ou recupera uma flash message da sessão
     * @param   array   $message
     * @return  string|void
     */
    public function flashMessage($message = null)
    {
        if ($message)
        {
            $_SESSION['flash_message'] = $message;
        }
        else
        {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);

            return $message;
        }
    }

    /**
     * Verifica se há uma flash message guardada na sessão
     * @return  boolean
     */
    public function hasFlashMessage()
    {
        return (isset($_SESSION['flash_message'])
                    && strlen($_SESSION['flash_message']));
    }

    /**
     * Ação de busca
     * @return  void
     */
    public function pesquisar()
    {
        // Seta as datas padrão
        $dataInicial  = strlen($this->_getGetParam('data_inicial'))
                            ? $this->_getGetParam('data_inicial')
                            : date('d/m/Y', strtotime('-1 month', time()));
        $dataFinal    = strlen($this->_getGetParam('data_final'))
                            ? $this->_getGetParam('data_final')
                            : date('d/m/Y');

        // Preenche campos do formulário
        $listaMotivos        = $this->_dao->findMotivos();
        $listaStatusRescisao = $this->_dao->findstatusRescisao();
        $listaTiposContrato  = $this->_dao->findTiposContrato();
        $listaUfs            = $this->_dao->findUfs();
        $listaResponsaveis   = $this->_dao->findResponsaveis();
        $listaClasseTermos   = $this->_dao->findClasseTermos();

        // Executa a busca de registros
        if (strtolower($this->_getGetParam('acao')) === 'pesquisar')
        {
            $resultados = $this->_dao->buscarRescisoes($_GET);
        }

        // [START][ORGMKTOTVS-1986] - Leandro Corso 
        if (INTEGRACAO) $message = ParametroIntegracaoTotvs::message('O gerador automático de títulos');
        // [END][ORGMKTOTVS-1986] - Leandro Corso 

        require_once $this->_viewPath . 'index.php';
    }

    /**
     * Popula formulário de busca
     * @param   string  $key
     * @return  string
     */
    public function populate($key)
    {
        if (isset($_GET[$key]))
        {
            return $_GET[$key];
        }

        return '';
    }

    /**
     * Exibe página de edição de rescisão
     * @return  void
     */
    public function visualizar()
    {
        $resmoid = $this->_getGetParam('resmoid');

        $dadosRescisao  = $this->_dao->findRescisao($resmoid);
        $contratos      = $this->_dao->findContratosRescisao($resmoid);
        $faturas        = $this->_dao->findFaturasContratosRecisao($resmoid);
        $multasLocacao  = $this->_dao->findMultasLocacaoRescisao($resmoid);
        $multasServicos = $this->_dao->findMultasServicosRescisao($resmoid);
        $taxasRetirada  = $this->_dao->findTaxasRetiradaRescisao($resmoid);
        $listaHistorico = $this->_dao->findHistoricoRescisao($resmoid);

        require_once $this->_viewPath . 'visualizar.php';
    }

    /**
     * Exclui uma rescisão
     * @return  void
     */
    public function excluir()
    {
        $resmoid = $this->_getGetParam('resmoid');

        if ($this->_dao->excluirRescisao($resmoid))
        {
            $this->flashMessage('Registro excluído com sucesso.');
        }
        else
        {
            $this->flashMessage('O registro não pôde ser excluído.');
        }
    }

    /**
     * Exibe a página de criação de rescisão
     * @return  void
     */
    public function novo()
    {
        require_once $this->_viewPath . 'novo.php';
    }

    /**
     * Busca a data da última solicitação de rescisão
     * @return  void
     */
    public function buscaDataSolicitacao()
    {
        // Fix para charset errada
        header('Content-type: text/html; charset=ISO-8859-1');

        $clioid = $this->_getGetParam('clioid');
        $data   = $this->_dao->findDataSolicitacao($clioid);

        if ($data)
        {
            echo date('d/m/Y', strtotime($data['prescadastro']));
        }
    }

    /**
     * Busca a data da última solicitação de rescisão do contrato
     * @return  void
     */
    public function buscaDataSolicitacaoContrato()
    {
        // Fix para charset errada
        header('Content-type: text/html; charset=ISO-8859-1');

        $data = $this->_dao->findDataSolicitacaoContrato($_GET['connumero']);

        if ($data)
        {
            echo date('d/m/Y', strtotime($data['prescadastro']));
        }
    }

    /**
     * Busca contratos do cliente
     * @return  void
     */
    public function buscaContratos()
    {
        // Fix para charset errada
        header('Content-type: text/html; charset=ISO-8859-1');

        $contratos = $this->_dao->findContratosNovaRescisao($_GET);

        if ($contratos) {
            for ($i=0; $i<sizeof($contratos); $i++) {
                $retornoFidelizacao = $this->_dao->verificaFidelizacao($contratos[$i]);
            
                if($retornoFidelizacao){
                    $contratos[$i] = array_merge($contratos[$i], $retornoFidelizacao[0]);
                }
            }
        }
        
        require_once $this->_viewPath . 'novo_contratos.php';
    }

    /**
     * Busca as multas (itens "multáveis") dos contratos selecionados
     * @return  void
     */
    public function buscaMultas()
    {
        // Fix para charset errada
        header('Content-type: text/html; charset=ISO-8859-1');

        $contratos = $this->_getGetParam('connumero');
        $clioid = $this->_getGetParam('clioid');

        if(!$clioid && count($contratos) == 1) {
            $clioid = $this->_dao->getClioidByContrato($contratos[0]);
        }

        try{
            //Dados da Multa
            $dadosCalculosWS = $this->buscarDadosMultaWS();
            $retorno = $dadosCalculosWS->dadosRescisao;
           
            $totaisRescisao = $dadosCalculosWS->totais;

            $isClienteSiggo = $dadosCalculosWS->isClienteSiggo;
            
            // Dados da finalização de rescisão
            $resmfax      = $this->_getGetParam('resmfax');
            $listaMotivos = $this->_dao->findMotivos();
            $listaStatusRescisao = $this->_dao->findStatusRescisao();
            $listaFormasCobranca = $this->_dao->findFormasCobranca();
            
            // STI 84189
            $emailCliente = $this->_dao->getEmailCliente($clioid);
            // FIM STI 84189

            require_once $this->_viewPath . 'novo_multas.php';

        } catch (Exception $e) {
            echo "<div class=\"separador\"></div>";
            echo "<div class=\"mensagem erro\"> " . $e->getMessage() . "</div>";
            exit();
        }
    }


    private function buscarDadosMultaWS() {

        $contrato               = isset($_GET['connumero'])   ? $_GET['connumero']   : '';
        $dataPreRescisao        = isset($_GET['solicitacao']) ? $_GET['solicitacao'] : '';
        $percentualMulta        = isset($_GET['multa'])       ? $_GET['multa']       : '';
        $isentarMonitoramento   = $_GET['isentar_monitoramento'] === 'true' ? true : false;
        $isentarLocacao         = $_GET['isentar_locacao'] === 'true' ? true : false;
        //Retorno
        $retorno = new stdClass();
        $retorno->dadosRescisao = array();
        $retorno->totais = new stdClass();
        $retorno->isClienteSiggo = false;

        /**
         * Totais da rescisao
         */
        $retorno->totais->totalMensalidadeEquipamento = 0;
        $retorno->totais->totalMensalidadeIndevido = 0;
        $retorno->totais->totalMultaMensalidadeEquipamento = 0;
        $retorno->totais->totalDiferencaIndevido = 0;
        $retorno->totais->valorMultaMensalidade = 0;
        $retorno->totais->valorMultaMensalidadeFaltante = 0;
        $retorno->totais->valorMultaMensalidadeDevolver = 0;
        $retorno->totais->valorPagoIndevidoMonitoramentoTotal = 0;
        $retorno->totais->valorMultaEquipamentoDevolver = 0;
        $retorno->totais->valorTotalVigencia = 0;
        $retorno->totais->totalizadorEquipamentoParcela = 0;

        try {

            //Valida os parametros
            if (empty($contrato) || !is_array($contrato)) {
                throw new Exception('Há campo não preenchidos.');
            }

            foreach ($contrato as $numero) {
                if (empty($dataPreRescisao[$numero])) {
                    throw new Exception('Há campo não preenchidos.');
                }
                if (empty($percentualMulta[$numero])) {
                    throw new Exception('Há campo não preenchidos.');
                }
            }

            // STI 84189
            $urlWebServiceCalculoRescisao = $this->_dao->getUrlWebService();
            // FIM STI 84189

            for($i = 0; $i < count($contrato); $i++){

                $connumero = $contrato[$i];
                $multa     = $percentualMulta[$connumero];
                $data      = $dataPreRescisao[$connumero];
                
                ob_start();
                $urlExiste = file_get_contents($urlWebServiceCalculoRescisao);
                ob_end_clean();


                if ($urlExiste === false) {
                    throw new Exception('Não foi possivel acessar o WebService');
                }

                $webServiceRescisao = new SoapClient($urlWebServiceCalculoRescisao,
                                array('trace' => 1,
                                    'exceptions' => 1,
                                    'soap_version' => SOAP_1_1));


                $parametrosEntrada = new stdClass();
                $parametrosEntrada->termo   = $connumero;
                $parametrosEntrada->dataPre = $data;
                $parametrosEntrada->multa   = $multa;
                $parametrosEntrada->zeraMultaMonitoamento   = $isentarMonitoramento;
                $parametrosEntrada->zerarMultaLocacao   = $isentarLocacao;

                $objRetorno = $webServiceRescisao->calcularRecisao($parametrosEntrada);
                
                $objRetorno->return->termo           = $connumero;
                $objRetorno->return->dataRecisao     = $data;
                $objRetorno->return->percentualMulta = $multa;

                //Transforma em array quando conter apenas um objeto
                if ( count($objRetorno->return->cobrancaMonitoramento) == 1){
                    $cobrancaMonitoramento = $objRetorno->return->cobrancaMonitoramento;
                    $objRetorno->return->cobrancaMonitoramento = array();
                    $objRetorno->return->cobrancaMonitoramento[] = $cobrancaMonitoramento;
                    unset($cobrancaMonitoramento);
                }

                if ( count($objRetorno->return->cobrancaEquipamentos) == 1){
                    $cobrancaEquipamentos = $objRetorno->return->cobrancaEquipamentos;
                    $objRetorno->return->cobrancaEquipamentos = array();
                    $objRetorno->return->cobrancaEquipamentos[] = $cobrancaEquipamentos;
                    unset($cobrancaEquipamentos);
                }

                if ( count($objRetorno->return->equipamentos) === 0){                    
                    $objRetorno->return->equipamentos = $this->_dao->getEquipamentosSiggo($connumero);
                    if (!empty($objRetorno->return->equipamentos) && count($objRetorno->return->equipamentos) > 0) {
                        $retorno->isClienteSiggo = true;
                    }

                } elseif ( count($objRetorno->return->equipamentos) === 1){
                    $equipamentos = $objRetorno->return->equipamentos;
                    $objRetorno->return->equipamentos = array();
                    $objRetorno->return->equipamentos[] = $equipamentos;
                    unset($equipamentos);
                }

                if (is_array($objRetorno->return->cobrancaEquipamentos)) {
                    foreach ($objRetorno->return->cobrancaEquipamentos as $indice => $equipamento) {
                        $dados = $this->_dao->buscarNotaFiscalTitulo(
                            $equipamento->numeroNotaEquipamento,
                            $equipamento->siglaNotaEquipamento,
                            $equipamento->dataVencimentoEquipamento
                        );


                        

                        if ($dados !== false) {
                            $dataRecisao = explode('/', $data);
                            $dataRecisao = $dataRecisao[2] . '-' . $dataRecisao[1] . '-01';
                            $dataRecisao = strtotime($dataRecisao);

                            $dataVencimento = explode('/', $equipamento->dataVencimentoEquipamento);
                            $dataVencimento = $dataVencimento[2] . '-' . $dataVencimento[1] . '-01';
                            $dataVencimento = strtotime($dataVencimento);

                            if (!$dados->notaFiscalStatus || !$dados->tituloStatus) {
                                unset($objRetorno->return->cobrancaEquipamentos[$indice]);
                            } elseif ($dataVencimento < $dataRecisao) {
                                unset($objRetorno->return->cobrancaEquipamentos[$indice]);
                            } else {
                                $objRetorno->return->cobrancaEquipamentos[$indice]->titulo = $dados->tituloNumero;
                            }
                        }
                    }
                }

                if (is_array($objRetorno->return->cobrancaMonitoramento)) {
                    foreach ($objRetorno->return->cobrancaMonitoramento as $indice=> $monitoramento) {
                        $dados = $this->_dao->buscarNotaFiscalTitulo(
                            $monitoramento->numeroNotaMonitoramento,
                            $monitoramento->siglaCobrancaMonitoramento,
                            $monitoramento->dataVencimentoMonitoramento
                        );

                        if ($dados !== false) {
                            $dataRecisao = explode('/', $data);
                            $dataRecisao = $dataRecisao[2] . '-' . $dataRecisao[1] . '-01';
                            $dataRecisao = strtotime($dataRecisao);

                            $dataVencimento = explode('/', $monitoramento->dataVencimentoMonitoramento);
                            $dataVencimento = $dataVencimento[2] . '-' . $dataVencimento[1] . '-01';
                            $dataVencimento = strtotime($dataVencimento);

                            if ($dataVencimento < $dataRecisao) {
                                unset($objRetorno->return->cobrancaMonitoramento[$indice]);
                            } else {
                                $objRetorno->return->cobrancaMonitoramento[$indice]->titulo = $dados->tituloNumero;
                            }
                        }
                    }
                }

                if (is_array($objRetorno->return->equipamentos)) {
                    foreach ($objRetorno->return->equipamentos as $indice => $equipamento) {
                        if (mb_check_encoding($equipamento->item,'UTF-8')) {
                            $objRetorno->return->equipamentos[$indice]->item = utf8_decode($equipamento->item);
                        } else {
                            $objRetorno->return->equipamentos[$indice]->item = $equipamento->item;
                        }
                    }
                }


                //Soma os totais
                $retorno->totais->totalMensalidadeEquipamento         += $objRetorno->return->totalMensalidadeEquipamento;
                $retorno->totais->totalMensalidadeIndevido            += $objRetorno->return->totalMensalidadeIndevido;
                $retorno->totais->totalMultaMensalidadeEquipamento    += $objRetorno->return->totalMultaMensalidadeEquipamento;
                $retorno->totais->totalDiferencaIndevido              += $objRetorno->return->totalDiferencaIndevido;
                $retorno->totais->valorMultaMensalidade               += $objRetorno->return->valorMultaMensalidade;
                $retorno->totais->valorMultaMensalidadeFaltante       += $objRetorno->return->valorMultaMensalidadeFaltante;
                $retorno->totais->valorMultaMensalidadeDevolver       += $objRetorno->return->valorMultaMensalidadeDevolver;
                $retorno->totais->valorPagoIndevidoMonitoramentoTotal += $objRetorno->return->valorPagoIndevidoMonitoramentoTotal;
                $retorno->totais->valorMultaEquipamentoDevolver       += $objRetorno->return->valorMultaEquipamentoDevolver;
                $retorno->totais->valorTotalVigencia                  += $objRetorno->return->valorTotalVigencia;
                $retorno->totais->totalizadorEquipamentoParcela       += $objRetorno->return->totalizadorEquipamentoParcela;

                $retorno->dadosRescisao[] = $objRetorno;
            }

            return $retorno;

        } catch (Exception $e) {
            echo "<div class=\"separador\"></div>";
            echo "<div class=\"mensagem erro\"> " . $e->getMessage() . "</div>";
            exit();
        }
    }

    /**
     * Ação de finalização de rescisão (AJAX)
     * @return  void
     */
    public function finalizarRescisao() {

        // Fix para charset errada
        header('Content-type: text/html; charset=ISO-8859-1');

        try {

            $this->_dao->_query('BEGIN');
// [ORGMKTOTVS-2576] PAULO SERGIO
         //   $arrBaixas = $this->_dao->agruparTitulosWebService();

//            $arrRescisoesBaixa = $this->_dao->baixarTitulos($arrBaixas);
// [ORGMKTOTVS-2576] PAULO SERGIO

            $response = $this->_dao->gerarNotaFiscalRescisao();

            if(empty($this->_dao->erro_registro)){
                $response['resmoid'] = $this->_dao->finalizarRescisao($_POST, $arrRescisoesBaixa);
                $this->_dao->_query('COMMIT');
            }else{
                $response = array('msgRetorno' => utf8_encode($this->_dao->erro_registro));
            }

            if(!empty($this->_dao->titulosBaixaParcial)){
                $response['idsBaixa'] = implode(",", $this->_dao->titulosBaixaParcial);
            }

            $json = json_encode($response, JSON_HEX_AMP);
            echo $json;
            exit;

        }
        catch (Exception $e) {
            $this->_dao->_query('ROLLBACK');
            echo "Erro: " , $e->getMessage(), "\n";
        }
    }

    /**
     * Ação de impressão de carta de rescisão
     * @return  void
     */
    public function imprimir() {

        $resmoid = $this->_getGetParam('resmoid');     
        $titven = $this->_getGetParam('titven');        
        $email = $this->_getGetParam('email');
        $idsbaixa = $this->_getGetParam('idsbaixa');
        $dao = $this->_dao;
        $mensalidadesVencidas = array();

        // Busca dados da rescisão
        $rescisaoMae   = $this->_dao->findRescisao($resmoid);
        $contratos     = $this->_dao->findContratosRescisao($resmoid);
        $multasLocacao = $this->_dao->findMultasLocacaoRescisao($resmoid);
        $taxasRetirada = $this->_dao->findCartaTaxasRetirada($resmoid);
        $taxaNaoRetirada = $this->_dao->findCartaTaxasValorNaoRetirada($resmoid);
        $valorTotalRescisao = $this->_dao->getValorTotalRescisao($resmoid);
        
        $strContratos = $this->_dao->getStrContratos($contratos);
        $maiorDataRescisao = $this->_dao->getMaiorDataRescisao($contratos);

        if($strContratos && $maiorDataRescisao) {
            $mensalidadesVencidas[] = $this->_dao->getMensalidadesVencidas($strContratos, $maiorDataRescisao);
        }

        $rescisaoBaixaIntegral = $this->_dao->getRescisaoBaixa($resmoid, 'I');
        $rescisaoBaixaParcial = $this->_dao->getRescisaoBaixa($resmoid, 'P');

        // Inicializa totalizadores (previne E_NOTICE)
        $totalMultaRescisao = 0;
        $totalTaxaRetirada = 0;
        $totalMultasVincendasDesconto = 0;        

        // Insere no histórico impressão de segunda via
        if ($this->_hasParam('segunda_via')) {
            $this->_dao->insertHistoricoSegundaVia($resmoid);
        }

        require_once $this->_viewPath . 'imprimir.php';
    }

    public function enviarEmail() {
        $files = $this->_dao->enviarEmail();

        if (!is_array($files)) {
            echo json_encode(array(
                'success' => false,
                'message' => $files
            )); die;
        }

        $files['success'] = true;

        echo json_encode($files); die;
    }
}