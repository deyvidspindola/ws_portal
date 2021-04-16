<?php

require_once _MODULEDIR_ . "/Financas/DAO/FinControleFiscalRemessasDAO.php"; //STI 83807

require_once _MODULEDIR_ . "/Financas/DAO/FinControleFiscalEquipamentoMovelDAO.class.php";

require_once _MODULEDIR_ . "/Financas/DAO/FinControleFiscalInstalacoesDAO.class.php";

require_once _SITEDIR_ . "lib/Components/CsvWriter.php";
require_once _SITEDIR_ . "lib/funcoes.php";

class FinControleFiscalRemessas {

    private $view;
    private $representantes=array();
    private $fornecedores=array();
    
    
    

    public function __construct() {

        global $conn;

        $this->view = new stdClass();
        //Filtros/parametros utlizados na view
        $this->view->parametros = null;
        $this->view->tiposMovimentacao = array();
        $this->view->estoqueremessaSatus = array();
        $this->view->retornaRepresentante = array();

        try {
            $this->dao = new FinControleFiscalRemessasDAO($conn);
            $this->view->tiposMovimentacao = $this->dao->tiposMovimentacao();
            $this->view->estoqueremessaSatus = $this->dao->estoqueRemessaStatus();
            $this->view->retornaRepresentante = $this->dao->retornaRepresentante();
            $this->view->retornaFornecedor = $this->dao->retornaFornecedor();
        } catch (Exception $e) {
            $this->view->msg = $e->getMessage();
        }
    }

    public function setRetornaView() {
        return $this->view;
    }

    public function pesquisa() {

        // $this->_testaFaturamentoUnificado();

        include (_MODULEDIR_ . 'Financas/View/fin_controle_fiscal_remessas/pesquisar' . (isset($_POST ['ajax']) ? '.ajax' : '') . '.php');
    }

