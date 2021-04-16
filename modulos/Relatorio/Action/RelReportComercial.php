<?php
/**
 * Report Comercial
 *
 * @package Relatório
 * @author  Kleber Goto Kihara <kleber.kihara@meta.com.br>
 */
class RelReportComercial {

    /**
     * Objeto DAO.
     *
     * @var RelReportComercialDao
     */
    private $dao;

    /**
     * Objeto Parâmetros.
     *
     * @var stdClass
     */
    private $param;

    /**
     * Objeto View.
     *
     * @var stdClass
     */
    private $view;

    const DIRETORIO_REPORT_COMERCIAL = '/var/www/docs_temporario/';

    /**
     * Mensagem de alerta - campos obrigatórios.
     *
     * @const String
     */
    const MENSAGEM_ALERTA_CAMPO_OBRIGATORIO = "Existem campos obrigatórios não preenchidos.";

    /**
     * Mensagem de alerta - campos obrigatórios.
     *
     * @const String
     */
    const MENSAGEM_ALERTA_SEM_REGISTRO = "Nenhum registro encontrado.";

    /**
     * Mensagem de erro - processamento de dados.
     *
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    /**
     * Mensagem de informação - campos obrigatórios.
     *
     * @const String
     */
    const MENSAGEM_INFO_CAMPO_OBRIGATORIO = "Campos com * são obrigatórios.";

    /**
     * Mensagem de sucesso - registro cadastrado.
     *
     * @const String
     */
    const MENSAGEM_SUCESSO_REGISTRO_CADASTRADO = "O relatório será gerado, em breve será disponibilizado para download.";

    /**
     * Mensagem de sucesso - registro excluído.
     *
     * @const String
     */
    const MENSAGEM_SUCESSO_REGISTRO_EXCLUIDO = "Registro excluído com sucesso.";
    const MENSAGEM_SUCESSO_REGISTROS_EXCLUIDOS = "Registros excluídos com sucesso.";

    /**
     * Mensagem - arquivo na fila.
     *
     * @const String
     */
    const MENSAGEM_ARQUIVO_NA_FILA = "Aguarde, o arquivo está sendo gerado.";

    /**
     * Mensagem - arquivo sem registro.
     *
     * @const String
     */
    const MENSAGEM_ARQUIVO_SEM_REGISTRO = "O relatório não foi gerado, não foram encontrados registros.";

    /**
     * Mensagem - arquivo com erro.
     *
     * @const String
     */
    const MENSAGEM_ARQUIVO_COM_ERRO = "O relatório não foi gerado, houve um erro no processamento dos dados.";

    /**
     * Método construtor.
     *
     * @param RelReportComercialDao $dao Objeto DAO.
     *
     * @return Void
     * @todo Parar a execução e apresentar o erro padrão (caso não receba $dao).
     */
    public function __construct($dao = null) {
        /*
         * Cria o objeto Dao.
         */
        if (is_object($dao)) {
            $this->dao = $dao;
        } else {
            // ToDo
        }

        /*
         * Cria o objeto Parâmetros.
         */
        $this->param = new stdClass();

        $this->tratarParametros();

        /*
         * Cria o objeto View.
         */
        $this->view = new stdClass();

        // Caminho do diretório
        $this->view->caminho = _MODULEDIR_ . 'Relatorio/View/rel_report_comercial/';

        // Campos incorretos
        $this->view->campos = array();

        // Dados
        $this->view->dados = null;

        // Mensagens
        $this->view->mensagem->alerta  = '';
        $this->view->mensagem->erro    = '';
        $this->view->mensagem->info    = self::MENSAGEM_INFO_CAMPO_OBRIGATORIO;
        $this->view->mensagem->sucesso = '';

        // Status
        $this->view->status = true;
    }

    /**
     * Método padrão da classe.
     *
     * @return Void
     */
    public function index() {
        try {
            $this->view->dados->regiaoComercialZona = $this->dao->buscarRegiaoComercialZona();
            $this->view->dados->reportComercial     = $this->dao->buscarReportComercial();
        } catch (ErrorException $e) {
            $this->view->mensagem->erro = $e->getMessage();
            $this->view->status         = false;
        } catch (Exception $e) {
            $this->view->mensagem->alerta = $e->getMessage();
            $this->view->status           = false;
        }

        require_once $this->view->caminho.'index.php';
    }

    /**
     * Método que busca os Clientes.
     *
     * @return Void
     */
    public function buscarCliente() {
        $this->param->term = isset($this->param->term) ? utf8_decode($this->param->term) : '';

        if (trim($this->param->term) != '') {
            $cliente = $this->dao->buscarCliente($this->param);

            foreach ($cliente as $chave => $registro) {
                $cliente[$chave]->id      = utf8_encode($registro->clioid);
                $cliente[$chave]->label   = utf8_encode($registro->clinome.' - '.formata_cgc_cpf($registro->clidocumento));
                $cliente[$chave]->value   = utf8_encode($registro->clinome);
                $cliente[$chave]->clinome = utf8_encode($registro->clinome);
            }
        } else {
            $cliente = array();
        }

        echo json_encode($cliente);
    }

