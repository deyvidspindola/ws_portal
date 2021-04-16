<?php

include  _SITEDIR_ . "lib/Components/PHPExcel/PHPExcel.php";

class FinCreditoFuturoPendentes extends FinCreditoFuturo {

    /**
     * Objeto DAO da classe.
     * 
     * @var CadExemploDAO
     */
    private $dao;

    /**
     * Mensagem de alerta para campos obrigatórios não preenchidos
     * @const String
     */

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";

    /**
     * Mensagem de sucesso para inserção do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_INCLUIR = "Registro incluído com sucesso.";

    /**
     * Mensagem de sucesso para alteração do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_ATUALIZAR = "Registro alterado com sucesso.";

    /**
     * Mensagem de sucesso para exclusão do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_EXCLUIR = "Registro excluído com sucesso.";

    /**
     * Mensagem para nenhum registro encontrado
     * @const String
     */
    const MENSAGEM_NENHUM_REGISTRO = "Nenhum registro encontrado.";

    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    /**
     * Contém dados a serem utilizados na View.
     * 
     * @var stdClass 
     */
    private $view;

    /**
     * Método construtor.
     * 
     * @param CadExemploDAO $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {

        //Verifica o se a variável é um objeto e a instancia na atributo local
        if (is_object($dao)) {
            $this->dao = $dao;
        }

        //Cria objeto da view
        $this->view = new stdClass();
        //Mensagem
        $this->view->mensagemErro = '';
        $this->view->mensagemAlerta = '';
        $this->view->mensagemSucesso = '';

        //Dados para view
        $this->view->dados = null;
        $this->view->xls = false;

        //Filtros/parametros utlizados na view
        $this->view->parametros = null;

        //Status de uma transação 
        $this->view->status = false;
    }

    /**
     * Método index()
     *
     * @return void()
     */
    public function index() {

        try {
            if (isset($_SESSION['flash_message']) && count($_SESSION['flash_message'])) {
                if ($_SESSION['flash_message']['tipo'] == 'sucesso') {
                    $this->view->mensagemSucesso = $_SESSION['flash_message']['mensagem'];
                }

                if ($_SESSION['flash_message']['tipo'] == 'erro') {
                    $this->view->mensagemErro = $_SESSION['flash_message']['mensagem'];
                }
                
                $this->view->parametros = '';
            }

            if (isset($_SESSION['flash_message']['multiplo']) && count($_SESSION['flash_message']['multiplo'])) {

                foreach ($_SESSION['flash_message']['multiplo'] as $mensagem) {

                    if ($mensagem['tipo'] == 'sucesso') {
                        $this->view->mensagemSucesso = $mensagem['mensagem'];
                    }

                    if ($mensagem['tipo'] == 'erro') {
                        $this->view->mensagemErro = $mensagem['mensagem'];
                    }

                    if ($mensagem['tipo'] == 'alerta') {
                        $this->view->mensagemAlerta = $mensagem['mensagem'];
                    }
                }

            }

            unset($_SESSION['flash_message']);

            if (isset($_POST['acao']) && trim($_POST['acao']) == 'pesquisar') {

                $this->limparSessaoPesquisa();

                $this->view->parametros = $this->tratarParametrosPesquisa();

                //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                $this->view->dados = $this->pesquisar($this->view->parametros);
            } else if ($_SESSION['pesquisa_pendentes']['usarSessao'] && $_GET['acao'] == 'pesquisar') {

                $this->view->parametros = (object) $_SESSION['pesquisa_pendentes'];
                //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }  else {
                $this->limparSessaoPesquisa();
            }


            if (isset($_POST['acao']) && trim($_POST['acao']) == 'gerarXls') {

                $this->limparSessaoPesquisa();

                $this->view->parametros = $this->tratarParametrosPesquisa();

                //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                $this->view->xls = $this->gerarXls($this->view->parametros);
               
            }

            
        } catch (ErrorException $e) {
            
            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {
           
            $this->view->mensagemAlerta = $e->getMessage();
        }

        //Inicializa os dados
        $this->inicializarParametros();
        require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_pendentes/index.php";
    }

    /**
     * Método pesquisar()
     * Realiza pesquisa por filtro
     *
     * @return object $resultadoPesquisa(
     */
    private function pesquisar(stdClass $parametros) {

        if ($this->validarPesquisa($parametros)) {
            $parametros->usuario_aprovador = isset($_SESSION['usuario']['oid']) && trim($_SESSION['usuario']['oid']) != '' ? trim($_SESSION['usuario']['oid']) : ''; //cara da sessao
            $resultadoPesquisa = $this->dao->buscarCreditosFuturosPendentes($parametros);

            
            if (count($resultadoPesquisa) == 0) {
                throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
            }

            $this->view->status = TRUE;

            return $resultadoPesquisa;

        }
    }

    /**
     * Método gerarXls()
     * Gera o arquivo xls conforme pesquisa
     *
     * @return string nome do arquivo
     */
    private function gerarXls(stdClass $parametros) {

        // Arquivo modelo para gerar o XLS
        $arquivoModelo = _MODULEDIR_.'Financas/View/fin_credito_futuro_pendentes/template_relatorio_credito_futuro_-_pendente.xlsx';
    
            // Instância PHPExcel
            $reader = PHPExcel_IOFactory::createReader("Excel2007");
                
            // Carrega o modelo
            $PHPExcel = $reader->load($arquivoModelo);
                
            // Processa o relatório
            $relatorio = $this->pesquisar($parametros);

                
            if (count($relatorio)) {
            
                $linha = 8;
                foreach ($relatorio as $row) {

                    //$PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getNumberFormat()->setFormatCode('0');
                    $PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($row->dt_inclusao));

                    $PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($row->protocolo));

                    //$PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode('0');
                    $PHPExcel->getActiveSheet()->setCellValue('C'.$linha, utf8_encode($row->cliente_nome));

                    $PHPExcel->getActiveSheet()->setCellValue('D'.$linha, utf8_encode($row->doc));

                    //verificar coluna E (UF)
                    //$PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode('0');
                    $PHPExcel->getActiveSheet()->setCellValue('E'.$linha, utf8_encode(($row->cliente_uf)));

                    $PHPExcel->getActiveSheet()->setCellValue('F'.$linha, utf8_encode($row->motivo_credito_descricao));

                    //$PHPExcel->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode('0');
                    $PHPExcel->getActiveSheet()->setCellValue('G'.$linha, utf8_encode($row->tipo_desconto_descricao));

                    //$PHPExcel->getActiveSheet()->getStyle('H'.$linha)->getNumberFormat()->setFormatCode('#.##0');
                    $PHPExcel->getActiveSheet()->setCellValue('H'.$linha, utf8_encode($row->valor));

                    $PHPExcel->getActiveSheet()->setCellValue('I'.$linha, utf8_encode($row->forma_aplicacao_descricao));

                    $PHPExcel->getActiveSheet()->setCellValue('J'.$linha, utf8_encode($row->status_descricao)); 

                    $PHPExcel->getActiveSheet()->setCellValue('K'.$linha, utf8_encode($row->usuario_inclusao_nome)); 

                    $PHPExcel->getActiveSheet()->setCellValue('L'.$linha, utf8_encode($row->usuario_avaliador_nome));                    

                    $PHPExcel->getActiveSheet()->getStyle('H'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $PHPExcel->getActiveSheet()->getStyle('D'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    /*
                    $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('F'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('G'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('I'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('J'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    */
                    
                    $linha++;
                }
            
                $PHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);                
                $PHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true); 
                $PHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);                 
            }
            else {
                $PHPExcel->getActiveSheet()->setCellValue('A8', utf8_encode("Nenhum resultado encontrado."));
            }

            $writer = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
            $writer->setPreCalculateFormulas(false);
            
            //Relatorio Registros de Credito Futuro ddmmaaaa hhmmss.xls
            //$file = "Relatorio_Credito_Futuro_-_Pendente_-_".date('Y_m_d').".xlsx";
            $file = "Relatorio_Registros_de_Credito_Futuro_" . date(dmY) . "_" . date(His) . ".xlsx";
            $dir = '/var/www/docs_temporario/';

            if(!file_exists($dir) || !is_writable($dir)) {
                throw new Exception('Houve um erro ao gerar o arquivo.');
            }
            
            //echo "aqui";
            $writer->save($dir.$file);
            

            if (file_exists($dir.$file)) {
                return $dir.$file;
            }

            return false;
    }

    /**
     * Método visualizar()
     * Mostra tela de detalhes do crédito furuto
     *
     * @return void
     */
    public function visualizar() {
        try {
            $parametros = $this->tratarParametros();            
            $this->view->parametros->historico = $this->dao->buscarHistoricoCreditoFuturo($parametros->id);
            $this->view->parametros->cadastro = $this->dao->pesquisarPorID($parametros->id);
            $this->view->parametros->obrigacaoFinanceiraDesconto = $this->dao->buscarObrigacaoFinanceiraDesconto();
            require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_pendentes/visualizar.php";
        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();
        }
    }

    /**
     * Método aprovar()
     * Método que aprova os creditos futuros
     *
     * @return void
     */
    public function aprovar() {

        try {

            $parametros = $this->tratarParametros();

            $creditoFuturoVo = new CreditoFuturoVO();
            $creditoFuturoBo = new CreditoFuturo($this->dao);

            $creditoFuturoVo->id = $parametros->id;
            $creditoFuturoVo->usuarioAvaliador = isset($_SESSION['usuario']['oid']) && trim($_SESSION['usuario']['oid']) != '' ? trim($_SESSION['usuario']['oid']) : ''; //cara da sessao
            $creditoFuturoVo->origem = 1;

            $creditoAprovado = $creditoFuturoBo->aprovar($creditoFuturoVo);

            if ($creditoAprovado == true) {
                $_SESSION['flash_message']['tipo'] = 'sucesso';
                $_SESSION['flash_message']['mensagem'] = 'Crédito(s) aprovado(s) com sucesso.';
                header('Location:fin_credito_futuro_pendentes.php?acao=pesquisar');
            } else {
                $_SESSION['flash_message']['tipo'] = 'erro';
                $_SESSION['flash_message']['mensagem'] = 'Houve um erro no processamento dos dados.';
                header('Location:fin_credito_futuro_pendentes.php?acao=pesquisar');
            }
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            header('Location:fin_credito_futuro_pendentes.php?acao=pesquisar');
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            header('Location:fin_credito_futuro_pendentes.php?acao=pesquisar');
        }
            
    }

    /**
     * Método aprovar()
     * Método que aprova os creditos futuros em massa
     *
     * @return void
     */
    public function aprovarMassa() {

        try {
             $this->dao->begin();

            $parametros = $this->tratarParametros();

            $creditoFuturoVo = new CreditoFuturoVO();
            $creditoFuturoBo = new CreditoFuturo($this->dao);            
            $creditoFuturoVo->usuarioAvaliador = isset($_SESSION['usuario']['oid']) && trim($_SESSION['usuario']['oid']) != '' ? trim($_SESSION['usuario']['oid']) : ''; //cara da sessao
            $creditoFuturoVo->origem = 1;

            $creditoAprovado = true;

            foreach ($parametros->analisar_item as $key => $value) {
                $creditoFuturoVo->id = $value;
                $creditoAprovado = $creditoFuturoBo->aprovar($creditoFuturoVo);
            }

            if ($creditoAprovado == true) {
                $this->dao->commit();
                $_SESSION['flash_message']['tipo'] = 'sucesso';
                $_SESSION['flash_message']['mensagem'] = 'Crédito(s) aprovado(s) com sucesso.';
                header('Location:fin_credito_futuro_pendentes.php?acao=pesquisar');
            } else {
                $this->dao->rollback();
                $_SESSION['flash_message']['tipo'] = 'erro';
                $_SESSION['flash_message']['mensagem'] = 'Houve um erro no processamento dos dados.';
                header('Location:fin_credito_futuro_pendentes.php?acao=pesquisar');
            }

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            header('Location:fin_credito_futuro_pendentes.php?acao=pesquisar');
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            header('Location:fin_credito_futuro_pendentes.php?acao=pesquisar');
        }

    }

    /**
     * Método reprovar()
     * Método que reprova os creditos futuros
     *
     * @return void
     */
    public function reprovar() {

        try {

            $parametros = $this->tratarParametros();

            $creditoFuturoVo = new CreditoFuturoVO();
            $creditoFuturoBo = new CreditoFuturo($this->dao);

            $creditoFuturoVo->id = $parametros->cfooid;
            $creditoFuturoVo->usuarioAvaliador = isset($_SESSION['usuario']['oid']) && trim($_SESSION['usuario']['oid']) != '' ? trim($_SESSION['usuario']['oid']) : ''; //cara da sessao
            $creditoFuturoVo->observacao = $parametros->justificativa;
            $creditoFuturoVo->origem = 1;

            $creditoReprovado = $creditoFuturoBo->reprovar($creditoFuturoVo);


            if ($creditoReprovado == true) {
                $_SESSION['flash_message']['tipo'] = 'sucesso';
                $_SESSION['flash_message']['mensagem'] = 'Crédito(s) reprovado(s) com sucesso.';
                header('Location:fin_credito_futuro_pendentes.php?acao=pesquisar');
            } else {
                $_SESSION['flash_message']['tipo'] = 'erro';
                $_SESSION['flash_message']['mensagem'] = 'Houve um erro no processamento dos dados.';
                header('Location:fin_credito_futuro_pendentes.php?acao=pesquisar');
            }
        } catch (ErrorException $e) {
            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            header('Location:fin_credito_futuro_pendentes.php?acao=pesquisar');
        } catch (Exception $e) {
            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            header('Location:fin_credito_futuro_pendentes.php?acao=pesquisar');
        }
    }


    /**
     * Método validarPesquisa()
     * Válida campos de pesquisa conforme a regra da demanda.
     *
     * @return boolean
     */
    private function validarPesquisa(stdClass $parametros) {
        
        $camposDestaques = array();

        $obrigatoriosPreenchidos = true;
        $periodoAnalise = true;

        //válido se foi informado a data de inicio de inclusao
        if (trim($parametros->cfodt_inclusao_de) == '') {
            $obrigatoriosPreenchidos = false;
            $camposDestaques[] = array(
                'campo' => 'cfodt_inclusao_de'
            );
        }

        //válido se foi informado a data de final de inclusao
        if (trim($parametros->cfodt_inclusao_ate) == '') {
            $obrigatoriosPreenchidos = false;
            $camposDestaques[] = array(
                'campo' => 'cfodt_inclusao_ate'
            );
        }


        if (trim($parametros->cfodt_avaliacao_de) != '' && trim($parametros->cfodt_avaliacao_ate) == '') {
            $periodoAnalise = false;
            $camposDestaques[] = array(
                'campo' => 'cfodt_avaliacao_ate'
            );
        }

        if (trim($parametros->cfodt_avaliacao_ate) != '' && trim($parametros->cfodt_avaliacao_de) == '') {
            $periodoAnalise = false;
            $camposDestaques[] = array(
                'campo' => 'cfodt_avaliacao_de'
            );
        }

        $this->view->dados = $camposDestaques;

        if ($obrigatoriosPreenchidos == false) {
            throw new Exception("O período de inclusão é uma informação obrigatória.");
        }

        if ($periodoAnalise == false) {
            throw new Exception("Deve ser informado data inicial e final.");
        }
        
        return true;
    }

    /**
     * Método inicializarParametros()
     * Inicializar parâmetros.
     *
     * @return boolean
     */
    private function inicializarParametros() {
        
        $this->view->parametros->cfodt_inclusao_de = isset($this->view->parametros->cfodt_inclusao_de) ? trim($this->view->parametros->cfodt_inclusao_de) : '';
        $this->view->parametros->cfodt_inclusao_ate = isset($this->view->parametros->cfodt_inclusao_ate) ? trim($this->view->parametros->cfodt_inclusao_ate) : '';
        $this->view->parametros->cliente_nome = isset($this->view->parametros->cliente_nome) ? trim($this->view->parametros->cliente_nome) : '';
        $this->view->parametros->tipo_pessoa = isset($this->view->parametros->tipo_pessoa) ? trim($this->view->parametros->tipo_pessoa) : '';
        $this->view->parametros->cpf = isset($this->view->parametros->cpf) ? trim($this->view->parametros->cpf) : '';
        $this->view->parametros->cnpj = isset($this->view->parametros->cnpj) ? trim($this->view->parametros->cnpj) : '';
        $this->view->parametros->contrato = isset($this->view->parametros->contrato) ? trim($this->view->parametros->contrato) : '';
        $this->view->parametros->cfostatus = isset($this->view->parametros->cfostatus) ? trim($this->view->parametros->cfostatus) : '';
        $this->view->parametros->cfousuoid_inclusao = isset($this->view->parametros->cfousuoid_inclusao) ? trim($this->view->parametros->cfousuoid_inclusao) : '';
        //variavel que seta usuario que icluiu crédito futuro
        $this->view->parametros->usuarioInclusaoCreditoFuturo = $this->dao->buscarUsuarioInclusaoCreditoFuturo();

    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     * 
     * @return stdClass Parametros tradados
     * 
     * @retrun stdClass
     */
    public function tratarParametrosPesquisa() {
        
        $temp = array();
        if (isset($_POST['acao']) && $_POST['acao'] = 'pesquisar') {
            foreach ($_POST as $key => $value) {
                if (isset($_POST[$key])) {
                    $temp[$key] = trim($_POST[$key]);
                } elseif (isset($_SESSION['pesquisa_pendentes'][$key])) {
                    $temp[$key] = trim($_SESSION['pesquisa_pendentes'][$key]);
                }
                $_SESSION['pesquisa_pendentes'][$key] = $temp[$key];
            }
        }

        $_SESSION['pesquisa_pendentes']['usarSessao'] = TRUE;

        return (object) $_SESSION['pesquisa_pendentes'];
    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     * 
     * @return stdClass Parametros tradados
     * 
     * @retrun stdClass
     */
    private function tratarParametros() {
        $retorno = new stdClass();

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                if (!is_array($_POST[$key])) {
                    $retorno->$key = isset($_POST[$key]) ? htmlentities(strip_tags($value)) : '';
                } else {
                    $retorno->$key = isset($_POST[$key]) ? $value : '';
                }
            }
            //Limpa o POST
            //unset($_POST);
        } 

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {

                //Verifica se atributo já existe e não sobrescreve.
                if (!isset($retorno->$key)) {
                    if (!is_array($_GET[$key])) {
                        $retorno->$key = isset($_GET[$key]) ? htmlentities(strip_tags($value)) : '';
                    } else {
                        $retorno->$key = isset($_GET[$key]) ? $value : '';
                    }
                }
            }
            //Limpa o GET
            //unset($_GET);
        }


        return $retorno;
    }

}

?>