    public function pesquisar() {

        $parametros = $_POST;
        $this->view->parametros = $_POST;

        $parametros ['dt_ini'] = $_POST ['dt_ini'];
        $parametros ['dt_fim'] = $_POST ['dt_fim'];
        $parametros ['nRemessa'] = $_POST ['nRemessa'];
        $parametros ['nfRemessa'] = $_POST ['nfRemessa'];
        $parametros ['tipoRelatorio'] = $_POST ['tipoRelatorio'];
        $parametros ['tipoMovimentacao'] = $_POST ['tipoMovimentacao'];
        $parametros ['statusRemessa'] = $_POST ['statusRemessa'];
        $parametros ['repreRespRem'] = $_POST ['repreRespRem'];
        $parametros ['repreRespDest'] = $_POST ['repreRespDest'];
        $parametros ['repreFornDest'] = $_POST ['repreFornDest'];
        $parametros ['nSerie'] = $_POST ['nSerie'];
        $parametros ['numero_pedido'] = $_POST ['numero_pedido'];

        try {
            $this->view->remessa = $this->dao->pesquisaEstoqueRemessa($parametros);
            
            if($this->view->remessa!==false){
                foreach ($this->view->remessa as $row){
                    if ($row['esrrelroid_emitente'] > 0) {
                        $this->representantes[$row['esrrelroid_emitente']] = $row['esrrelroid_emitente'];
                    }
                    if ($row['esrrelroid'] > 0) {
                        $this->representantes[$row['esrrelroid']] = $row['esrrelroid'];
                    }
                    if ($row['esrforoid'] > 0) {
                        $this->fornecedores[$row['esrforoid']] = $row['esrforoid'];
                    }
                }


                $this->representantes=$this->dao->getRepresentante($this->representantes);
                $this->fornecedores=$this->dao->getFornecedor($this->fornecedores);
            }
            
            
        } catch (Exception $e) {
            echo json_encode(array(
                "status" => "error",
                "message" => $e->getMessage(),
                "redirect" => ""
            ));
            return;
        }


        include (_MODULEDIR_ . 'Financas/View/fin_controle_fiscal_remessas/resultado_remessa' . (isset($_POST ['ajax']) ? '.ajax' : '') . '.php');
    }

    
    /**
     * Método gerarPrevia()
     * Processa/Cria arquivo de prévia e retorna o caminho.
     * 
     * @param stdClass $dados
     * @return string $arquivo_previa
     */
    public function gerarRemessaCSV() {

//        $parametros = $_POST;
//        $parametros ['dt_ini'] = $_POST ['dt_ini'];
//        $parametros ['dt_fim'] = $_POST ['dt_fim'];
//        $parametros ['nRemessa'] = $_POST ['nRemessa'];
//        $parametros ['nfRemessa'] = $_POST ['nfRemessa'];
//        $parametros ['tipoRelatorio'] = $_POST ['tipoRelatorio'];
//        $parametros ['tipoMovimentacao'] = $_POST ['tipoMovimentacao'];
//        $parametros ['statusRemessa'] = $_POST ['statusRemessa'];
//        $parametros ['repreRespRem'] = $_POST ['repreRespRem'];
//        $parametros ['repreRespDest'] = $_POST ['repreRespDest'];
//        $parametros ['nSerie'] = $_POST ['nSerie'];

       // $this->view->remessa = $this->dao->pesquisaEstoqueRemessa($_POST);



        try {
            // Gerar Relatório
            $this->view->remessa = $this->dao->pesquisaEstoqueRemessa($_POST);

                       
            if($this->view->remessa!==false){
                
                
                foreach ($this->view->remessa as $row){
                    if ($row['esrrelroid_emitente'] > 0) {
                        $this->representantes[$row['esrrelroid_emitente']] = $row['esrrelroid_emitente'];
                    }
                    if ($row['esrrelroid'] > 0) {
                        $this->representantes[$row['esrrelroid']] = $row['esrrelroid'];
                    }
                    if ($row['esrforoid'] > 0) {
                        $this->fornecedores[$row['esrforoid']] = $row['esrforoid'];
                    }
                }


                $this->representantes=$this->dao->getRepresentante($this->representantes);
                $this->fornecedores=$this->dao->getFornecedor($this->fornecedores);
                
                
                if($_POST['tipoRelatorio']=='S'){
                    $content .= "Nº de Série;Cód. Produto;Produto;Qtde;Remetente;CNPJ Remetente;UF Remetente;Destinatário;CNPJ Destinatário;UF Destinatário;Nº Remessa;Data Remessa;NF Remessa;Nº Pedido;Status\n";
                }elseif($_POST['tipoRelatorio']!='NF'){
                    $content .= "Cód. Produto;Produto;Qtde;Remetente;CNPJ Remetente;UF Remetente;Destinatário;CNPJ Destinatário;UF Destinatário;Nº Remessa;Data Remessa;NF Remessa;Nº Pedido;Status\n";
                }else{
                    $content .= "Remetente;CNPJ Remetente;UF Remetente;Destinatário;CNPJ Destinatário;UF Destinatário;Nº Remessa;Data Remessa;NF Remessa;Nº Pedido;Status\n";
                }
                
                foreach ($this->view->remessa as $row) {
                    
                    
                    if($_POST['tipoRelatorio']=='S'){
                         $content .=$row['esrinumero_serie'].";";
                     }

                    if($_POST['tipoRelatorio']!='NF'){
                         $content .=$row['prdoid'].";";
                         $content .=$row['prdproduto'].";";
                         $content .=$row['quantidade'].";";
                     }
                     $content .=$this->representantes[$row['esrrelroid_emitente']]['nome'].";";
                     $content .=formata_cgc_cpf($this->representantes[$row['esrrelroid_emitente']]['cnpj']).";";
                     $content .=$this->representantes[$row['esrrelroid_emitente']]['uf'].";";
                     if($row['esrforoid']>0){
                        $content .='Fornecedor: ' .$this->fornecedores[$row['esrforoid']]['nome'].";";
                        $content .=formata_cgc_cpf($this->fornecedores[$row['esrforoid']]['cnpj']).";";
                        $content .=$this->fornecedores[$row['esrforoid']]['uf'].";";
                     }else{
                        $content .=$this->representantes[$row['esrrelroid']]['nome'].";";
                        $content .=formata_cgc_cpf($this->representantes[$row['esrrelroid']]['cnpj']).";";
                        $content .=$this->representantes[$row['esrrelroid']]['uf'].";";
                     }
                     $content .=$row['esroid'].";";
                     $content .=$row['data'].";";
                     $content .=$row['esrpnfno_numero'].";";
                     $content .=$row['esrpnfoid'].";";
                     $content .=$row['ersdescricao'].";\n";
                    
                    
                }
            }

          return $content;
            
            
            
            
        } catch (Exception $e) {


            echo json_encode(array(
                "codigo" => 1,
                "msg" => 'Falha ao gerar planilha CSV.',
            ));
            exit;
        }
       // include (_MODULEDIR_ . 'Financas/View/fin_controle_fiscal_remessas/arquivo_csv' . (isset($_POST ['ajax']) ? '.ajax' : '') . '.php');
    }