    /**
     * Método que cria as tabelas temporárias.
     *
     * @param stdClass $param Parâmetros.
     *
     * @return Void
     * @throws ErrorException
     */
    private function criarTabelaTemporaria(stdClass $param) {
        $reportComercialDao = new ReportComercialDao($param);

        $this->dao->criarTabelaTemporaria($reportComercialDao->criarDadosEntrada());
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarDadosSaida());
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarCalculoEntrada());
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarAggMonitoramento());
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarAgrupamentoSaida());
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarBaseAtiva());
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarBaseNfDetalhada());

        $this->dao->criarTabelaTemporaria($reportComercialDao->criarBaseAtivaMesEspecifico('anterior'));
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarBaseAtivaMesEspecifico('atual'));
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarFreteiroMesEspecifico('anterior'));
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarFreteiroMesEspecifico('atual'));
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarReceitaMonitoramentoMesEspecifico('anterior'));
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarReceitaMonitoramentoMesEspecifico('atual'));
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarContratoMesEspecifico('anterior'));
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarContratoMesEspecifico('atual'));
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarContratoMesEspecifico('todos'));
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarContratoAgg());
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarSimulacaoPorGrupo());
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarSimulacaoFreteiro(false));
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarSimulacaoFreteiro(true));
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarContratoFaturado(true));
        $this->dao->criarTabelaTemporaria($reportComercialDao->criarReportComercial(true));
    }

    /**
     * Método que exclui os reports comerciais.
     *
     * @return Void
     */
    public function excluirReportComercial() {
        $this->dao->begin();

        if (isset($this->param->rpcoid) && is_array($this->param->rpcoid)) {
            try {
                $rpcoid = array();

                foreach($this->dao->buscarReportComercial($this->param) as $registro) {
                    if ($registro->rpcprocessando == 'f') {
                        $rpcoid[] = $registro->rpcoid;

                        if (file_exists(self::DIRETORIO_REPORT_COMERCIAL . $registro->rpcarquivo)) {
                            unlink(self::DIRETORIO_REPORT_COMERCIAL . $registro->rpcarquivo);
                        }
                    }
                }

                $this->param->rpcoid = $rpcoid;

                $this->dao->excluirReportComercial($this->param);

                $this->dao->commit();

                if (count($this->param->rpcoid) > 1) {
                    $this->view->mensagem->sucesso = self::MENSAGEM_SUCESSO_REGISTROS_EXCLUIDOS;
                } else {
                    $this->view->mensagem->sucesso = self::MENSAGEM_SUCESSO_REGISTRO_EXCLUIDO;
                }
            } catch (ErrorException $e) {
                $this->dao->rollback();

                $this->view->mensagem->erro = $e->getMessage();
                $this->view->status         = false;
            } catch (Exception $e) {
                $this->dao->rollback();

                $this->view->mensagem->alerta = $e->getMessage();
                $this->view->status           = false;
            }
        }

        $this->index();
    }

    /**
     * Gera o arquivo CSV.
     *
     * @param type $arquivo
     * @param stdClass $registros
     *
     * @return Boolean
     */
    public function gerarCSV($arquivo, $registros) {
        require_once _SITEDIR_ . 'lib/Components/CsvWriter.php';

        if (!file_exists(self::DIRETORIO_REPORT_COMERCIAL)) {
            return false;
        } elseif (!is_array($registros)) {
            return false;
        }

        $csvWriter = new CsvWriter(self::DIRETORIO_REPORT_COMERCIAL . $arquivo, ';', '', true);
        $csvWriter->addLine(array(
            'dmv',
            'cliente',
            'vl_base',
            'vl_up',
            'vl_rtvc',
            'vl_down',
            'vl_outros',
            'vl_churn',
            'vl_inst_mes_antr',
            'vl_inst_mes_atual'
        ));

        foreach ($registros as $registro) {
            $csvWriter->addLine(array(
                $registro->dmv,
                $registro->cliente,
                $registro->vl_base,
                $registro->vl_up,
                $registro->vl_rtvc,
                $registro->vl_down,
                $registro->vl_outros,
                $registro->vl_churn,
                $registro->vl_inst_mes_antr,
                $registro->vl_inst_mes_atual
            ));
        }

        if (!file_exists(self::DIRETORIO_REPORT_COMERCIAL . $arquivo)) {
            return false;
        }

        return true;
    }

    /**
     * Método que processa os reports comerciais.
     *
     * @return Void
     * @throws ErrorException
     */
    public function processarReportComercial() {
        $param = new stdClass();
        $param->rpcarquivo     = '';
        $param->rpcprocessando = false;

        while ($registro = $this->dao->buscarReportComercialCron($param)) {
            $dadosReportComercial = new stdClass();
            $dadosReportComercial->rpcoid = $registro->rpcoid;

            echo date('H:i:s')." - ARQUIVO: Iniciado o processamento da solicitação ".$registro->rpcoid."\n";
                        
            $dadosReportComercial->rpcprocessando = true;

            if (!$this->dao->atualizarReportComercial($dadosReportComercial)) {
                echo date('H:i:s')." - ERRO: ".self::MENSAGEM_ERRO_PROCESSAMENTO."\n";                
            }
            
            $this->dao->begin();

            try {                

                $registro->rpcdmv = $this->dao->buscarReportComercialDmvCron($registro);

                $this->criarTabelaTemporaria($registro);

                if (!$reportComercial = $this->dao->buscarTmpReportComercial($registro)) {
                    throw new Exception(self::MENSAGEM_ALERTA_SEM_REGISTRO);
                }

                $dadosReportComercial->rpcarquivo = date('d-m-Y')."_".date('His').".csv";

                if(!$this->gerarCSV($dadosReportComercial->rpcarquivo, $reportComercial)) {
                    throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
                }

                $this->dao->commit();
            } catch (ErrorException $e) {
                $this->dao->rollback();

                echo date('H:i:s')." - ERRO: ".$e->getMessage()."\n";

                $dadosReportComercial->rpcarquivo = self::MENSAGEM_ARQUIVO_COM_ERRO;
            } catch (Exception $e) {
                $this->dao->rollback();

                echo date('H:i:s')." - ALERTA: ".$e->getMessage()."\n";

                $dadosReportComercial->rpcarquivo = self::MENSAGEM_ARQUIVO_SEM_REGISTRO;
            }

            $dadosReportComercial->rpcprocessando = false;

            if (!$this->dao->atualizarReportComercial($dadosReportComercial)) {
                echo date('H:i:s')." - ERRO: ".self::MENSAGEM_ERRO_PROCESSAMENTO."\n";;
            }

            echo date('H:i:s')." - ARQUIVO: Encerrado o processamento da solicitação ".$registro->rpcoid."\n";
            echo "====================================================================================================\n";
        }
    }

    /**
     * Método que salva os reports comerciais.
     *
     * @return Void
     */
    public function salvarReportComercial() {
        $this->validarParametros();

        if ($this->view->status) {
            $this->dao->begin();

            try {
                $this->param->rpcoid = $this->dao->inserirReportComercial($this->param);

                if ($this->param->rpcoid) {
                    $this->dao->inserirReportComercialDmv($this->param);
                }

                $this->dao->commit();

                $this->view->mensagem->sucesso = self::MENSAGEM_SUCESSO_REGISTRO_CADASTRADO;
            } catch (ErrorException $e) {
                $this->dao->rollback();

                $this->view->mensagem->erro = $e->getMessage();
                $this->view->status         = false;
            } catch (Exception $e) {
                $this->dao->rollback();

                $this->view->mensagem->alerta = $e->getMessage();
                $this->view->status           = false;
            }
        }

        $this->index();
    }

    /**
     * Método que instância os dados do $_POST e $_GET.
     *
     * @return Void
     */
    private function tratarParametros() {
        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $this->param->$key = isset($_POST[$key]) ? $value : '';
            }
        }

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {
                if (!isset($this->param->$key)) {
                    $this->param->$key = isset($_GET[$key]) ? $value : '';
                }
            }
        }
    }

    /**
     * Método que valida os dados do $_POST e $_GET.
     *
     * @return Void
     * @todo Validar data.
     */
    private function validarParametros() {
        if (empty($this->param->rpcdt_referencia)) {
            $this->view->campos[] = array(
                'campo'    => 'rpcdt_referencia',
                'mensagem' => utf8_encode('Campo obrigatório')
            );
            $this->view->status   = false;
        } else {
            // ToDo
        }

        if (empty($this->param->rpcdrczoid)) {
            $this->view->campos[] = array(
                'campo'    => 'rpcdrczoid',
                'mensagem' => utf8_encode('Campo obrigatório')
            );
            $this->view->status   = false;
        }

        /* if (empty($this->param->rpcclioid)) {
            $this->view->campos[] = array(
                'campo'    => 'rpcclinome',
                'mensagem' => utf8_encode('Campo obrigatório')
            );
            $this->view->status   = false;
        } */

        if (!$this->view->status) {
            $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_CAMPO_OBRIGATORIO;
        }
    }

}