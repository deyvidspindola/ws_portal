<?php

require_once _SITEDIR_ . "lib/Components/PHPExcel/PHPExcel.php";

require_once _MODULEDIR_ . 'Financas/DAO/FinRescisaoDAO2.php';
require_once 'FinRescisaoHelpers.php';

use infra\Helper\Response;

class FinRescisao2
{
    protected $_dao;
    protected $_viewPath;

    public function __construct()
    {
        $this->_dao = new FinRescisaoDAO2();
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
        //if (INTEGRACAO) $message = ParametroIntegracaoTotvs::message('O gerador automático de títulos');
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
        require_once $this->_viewPath . 'novo2.php';
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
             
            // verificar data de inicio de vigencia
            $contratos = $this->dataInicioVigencia($contratos); 
        }

        require_once $this->_viewPath . 'novo_contratos2.php';
    }

    /**
     * Busca as multas (itens "multáveis") dos contratos selecionados
     * @return  void
     */
    public function buscaMultas() {

        // Fix para charset errada
        header('Content-type: text/html; charset=ISO-8859-1');
        
        $contratos = $this->_getPostParam('connumero');
        $clioid = $this->_getPostParam('clioid');

        if (!$clioid && count($contratos) == 1) {
            $clioid = $this->_dao->getClioidByContrato($contratos[0]);
        }

        try {
           
            // Dados da finalização de rescisão
            $resmfax = $this->_getPostParam('resmfax');
            $listaMotivos = $this->_dao->findMotivos();
            $listaStatusRescisao = $this->_dao->findStatusRescisao();
            $listaFormasCobranca = $this->_dao->findFormasCobranca();

            // STI 84189
            $emailCliente = $this->_dao->getEmailCliente($clioid);
            // FIM STI 84189

            $dados['connumero'] = !empty($_POST['connumero']) ? $_POST['connumero'] : '';
            $dados['dataPreRescisao'] = !empty($_POST['solicitacao']) ? $_POST['solicitacao'] : '';
            $dados['percentualMulta'] = !empty($_POST['multa']) ? $_POST['multa'] : '';
            $dados['isentarMonitoramento'] = !empty($_POST['isentar_monitoramento'])  ? $_POST['isentar_monitoramento'] : '';
            $dados['isentarLocacao'] = !empty($_POST['isentar_locacao']) ? $_POST['isentar_locacao'] : '';
            $dados['calcularDescontos'] = !empty($_POST['calcular_descontos']) ? $_POST['calcular_descontos'] : '';
           
            //Valida os parametros
            if (empty($dados) || !is_array($dados)) {
                throw new Exception('Há campo não preenchidos.');
            }
             if(empty($dados['dataPreRescisao']) || !is_array($dados['dataPreRescisao']))
                throw new Exception('Há campo não preenchidos.');
            
            if(empty($dados['percentualMulta']) || !is_array($dados['percentualMulta']))
                throw new Exception('Há campo não preenchidos.');
             
            foreach ($dados['dataPreRescisao'] as $dataPre) {
                if (empty($dataPre)) {
                    throw new Exception('Há campo não preenchidos.');
                }
            }
            foreach ($dados['percentualMulta'] as $percMulta) {
                if (empty($percMulta)) {
                    throw new Exception('Há campo não preenchidos.');
                }
            }
            
            // buscar contratos
            $contratosMultasLocacao = array();
            $contratosMultasLocacao = $this->_dao->findContratosNovaRescisao($dados);

            if ($contratosMultasLocacao) {
                
                // verificar data de inicio de vigencia
                $contratosMultasLocacao = $this->dataInicioVigencia($contratosMultasLocacao);
                
                foreach ($contratosMultasLocacao as $indice => $contrato) {

                    // buscar valores do contrato locação + acessorios e monitoramento
                    $retornoValores = $this->_dao->findValoresMonitoramentoLocacaoAcessorios($contrato);

                    if (!empty($retornoValores)) {
                        $contratosMultasLocacao[$indice] = array_merge($contrato, $retornoValores[0]);
                    }
                    
                    // data pre rescisão
                    foreach ($dados['dataPreRescisao'] as $indMe => $dataPre) {
                        if (intval($contratosMultasLocacao[$indice]['connumero']) == $indMe) {
                            $contratosMultasLocacao[$indice]['dataPreRescisao'] = $dataPre;
                        }
                    }
                    // percentual da multa 
                    foreach ($dados['percentualMulta'] as $indM => $multa) {

                        if (intval($contratosMultasLocacao[$indice]['connumero']) == $indM) {
                            $contratosMultasLocacao[$indice]['percentualMulta'] = $multa;
                        }
                    }
                    // flag isentar monitoramento
                    if (!empty($dados['isentarMonitoramento'])) {
                        foreach ($dados['isentarMonitoramento'] as $indMe => $isentarMoni) {
                            if (intval($contratosMultasLocacao[$indice]['connumero']) == $indMe) {
                                $contratosMultasLocacao[$indice]['isentar_monitoramento'] = $isentarMoni;
                            }
                        }
                    }
                    // flag isentar locação
                    if (!empty($dados['isentarLocacao'])) {
                        foreach ($dados['isentarLocacao'] as $indMe => $isentarLoc) {
                            if (intval($contratosMultasLocacao[$indice]['connumero']) == $indMe) {
                                $contratosMultasLocacao[$indice]['isentar_locacao'] = $isentarLoc;
                            }
                        }
                    }
                    // flag descontos 
                    if (!empty($dados['calcularDescontos'])) {
                        foreach ($dados['calcularDescontos'] as $indMe => $calcularDesc) {
                            if (intval($contratosMultasLocacao[$indice]['connumero']) == $indMe) {
                                $contratosMultasLocacao[$indice]['calcular_descontos'] = $calcularDesc;
                            }
                        }
                    }
                }
            }

            // calculo valores multa monitoramento
            $contratosMultasLocacao = $this->calcularValoresMonitoramentoLocacaoAcessorios($contratosMultasLocacao);

            // buscar valor taxa de retirada
            $contratosMultasLocacao = $this->buscaValortaxaRetirada($contratosMultasLocacao);
            
            // buscar contrato com TRC faturado e nao cobrar a taxa de rerirada
            $contratosMultasLocacao = $this->buscaContratoTRC($contratosMultasLocacao);
            
            // calculo pro rata
            $contratosMultasLocacao = $this->calcularProRataMonitoramentoLocacaoAcessorios($contratosMultasLocacao);
            
            // calculo totais resciso
            $totalRescisao = $this->calculoTotaisRescisao($contratosMultasLocacao);

           // ORGMKTOTVS-[3596] Gerar Excel
            $arquivo =  $this->gerarExcelSimulacaoRescisao($contratosMultasLocacao, $totalRescisao);
           
            require_once $this->_viewPath . 'novo_multas2.php';
            
        } catch (Exception $e) {
            echo "<div class=\"separador\"></div>";
            echo "<div class=\"mensagem erro\"> " . $e->getMessage() . "</div>";
            exit();
        }
    }

    /**
     * Calcula os valores de multa de monitoramento e multa de locacao + acessórios
     * @return  array
     */
    private function calcularValoresMonitoramentoLocacaoAcessorios($contratosMultasLocacao) {

        $totalMonitoramento = 0;
        $totalLocacaoAcessorios = 0;
         
        foreach ($contratosMultasLocacao as $indice => $contrato) {
           
            //verifica se há fidelizacao
            $dataInicioVigenciaFidelizacao = null;
            $dataInicioSubstituicao = null;
            $dataInicioVigencia = null;

            //verifica se há fidelizacao
            if (isset($contrato['hfcdt_fidelizacao'])) {
                $dataInicioVigenciaFidelizacao = $contrato['hfcdt_fidelizacao'];
            }

            // verifica se há data de substituicao (transferência/upgrade ou downgrade)
            if (!empty($contrato['dt_vigencia_ultimo_contrato'])) {
                $dataInicioSubstituicao = $contrato['dt_vigencia_ultimo_contrato'];
            }

            if (!empty($dataInicioSubstituicao) && !empty($dataInicioVigenciaFidelizacao)) {
                if ($dataInicioSubstituicao > $dataInicioVigenciaFidelizacao) {
                    $dataInicioVigencia = $dataInicioSubstituicao;
                } else {
                    $dataInicioVigencia = $dataInicioVigenciaFidelizacao;
                }
            } else if (empty($dataInicioSubstituicao) && !empty($dataInicioVigenciaFidelizacao)) {
                $dataInicioVigencia = $dataInicioVigenciaFidelizacao;
            } else if (empty($dataInicioVigenciaFidelizacao) && !empty($dataInicioSubstituicao)) {
                $dataInicioVigencia = $dataInicioSubstituicao;
            } else {
                $dataInicioVigencia = $contrato['condt_ini_vigencia'];
            }

             // Calcula meses do contrato
            if (isset($contrato['meses_aditivo']) && $contrato['meses_aditivo'] > 0) {
                $mesesVigencia = $contrato['meses_aditivo'];
            } else {
                $mesesVigencia = $contrato['conprazo_contrato'];
            }
            
            // Calcula a diferença entre meses faltantes
            $dataInicio = $dataInicioVigencia;
             
            $dataInicio = strtotime($dataInicio);
            $dataFim = strtotime("+{$mesesVigencia} months", strtotime($dataInicioVigencia));
            $dataPreRescisao = strtotime(str_replace("/", "-", $contrato['dataPreRescisao']));

            $dataInicioD = new DateTime(date('d-m-Y', $dataInicio));
            $dataFimD = new DateTime(date('d-m-Y', $dataFim));
            $dataPre = new DateTime(date('d-m-Y', $dataPreRescisao));
 
            $diffUtilizado =  $dataInicioD->diff($dataPre);
            $mesesUtilizados = floor($diffUtilizado->days / 30.416666); //(365/12)
                    
            $mesesFaltantes = 0;
         
            if ($mesesVigencia > 0) {
                               
                if ($dataFimD > $dataPre) {
                    
                    $diff2 = $dataFimD->diff($dataPre);
                    $mesesFaltantes = ($diff2->days /  30.41666) < 1 ? ceil($diff2->days / 30.41666) : round($diff2->days / 30.41666);
                    
                    $mesesUtilizados = intval($mesesVigencia) - intval($mesesFaltantes);
                }
            } 
             
            $valorMultalocacao = 0;
            $valorMultaAcessorio = 0;
            $valorMultaMonitoramento = 0;
            $totalMultaMonitoramento = 0;
            $totalMultaLocacaoAcessorios = 0;
            
            // Porcentagem da multa
            $porcentagemMulta = $contrato['percentualMulta'] ? intval($contrato['percentualMulta']) : 0;

            // Flag de isenção monitoramento
            if (!$contrato['isentar_monitoramento']) {

                // Calcula a multa do monitoramento
                $valorMonitoramento = $contrato['valor_monitoramento'] ? floatval($contrato['valor_monitoramento']) * $mesesFaltantes : 0;
                $valorMultaMonitoramento = $porcentagemMulta * $valorMonitoramento / 100;

                // Cálculo do total- monitoramento
                $totalMultaMonitoramento = floatval($valorMultaMonitoramento);
                $totalMultaMonitoramento = ($totalMultaMonitoramento > 0) ? $totalMultaMonitoramento : 0;
            }

            // Flag de isenção locação
            if (!$contrato['isentar_locacao']) {

                // Calcula a multa de locação + acessórios
                $valorlocacao = $contrato['valor_modulo'] ? round(floatval($contrato['valor_modulo']) * $mesesFaltantes, 2) : 0;
                $valorAcessorio = $contrato['valor_acessorio'] ? round(floatval($contrato['valor_acessorio']) * $mesesFaltantes, 2) : 0;

                $valorMultalocacao = round(($porcentagemMulta * $valorlocacao) / 100, 2);
                $valorMultaAcessorio = round(($porcentagemMulta * $valorAcessorio) / 100, 2);
              
                // nao cobrar multa de locacao se o contrato for refidelizado com redução 
                if(strpos(strtolower($contrato['gctdescricao']), 'redução') !== false){
                    //busca ultima NF se nao tiver valor faturado nao calcular multa de locacao
                    $ultimaNotaFiscal = $this->_dao->buscarUltimaNotaFaturada($contrato);
                    
                    if($ultimaNotaFiscal['faturado_locacao'] <= 0){
                        $valorMultalocacao = 0;
                        $valorMultaAcessorio = 0;
                       
                    }
                }
                 
                // Cálculo do total - locação e acessórios
                $totalMultaLocacaoAcessorios = round(floatval($valorMultalocacao) + floatval($valorMultaAcessorio), 2);
                $totalMultaLocacaoAcessorios = ($totalMultaLocacaoAcessorios > 0) ? $totalMultaLocacaoAcessorios : 0;
            }

            $contratosMultasLocacao[$indice]['dataInicioVigencia'] = $dataInicioVigencia;
            $contratosMultasLocacao[$indice]['mesesUtilizados'] = $mesesUtilizados;
            $contratosMultasLocacao[$indice]['mesesVigencia'] = $mesesVigencia;
            $contratosMultasLocacao[$indice]['mesesFaltantes'] = $mesesFaltantes;
            $contratosMultasLocacao[$indice]['totalMultaMonitoramento'] = $totalMultaMonitoramento;
            $contratosMultasLocacao[$indice]['totalMultaLocacaoAcessorios'] = $totalMultaLocacaoAcessorios;
            
            $valorL = $contrato['valor_modulo'] ? floatval($contrato['valor_modulo']) : 0;
            $valorA = $contrato['valor_acessorio'] ? floatval($contrato['valor_acessorio']) : 0;
            $contratosMultasLocacao[$indice]['valor_locacao_acessorios'] = round(floatval($valorL + $valorA), 2);
               
           
        }
       return $contratosMultasLocacao;
    }
    
    /**
     * Calcula os valores de pro rata de monitoramento e de locacao + acessórios
     * @return  array
     */
    private function calcularProRataMonitoramentoLocacaoAcessorios($contratosMultasLocacao) {
       
        
        foreach ($contratosMultasLocacao as $indice => $contrato) {
           
            $proRataLocacao = 0;
            $proRataMonitoramento = 0;
            
            // Flag para calcular os descontos - pró rata locação e monitoramento
            if (!empty($contrato['calcular_descontos'])){

                $dataPreRescisao = date('Y-m-d', strtotime(str_replace("/", "-", $contrato['dataPreRescisao'])));
                $dataPre = explode("-", $dataPreRescisao);
                $diaRescisao =  $dataPre[2];
                
                // buscar o valor faturado LOCAÇÃO E ACESSSORIOS na NF do mes atual ou posteior a data de rescisão
                $notaFiscalValoresLocacaoEAcessorios = $this->_dao->buscarvaloresLocacaoUltimaNotaFaturada($contrato, $dataPreRescisao);
               
                // buscar o valor faturado MONIOTRAMENTO na NF do mes atual ou posteior a data de rescisão
                $notaFiscalValoresMonitoramento = $this->_dao->buscarvaloresMonitoramentoUltimaNotaFaturada($contrato, $dataPreRescisao);
                
                $valorFaturadoLocacao = $notaFiscalValoresLocacaoEAcessorios[0]['vl_locacao_faturado'];
                $valorDevidoLocacao = floatval(($valorFaturadoLocacao / 30) * intval($diaRescisao));
                $proRataLocacao = round($valorFaturadoLocacao - $valorDevidoLocacao, 2);

                $valorFaturadoMonitoramento = $notaFiscalValoresMonitoramento[0]['vl_monitoramento_faturado'];
                $valorDevidoMonitoramento = floatval(($valorFaturadoMonitoramento/30)* intval($diaRescisao));
                $proRataMonitoramento = round($valorFaturadoMonitoramento - $valorDevidoMonitoramento, 2);
   
            }
      
            $contratosMultasLocacao[$indice]['descontoProRataLocacao'] = $proRataLocacao;
            $contratosMultasLocacao[$indice]['descontoProRataMonitoramento'] = $proRataMonitoramento;
        }
      
       return $contratosMultasLocacao;
    }
    
    /**
     * Calculo dos valores totais da rescis?o
     * @return  array
     */
    private function calculoTotaisRescisao($contratosMultasLocacao) {
        
        $totalRescisao = array();
        
        $totalRescisao['totalMonitoramento'] = 0;
        $totalRescisao['totalLocacaoAcessorios'] = 0;
        $totalRescisao['totalTaxaRetirada'] = 0;
        $totalRescisao['totalProRataLocacaoeAcessorios'] = 0;
        $totalRescisao['totalProRataMonitoramento'] = 0;
        
        
        foreach ($contratosMultasLocacao as $indice => $contrato) {
          
            $totalRescisao['totalMonitoramento'] += floatval($contrato['totalMultaMonitoramento']);
            $totalRescisao['totalLocacaoAcessorios'] += floatval($contrato['totalMultaLocacaoAcessorios']);
            
            if(!$contrato['TRC_faturado']){
                $totalRescisao['totalTaxaRetirada'] += floatval($contrato['taxa_retirada']);
            }
            
            if(!empty($contrato['descontoProRataLocacao'])){
                $totalRescisao['totalProRataLocacaoeAcessorios'] += floatval($contrato['descontoProRataLocacao']);
            }
            
            if(!empty($contrato['descontoProRataMonitoramento'])){
                $totalRescisao['totalProRataMonitoramento'] += floatval($contrato['descontoProRataMonitoramento']);
            }
        }
        
        $totalRescisao['totalRescisaoSemDescontos'] = floatval($totalRescisao['totalMonitoramento'] + $totalRescisao['totalLocacaoAcessorios'] + $totalRescisao['totalTaxaRetirada']);
        $totalRescisao['totalRescisao'] = floatval($totalRescisao['totalMonitoramento'] + $totalRescisao['totalLocacaoAcessorios'] + $totalRescisao['totalTaxaRetirada']);
        $totalRescisao['totalRescisao'] -= floatval($totalRescisao['totalProRataLocacaoeAcessorios'] + $totalRescisao['totalProRataMonitoramento']);
        
        return $totalRescisao;
    }

     /**
     * Data de inicio de vigencia dos contratos
     * @return  array
     */
    private function dataInicioVigencia($contratos){

        for ($i=0; $i<sizeof($contratos); $i++) {
                
            // busca fidelização 
            $retornoFidelizacao = $this->_dao->verificaFidelizacao($contratos[$i]);
           
            if (!empty($retornoFidelizacao)) {
                $contratos[$i] = array_merge($contratos[$i], $retornoFidelizacao[0]);
            }
            
            //  busca qual tipo de fidelização -- transferencia up/down
           if (!empty($contratos[$i]['connumero_antigo'])) {

                // transferencia
                if (strpos($contratos[$i]['msubdescricao'], "TRANSF") !== false) {

                    $retornoTransferencia = $this->_dao->verificaTranferencia($contratos[$i]);

                    if (!empty($retornoTransferencia)) {
                        $contratos[$i] = array_merge($contratos[$i], $retornoTransferencia[0]);
                    }
                }

                // up ou down -- consulta dados da OS
                if (strpos($contratos[$i]['msubdescricao'], "UP") !== false || strpos($contratos[$i]['msubdescricao'], "DOWN") !== false) {

                    $retornoUpDown = $this->_dao->verificaUpDown($contratos[$i]);
                    
                    if (!empty($retornoUpDown)) {
                        $contratos[$i] = array_merge($contratos[$i], $retornoUpDown[0]);
                    }
                }
            }
        }

        return $contratos;
    }

    /**
     * Buscar Taxa de retirada dos contratos
     * @return  array
     */
    private function buscaValortaxaRetirada($contratosMultasLocacao) {

        foreach ($contratosMultasLocacao as $indice => $contrato) {

            $taxaRetirada = $this->_dao->buscavalorTaxaRetirada($contrato);

            if (!empty($taxaRetirada)) {

                foreach ($taxaRetirada as $taxa) {

                    $contratosMultasLocacao[$indice]['descricao_obrigacao_retirada'] = $taxa['descricao_obrigacao'];
                    $contratosMultasLocacao[$indice]['taxa_retirada'] = floatval($taxa['taxa_retirada']);
                    $contratosMultasLocacao[$indice]['obrobroid_retirada'] = $taxa['obrobroid_retirada'];
                }
            } else {
                $contratosMultasLocacao[$indice]['descricao_obrigacao_retirada'] = "";
                $contratosMultasLocacao[$indice]['taxa_retirada'] = 0.00;
                $contratosMultasLocacao[$indice]['obrobroid_retirada'] = "";
            }
        }
        return $contratosMultasLocacao;
    }

    /**
     * Buscar Contratos com TRC
     * @return  array
     */
    private function buscaContratoTRC($contratosMultasLocacao) {

        // buscar id das taxa de extensão na tabela de parâmetros
        $obrigacaoTaxaExtensao = $this->_dao->buscaIdTaxadeExtensao();

        foreach ($contratosMultasLocacao as $indice => $contrato) {

            $contratosMultasLocacao[$indice]['TRC_faturado'] = false;
            $contratosMultasLocacao[$indice]['TRC_faturado_nfloid'] = "";
            $contratosMultasLocacao[$indice]['TRC_faturado_obrigacao'] = "";
            $contratosMultasLocacao[$indice]['TRC_faturado_nfiobroid'] = "";

            // buscar obrigacao financeira vinculada na O.S
            $obrigacaoFinanceiraOSconcluida[] = $this->_dao->buscaOFContratoTrcOsConcluido($contrato);
          
            if ($obrigacaoFinanceiraOSconcluida) {
                // juntar as OF (encontrada na OS e taxa de extensão)
                $obrigacaoFinanceiraOSconcluida = array_merge($obrigacaoFinanceiraOSconcluida, $obrigacaoTaxaExtensao);
            } else {
                $obrigacaoFinanceiraOSconcluida = $obrigacaoTaxaExtensao;
            }
            // buscar ultimo RPS do faturamento 
            $contratoTRCFaturado = $this->_dao->buscaUltimoRPSContratoTRC($contrato);
           
            if (!empty($contratoTRCFaturado)) {

                foreach ($contratoTRCFaturado as $contratoFaturado) {

                    foreach ($obrigacaoFinanceiraOSconcluida as $obrigacao) {

                        if ($obrigacao['nfiobroid'] == $contratoFaturado['nfiobroid']) {
                            
                            $contratosMultasLocacao[$indice]['TRC_faturado'] = true;
                            $contratosMultasLocacao[$indice]['TRC_faturado_nfloid'] = $contratoFaturado['nfloid'];
                            $contratosMultasLocacao[$indice]['TRC_faturado_nfiobroid'] = $contratoFaturado['nfiobroid'];
                            $contratosMultasLocacao[$indice]['TRC_faturado_obrigacao'] = $contratoFaturado['obrobrigacao'];
                        }
                    }
                }
            }
        }
        return $contratosMultasLocacao;
    }

    private function gerarExcelSimulacaoRescisao($contratosMultasLocacao, $totalRescisao) {

         $dir = '/var/www/arq_financeiro/simulacao_rescisao/';

        if (!file_exists($dir) || !is_writable($dir)) {
            if (!mkdir($dir)) {
                unset($PHPExcel);
                throw new Exception('Houve um erro ao gerar o arquivo.');
            }
        }
         chmod($dir, 0755);

         // apagar arquivos
        $diretorio = dir($dir);
        while ($arq = $diretorio->read()) {
            if (($arq != '.') && ($arq != '..')) {
                unlink($dir . $arq);
            }
        }
        $diretorio->close();

        // Arquivo modelo para gerar o XLS
        $arquivoModelo = _MODULEDIR_ . 'Financas/View/fin_rescisao/template_simulacao_rescisao.xlsx';

        // Instância PHPExcel
        $reader = PHPExcel_IOFactory::createReader("Excel2007");

        // Carrega o modelo
        $PHPExcel = $reader->load($arquivoModelo);

        $linha = 8;
        if (!empty($contratosMultasLocacao)) {

            foreach ($contratosMultasLocacao as $row) {

                $PHPExcel->getActiveSheet()->setCellValue('A' . $linha, utf8_encode($row['connumero']));
                $PHPExcel->getActiveSheet()->setCellValue('B' . $linha, utf8_encode($row['veiplaca']));
                $PHPExcel->getActiveSheet()->setCellValue('C' . $linha, utf8_encode($row['eqcdescricao']));
                $PHPExcel->getActiveSheet()->setCellValue('D' . $linha, utf8_encode(date('d/m/Y', strtotime($row['dataInicioVigencia']))));
                $PHPExcel->getActiveSheet()->setCellValue('E' . $linha, utf8_encode($row['dataPreRescisao']));
                $PHPExcel->getActiveSheet()->setCellValue('F' . $linha, utf8_encode($row['mesesUtilizados']));
                $PHPExcel->getActiveSheet()->setCellValue('G' . $linha, utf8_encode($row['mesesVigencia']));
                $PHPExcel->getActiveSheet()->setCellValue('H' . $linha, utf8_encode($row['mesesFaltantes']));
                $PHPExcel->getActiveSheet()->setCellValue('I' . $linha, utf8_encode($row['valor_monitoramento']));
                $PHPExcel->getActiveSheet()->setCellValue('J' . $linha, utf8_encode($row['valor_locacao_acessorios']));
                $PHPExcel->getActiveSheet()->setCellValue('K' . $linha, utf8_encode($row['totalMultaMonitoramento']));
                $PHPExcel->getActiveSheet()->setCellValue('L' . $linha, utf8_encode($row['totalMultaLocacaoAcessorios']));

                if (!$row['TRC_faturado']) {
                    $taxaRetirada = $row['taxa_retirada'];
                } else {
                    $taxaRetirada = 0;
                }
                $PHPExcel->getActiveSheet()->setCellValue('M' . $linha, utf8_encode($taxaRetirada));
                $PHPExcel->getActiveSheet()->setCellValue('N' . $linha, utf8_encode(0));

                // FORMATAR VALORES
                $PHPExcel->getActiveSheet()->getStyle('A' . $linha)->getNumberFormat()->setFormatCode('0');
                $PHPExcel->getActiveSheet()->getStyle('F' . $linha)->getNumberFormat()->setFormatCode('0');
                $PHPExcel->getActiveSheet()->getStyle('G' . $linha)->getNumberFormat()->setFormatCode('0');
                $PHPExcel->getActiveSheet()->getStyle('H' . $linha)->getNumberFormat()->setFormatCode('0');

                // FORMATO MOEDA
                $PHPExcel->getActiveSheet()->getStyle('I' . $linha)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                $PHPExcel->getActiveSheet()->getStyle('J' . $linha)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                $PHPExcel->getActiveSheet()->getStyle('K' . $linha)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                $PHPExcel->getActiveSheet()->getStyle('L' . $linha)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                $PHPExcel->getActiveSheet()->getStyle('M' . $linha)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                $PHPExcel->getActiveSheet()->getStyle('N' . $linha)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');

                // ALINHAMENTO
                $PHPExcel->getActiveSheet()->getStyle('A' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('B' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('C' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('D' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('E' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('F' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('G' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('H' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('I' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('J' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('K' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('L' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('M' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('N' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $linha++;
            }

            if (!empty($totalRescisao)) {

                // TOTAIS
                $PHPExcel->getActiveSheet()->setCellValue('K' . $linha, utf8_encode($totalRescisao['totalMonitoramento']));
                $PHPExcel->getActiveSheet()->setCellValue('L' . $linha, utf8_encode($totalRescisao['totalLocacaoAcessorios']));
                $PHPExcel->getActiveSheet()->setCellValue('M' . $linha, utf8_encode($totalRescisao['totalTaxaRetirada']));
                $PHPExcel->getActiveSheet()->setCellValue('N' . $linha, utf8_encode(0));

                // FORMATAR TOTAIS
                $PHPExcel->getActiveSheet()->getStyle('K' . $linha)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                $PHPExcel->getActiveSheet()->getStyle('L' . $linha)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                $PHPExcel->getActiveSheet()->getStyle('M' . $linha)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                $PHPExcel->getActiveSheet()->getStyle('N' . $linha)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');

                $PHPExcel->getActiveSheet()->getStyle('K' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('L' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('M' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('N' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                // TOTAL RESCISAO
                $linha++;
                $PHPExcel->getActiveSheet()->setCellValue('M' . $linha, utf8_encode('TOTAL'));
                $PHPExcel->getActiveSheet()->setCellValue('N' . $linha, utf8_encode($totalRescisao['totalRescisaoSemDescontos']));

                $PHPExcel->getActiveSheet()->getStyle('N' . $linha)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                $PHPExcel->getActiveSheet()->getStyle('M' . $linha)->applyFromArray(array('fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFFF00'))));
                $PHPExcel->getActiveSheet()->getStyle('N' . $linha)->applyFromArray(array('fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFFF00'))));
                $PHPExcel->getActiveSheet()->getStyle('M' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('N' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            }

            $PHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        } else {
            $PHPExcel->getActiveSheet()->setCellValue('A' . $linha, utf8_encode("Nenhum resultado encontrado."));
        }

        $arquivo = "simulacao_rescisao_" . date('dmY') . "_" . date('Hi') . ".xlsx";
       
        $writer = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');

        // salvar
        $writer->save($dir . $arquivo);
        $PHPExcel->disconnectWorksheets();
       
        unset($PHPExcel);

        if (file_exists($dir . $arquivo)) {
            return $dir . $arquivo;
        }

        return false;
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
       
            $response = $this->_dao->gerarNotaFiscalRescisao();
          
            if(empty($this->_dao->erro_registro)){
                $response['resmoid'] = $this->_dao->finalizarRescisao($_POST);
                $this->_dao->_query('COMMIT');
            }else{
                $response = array('msgRetorno' => utf8_encode($this->_dao->erro_registro));
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
        
        require_once $this->_viewPath . 'imprimir_2.php';
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