    public function pesquisarCliente() {

        global $conn;

        $dao = new FinControleFiscalEquipamentoMovelDAO($conn);

        echo $dao->pesquisarCliente($_POST['nomeCliente']);
    }

// INSTALAÇÕES /////////////////////////////////////////////////////////////////
    public function instalacoes() {

        include (_MODULEDIR_ . 'Financas/View/fin_controle_fiscal_remessas/instalacoes' . (isset($_POST ['ajax']) ? '.ajax' : '') . '.php');
    }
    
    public function carregaRepresentante() {

        global $conn;

        $dao = new FinControleFiscalInstalacoesDAO($conn);

        echo $dao->representante();
    }
    
    public function instalacoesPesquisar() {

        global $conn;
         
        $dao = new FinControleFiscalInstalacoesDAO($conn);

        $dataInicio = $_POST['pesquisa_data_inicio'];
        $dao->setDataInicio($dataInicio);

        $dataFim = $_POST['pesquisa_data_fim'];
        $dao->setDataFim($dataFim);

        $contrato = $_POST['pesquisa_contrato'];
        $dao->setContrato($contrato);

        $serie = $_POST['pesquisa_n_serie'];
        $dao->setSerie($serie);

        $tipoRelatorio = $_POST['pesquisa_tipo_relatorio'];
        $dao->setTipoRelatorio($tipoRelatorio);

        $cliente = $_POST['cliente'];
        $dao->setCliente($cliente);

        $representante = $_POST['representante'];
        $dao->setRepresentante($representante);

        $possuiNFRetornoSimbolico = $_POST['pesquisa_possui_nf_retorno_simbolico'];
        $dao->setPossuiNFRetornoSimbolico($possuiNFRetornoSimbolico);

        $possuiNFRemessaSimbolico = $_POST['pesquisa_possui_nf_remessa_simbolico'];
        $dao->setPossuiNFRemessaSimbolico($possuiNFRemessaSimbolico);
        
        $nfRemessaSimbolico = $_POST['pesquisa_nf_remessa_simbolico'];
        $dao->setNfRemessaSimbolico($nfRemessaSimbolico);
        
        $nfRetornoSimbolico = $_POST['pesquisa_nf_retorno_simbolico'];
        $dao->setNfRetornoSimbolico($nfRetornoSimbolico);
        
        try {

            $this->view->instalacoes = $dao->getOrdemServico('OS_INSTALACAO');
            
        } catch (Exception $e) {
            echo json_encode(array(
                "status" => "error",
                "message" => $e->getMessage(),
                "redirect" => ""
            ));
            return;
        } 

        include (_MODULEDIR_ . 'Financas/View/fin_controle_fiscal_remessas/resultado_instalacoes' . (isset($_POST ['ajax']) ? '.ajax' : '') . '.php');
    }

// RETIRADAS ///////////////////////////////////////////////////////////////////
    public function retiradas() {

        include (_MODULEDIR_ . 'Financas/View/fin_controle_fiscal_remessas/retiradas' . (isset($_POST ['ajax']) ? '.ajax' : '') . '.php');
    }

    public function retiradasPesquisar() {

        global $conn;
         
        $dao = new FinControleFiscalInstalacoesDAO($conn);

        $dataInicio = $_POST['pesquisa_data_inicio'];
        $dao->setDataInicio($dataInicio);

        $dataFim = $_POST['pesquisa_data_fim'];
        $dao->setDataFim($dataFim);

        $contrato = $_POST['pesquisa_contrato'];
        $dao->setContrato($contrato);

        $serie = $_POST['pesquisa_n_serie'];
        $dao->setSerie($serie);

        $tipoRelatorio = $_POST['pesquisa_tipo_relatorio'];
        $dao->setTipoRelatorio($tipoRelatorio);

        $cliente = $_POST['cliente'];
        $dao->setCliente($cliente);

        $representante = $_POST['representante'];
        $dao->setRepresentante($representante);

        $possuiNFRetornoSimbolico = $_POST['pesquisa_possui_nf_retorno_simbolico'];
        $dao->setPossuiNFRetornoSimbolico($possuiNFRetornoSimbolico);

        $possuiNFRemessaSimbolico = $_POST['pesquisa_possui_nf_remessa_simbolico'];
        $dao->setPossuiNFRemessaSimbolico($possuiNFRemessaSimbolico);
        
        $nfRemessaSimbolico = $_POST['pesquisa_nf_remessa_simbolico'];
        $dao->setNfRemessaSimbolico($nfRemessaSimbolico);
        
        $nfRetornoSimbolico = $_POST['pesquisa_nf_retorno_simbolico'];
        $dao->setNfRetornoSimbolico($nfRetornoSimbolico);
        
        try {

            $this->view->retiradas = $dao->getOrdemServico('OS_RETIRADA');
            
        } catch (Exception $e) {
            echo json_encode(array(
                "status" => "error",
                "message" => $e->getMessage(),
                "redirect" => ""
            ));
            return;
        } 

        include (_MODULEDIR_ . 'Financas/View/fin_controle_fiscal_remessas/resultado_retiradas' . (isset($_POST ['ajax']) ? '.ajax' : '') . '.php');
    }

/// EQUIPAMENTO MÓVEL //////////////////////////////////////////////////////////
    public function equipamentoMovel() {

        include (_MODULEDIR_ . 'Financas/View/fin_controle_fiscal_remessas/equipamento_movel' . (isset($_POST ['ajax']) ? '.ajax' : '') . '.php');
    }

    public function equipamentoMovelPesquisar() {

        global $conn;

        $controleMovelAba = $_POST['controle_movel_aba'];

        $dataInicio = $_POST['pesquisa_data_inicio'];

        $dataFim = $_POST['pesquisa_data_fim'];

        $tipoRelatorio = $_POST['pesquisa_tipo_relatorio'];

        $remessa = $_POST['pesquisa_nf_remessa'];

        $serie = $_POST['pesquisa_n_serie'];

        $contrato = $_POST['pesquisa_contrato'];

        $cliente = $_POST['pesquisa_id_cliente'];

        $possuiNFRemessa = $_POST['pesquisa_possui_nf_remessa'];

        $numeroPedido = $_POST['pesquisa_numero_pedido'];

        $dao = new FinControleFiscalEquipamentoMovelDAO($conn);

        $dao->setAba($controleMovelAba);

        $dao->setDataInicio($dataInicio);

        $dao->setDataFim($dataFim);

        $dao->setContrato($contrato);

        $dao->setSerie($serie);

        $dao->setRemessa($remessa);

        $dao->setTipoRelatorio($tipoRelatorio);

        $dao->setCliente($cliente);

        $dao->setPossuiNFRemessa($possuiNFRemessa);

        $dao->setNumeroPedido($numeroPedido);

        try {

            if ($tipoRelatorio === 'serial') {

                $this->view->equipamentoMovel = $dao->consultaTipoSerial();
            } else {

                $this->view->equipamentoMovel = $dao->consultaTipoProduto();
            }
        } catch (Exception $e) {
            echo json_encode(array(
                "status" => "error",
                "message" => $e->getMessage(),
                "redirect" => ""
            ));
            return;
        }

        include (_MODULEDIR_ . 'Financas/View/fin_controle_fiscal_remessas/resultado_equipamento_movel' . (isset($_POST ['ajax']) ? '.ajax' : '') . '.php');
    }
    
    
     public function gerarContratoControleCSV() {

        global $conn;

        $controleMovelAba = $_POST['controle_movel_aba'];

        $dataInicio = $_POST['pesquisa_data_inicio'];

        $dataFim = $_POST['pesquisa_data_fim'];

        $tipoRelatorio = $_POST['pesquisa_tipo_relatorio'];

        $remessa = $_POST['pesquisa_nf_remessa'];

        $serie = $_POST['pesquisa_n_serie'];

        $contrato = $_POST['pesquisa_contrato'];

        $cliente = $_POST['pesquisa_id_cliente'];

        $possuiNFRemessa = $_POST['pesquisa_possui_nf_remessa'];

        $numeroPedido = $_POST['pesquisa_numero_pedido'];

        $dao = new FinControleFiscalEquipamentoMovelDAO($conn);

        $dao->setAba($controleMovelAba);

        $dao->setDataInicio($dataInicio);

        $dao->setDataFim($dataFim);

        $dao->setContrato($contrato);

        $dao->setSerie($serie);

        $dao->setRemessa($remessa);

        $dao->setTipoRelatorio($tipoRelatorio);

        $dao->setCliente($cliente);

        $dao->setPossuiNFRemessa($possuiNFRemessa);

        $dao->setNumeroPedido($numeroPedido);

       

            if ($tipoRelatorio === 'serial') {

                $result=$this->view->equipamentoMovel = $dao->consultaTipoSerial();
            } else {

                $result=$this->view->equipamentoMovel = $dao->consultaTipoProduto();
            }
            $content="";
                    if ($result[2]['aba'] === 'envio') {
                        $content.="Enviado;";
                    } else {
                        $content.="Retorno;";
                    }
                    if ($result[2]['tipo_relatorio'] === 'serial') {

                        $content.="Contrato;Nº Série;";
                    }

            $content.="Código/Produto;NCM;Quantidade;Valor;Cliente;CPF/CNPJ;UF;NF Remessa;Nº Pedido\n";
            
           

                    while ($row = pg_fetch_object($result[0])) {

                      
                            $content.=$row->data_envio.";";

                            if ($result[2]['tipo_relatorio'] === 'serial') {

                                $content.=$row->contrato.";";
                                $content.=$row->serie.";";

                            }

                            $content.=$row->codigo_produto . " - " . utf8_encode($row->descricao_produto).";";
                            $content.=$row->codigo_ncm.";";
                            $content.=$row->quantidade.";";
                            
                                $precoUnitario = '';

                                if ($row->preco_unitario_1) {
                                    $precoUnitario = $row->preco_unitario_1;
                                } else if ($row->preco_unitario_2) {
                                    $precoUnitario = $row->preco_unitario_2;
                                } else {
                                    $precoUnitario = $row->preco_unitario_3;
                                }
                                

                                $content.='R$ ' . number_format($precoUnitario, 2, ',', '.').";";
                                $content.=utf8_encode($row->nome_cliente).";";
                                $content.=formata_cgc_cpf($row->cliente_cnpj).";";
                                $content.=$row->cliente_uf.";";
                                $content.=$row->nf_remessa.";";
                           
                                $content.=$row->numero_pedido."\n";
                        
                    }
                
            
            
            
            
            
            
            return $content;
            
    }
    
    

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     *
     * @return stdClass Parametros tradados
     *
     * @retrun stdClass
     */
    private function tratarParametros($parametros = null) {
        if (is_null($parametros)) {
            $retorno = new stdClass();
        } else {
            $retorno = $parametros;
        }


        if (count($_POST) > 0) {
            unset($_GET);
            foreach ($_POST as $key => $value) {
                $retorno->$key = isset($_POST[$key]) ? $value : '';
            }
        }

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {

                //Verifica se atributo jÃ¡ existe e nÃ£o sobrescreve.
                if (!isset($retorno->$key)) {
                    $retorno->$key = isset($_GET[$key]) ? $value : '';
                }
            }
        }
        return $retorno;
    }

}
