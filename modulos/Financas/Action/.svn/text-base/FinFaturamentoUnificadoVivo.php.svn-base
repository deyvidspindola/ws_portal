<?php

/**
 * Classe FinFaturamentoUnificadoVivo.
 * Camada de regra de negócio.
 *
 * @package  Financas
 * @author   Ricardo Bonfim <ricardo.bonfim@meta.com.br>
 *
 */
class FinFaturamentoUnificadoVivo {

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
    private $parametrosFaturamento;
    private $arquivo;

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

        //Filtros/parametros utlizados na view
        $this->view->parametros = null;

        $this->parametrosFaturamento = new stdClass();

        $this->view->mostrarConsulta = false;

        $this->view->mostrarArquivo = false;

        $this->view->nomeArquivo = '';

        $this->view->mostrarRelatorioPreFaturamento = false;

        $this->arquivo = null;
    }

    /**
     * Método padrão da classe.
     *
     * Reponsável também por realizar a pesquisa invocando o método privado
     *
     * @return void
     */
    public function index() {
        try {

            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            if (isset($this->view->parametros->acao) && $this->view->parametros->acao == 'consultarResumo') {
                $this->view->mostrarConsulta = $this->consultarResumo();
                $parametros = array(
                    $this->view->parametros->dataReferencia,
                    $this->view->parametros->tipoContrato,
                    $this->view->parametros->servicosFaturados,
                    $this->view->parametros->tipoPessoa,
                    $this->view->parametros->nome,
                    $this->view->parametros->documentoCliente,
                    str_replace('-', '', $this->view->parametros->placa),
                    $_SESSION['usuario']['oid']
                );
                $this->view->parametros->parametrosFormatado = implode('|', $parametros);
            }
        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();
        }

        $dadosProcesso = (object) $this->verificarProcesso(false);
        if ($dadosProcesso->codigo == 2) {
            $this->view->processoExecutando = true;
            $this->view->mensagemFaturamento = $dadosProcesso->msg;
            $this->view->parametrosProcesso = $dadosProcesso->parametros;
        } else {
            $this->view->processoExecutando = false;
        }

        require_once _MODULEDIR_ . "Financas/View/fin_faturamento_unificado_vivo/index.php";
    }

    /**
     * Popula os arrays para os combos de estados e cidades
     *
     * @return void
     */
    private function inicializarParametros() {

        //Verifica se os parametro existem, senão iniciliza todos
        $this->view->parametros->dataReferencia = isset($this->view->parametros->dataReferencia) && !empty($this->view->parametros->dataReferencia) ? trim($this->view->parametros->dataReferencia) : "";

        $this->view->parametros->tipoContrato = isset($this->view->parametros->tipoContrato) && !empty($this->view->parametros->tipoContrato) ? trim($this->view->parametros->tipoContrato) : "";

        $this->view->parametros->servicosFaturados = isset($this->view->parametros->servicosFaturados) && !empty($this->view->parametros->servicosFaturados) ? trim($this->view->parametros->servicosFaturados) : "";

        $this->view->parametros->tipoPessoa = isset($this->view->parametros->tipoPessoa) && !empty($this->view->parametros->tipoPessoa) ? trim($this->view->parametros->tipoPessoa) : "";

        $this->view->parametros->nomeCliente = isset($this->view->parametros->nomeCliente) && !empty($this->view->parametros->nomeCliente) ? trim($this->view->parametros->nomeCliente) : "";

        $this->view->parametros->razaoSocial = isset($this->view->parametros->razaoSocial) && !empty($this->view->parametros->razaoSocial) ? trim($this->view->parametros->razaoSocial) : "";

        $this->view->parametros->cnpj = isset($this->view->parametros->cnpj) && !empty($this->view->parametros->cnpj) ? trim($this->view->parametros->cnpj) : "";

        $this->view->parametros->cpf = isset($this->view->parametros->cpf) && !empty($this->view->parametros->cpf) ? trim($this->view->parametros->cpf) : "";

        $this->view->parametros->placa = isset($this->view->parametros->placa) && !empty($this->view->parametros->placa) ? preg_replace('/[^a-zA-Z0-9]/', '', trim($this->view->parametros->placa)) : "";
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
                $retorno->$key = isset($_POST[$key]) ? $value : '';
            }
        }

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {

                //Verifica se atributo já existe e não sobrescreve.
                if (!isset($retorno->$key)) {
                    $retorno->$key = isset($_GET[$key]) ? $value : '';
                }
            }
        }
        return $retorno;
    }

    public function gerarResumo() {

        if ($this->parametrosFaturamento->servicosFaturados != 'L') {
            return false;
        }

        try {

            $this->dao->begin();

            $arquivo = fopen("faturamento/resumo_faturamento_vivo", "a");

            $idProcesso = $this->dao->buscarIdProcessoBancoDados();

            if ($idProcesso > 0) {
                file_put_contents('faturamento/pidProcessoVivoBD.txt', $idProcesso);
            }

            $this->dao->deletarPrevisoes($this->parametrosFaturamento);

            // Inserir valores na previsão
            $this->dao->inserirPrevisaoProRataLocacoesEquipamentos($this->parametrosFaturamento);

            fputs($arquivo, "inserirPrevisaoProRataLocacoesEquipamentos finalizada - " . microtime(true) . "\r\n");
            
            $this->dao->inserirPrevisaoLocacoesEquipamentos($this->parametrosFaturamento);

            fputs($arquivo, "inserirPrevisaoLocacoesEquipamentos finalizada - " . microtime(true) . "\r\n");
            
            $this->dao->inserirPrevisaoProRataLocacoesAcessorios($this->parametrosFaturamento);

            fputs($arquivo, "inserirPrevisaoProRataLocacoesAcessorios finalizada - " . microtime(true) . "\r\n");

            $this->dao->inserirPrevisaoLocacoesAcessorios($this->parametrosFaturamento);

            fputs($arquivo, "inserirPrevisaoLocacoesAcessorios finalizada - " . microtime(true) . "\r\n");

            $this->dao->finalizarProcesso('S');

            fputs($arquivo, "gerarResumo finalizada - " . microtime(true) . "\r\n");

            fclose($arquivo);

            $this->dao->commit();
        } catch (Exception $e) {

            $this->dao->rollback();

            $this->dao->finalizarProcesso('F');

            file_put_contents('faturamento/resumo_faturamento_vivo', $e->getMessage());
        }
    }

    public function limparResumo() {

        try {

            $this->dao->begin();

            $this->dao->deletarPrevisoes();

            $this->dao->commit();

            $this->view->mensagemSucesso = "Resumo limpo com sucesso.";

            unset($_POST);
        } catch (ErrorException $e) {

            $this->dao->rollback();
        }

        $this->index();
    }

    public function pararResumo() {
        try {
            $this->dao->begin();

            $this->dao->pararResumo();

            $this->dao->commit();

            $this->view->mensagemSucesso = "Geração de resumo parada com sucesso.";
        } catch (ErrorException $e) {
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }
        $this->index();
    }

    private function consultarResumo() {
        try {

            $this->validarCampos($this->view->parametros);

            if (trim($this->view->parametros->tipoPessoa) == 'F') {
                $this->view->parametros->documentoCliente = $this->apenasNumeros($this->view->parametros->cpf);
                $this->view->parametros->nome = $this->view->parametros->nomeCliente;
            } else {
                $this->view->parametros->documentoCliente = $this->apenasNumeros($this->view->parametros->cnpj);
                $this->view->parametros->nome = $this->view->parametros->razaoSocial;
            }

            $this->view->dados = $this->dao->consultarResumo($this->view->parametros);
            return true;
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
        }
        return false;
    }

    public function prepararResumo() {
        try {
            $this->dao->begin();

            $this->view->parametros = $this->tratarParametros();

            $this->validarCampos($this->view->parametros);

            if (trim($this->view->parametros->tipoPessoa) == 'F') {
                $this->view->parametros->documentoCliente = $this->apenasNumeros($this->view->parametros->cpf);
                $this->view->parametros->nome = $this->view->parametros->nomeCliente;
            } else {
                $this->view->parametros->documentoCliente = $this->apenasNumeros($this->view->parametros->cnpj);
                $this->view->parametros->nome = $this->view->parametros->razaoSocial;
            }

            $this->view->parametros->idUsuario = $_SESSION['usuario']['oid'];

            $this->view->parametros->obrigacoesFinanceiras = 0;

            $this->dao->inserirDadosExecucao('R', $this->view->parametros);

            $this->dao->commit();

            if (!is_dir(_SITEDIR_ . "faturamento")) {
                if (!mkdir(_SITEDIR_ . "faturamento", 0777)) {
                    throw new Exception('Falha ao criar arquivo de log.');
                }
            }

            chmod(_SITEDIR_ . "faturamento", 0777);

            $arquivo = fopen(_SITEDIR_ . "faturamento/resumo_faturamento_vivo", "w");
            if ($arquivo) {

                fputs($arquivo, "Resumo Iniciado\r\n");
                fclose($arquivo);
                chmod(_SITEDIR_ . "faturamento/resumo_faturamento_vivo", 0777);               

                $httpHost = $_SERVER['HTTP_HOST'];
                // Windows
                if( $httpHost == "10.1.4.242"){ 
                	
                	exec('"C:/Program Files (x86)/Zend/ZendServer/bin/php.exe" C:/var/www/html/sistemaWeb/CronProcess/gerar_previsao_faturamento_vivo.php >> C:/var/www/html/sistemaWeb/faturamento/resumo_faturamento_vivo 2>&1 &');
                
                } 
                // Linux
                else {
                
                	passthru("/usr/bin/php " . _SITEDIR_ . "CronProcess/gerar_previsao_faturamento_vivo.php >> " . _SITEDIR_ . "faturamento/resumo_faturamento_vivo 2>&1 &");
                	
                }
                
            } else {
                throw new Exception('Falha ao criar arquivo de log.');
            }

            unset($_POST);
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();

            $this->dao->rollback();
        } catch (ErrorException $e) {
            $this->dao->finalizarProcesso('F');

            $this->dao->rollback();
        }

        $this->index();
    }

    public function prepararFaturamento() {
        try {
            $this->dao->begin();

            $this->view->parametros = $this->tratarParametros();

            if (!isset($this->view->parametros->obrigacoesFaturar)) {
                throw new Exception("É obrigatório selecionar pelo menos uma obrigação financeira.");
            }

            $dados = $this->tratarParametrosConsulta($this->view->parametros->parametrosConsulta, $this->view->parametros->obrigacoesFaturar);

            $this->dao->inserirDadosExecucao('F', $dados);

            $this->dao->commit();

            if (!is_dir(_SITEDIR_ . "faturamento")) {
                if (!mkdir(_SITEDIR_ . "faturamento", 0777)) {
                    throw new Exception('Falha ao criar arquivo de log.');
                }
            }

            chmod(_SITEDIR_ . "faturamento", 0777);

            $arquivo = fopen(_SITEDIR_ . "faturamento/geracao_faturamento_vivo", "w");
            if ($arquivo) {

                fputs($arquivo, "Faturamento Iniciado\r\n");
                fclose($arquivo);
                chmod(_SITEDIR_ . "faturamento/geracao_faturamento_vivo", 0777);                

                $httpHost = $_SERVER['HTTP_HOST'];
                // Windows
                if( $httpHost == "10.1.4.242"){ 
                	
                	exec('"C:/Program Files (x86)/Zend/ZendServer/bin/php.exe" C:/var/www/html/sistemaWeb/CronProcess/gerar_faturamento_unificado_vivo.php >> C:/var/www/html/sistemaWeb/faturamento/geracao_faturamento_vivo 2>&1 &');
                
                } 
                // Linux
                else {
                
                	passthru("/usr/bin/php " . _SITEDIR_ . "CronProcess/gerar_faturamento_unificado_vivo.php >> " . _SITEDIR_ . "faturamento/geracao_faturamento_vivo 2>&1 &");
                	
                }
                
            } else {
                throw new Exception('Falha ao criar arquivo de log.');
            }

            unset($_POST);
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();

            $this->dao->rollback();
            $_POST['acao'] = "consultarResumo";
        } catch (ErrorException $e) {
            $this->dao->finalizarProcesso('F');

            $this->dao->rollback();
            $_POST['acao'] = "consultarResumo";
        }

        $this->index();
    }

    public function gerarFaturamento() {

        if ($this->parametrosFaturamento->servicosFaturados != 'L') {
            return false;
        }

        try {
            $this->dao->begin();

            $this->dao->faturar($this->parametrosFaturamento); 

            $this->dao->finalizarProcesso('S');

            $this->dao->commit();
        } catch (Exception $e) {

            $this->dao->rollback();

            $this->dao->finalizarProcesso('F');
        }
    }

    public function gerarPlanilha() {
        try {

            $this->view->parametros = $this->tratarParametros();

            if (!isset($this->view->parametros->obrigacoesFaturar)) {
                throw new Exception("É obrigatório selecionar pelo menos uma obrigação financeira.");
            }

            $listaPendentes = $this->dao->gerarPlanilhaCsv(implode(',', $this->view->parametros->obrigacoesFaturar));

            $this->view->nomeArquivo = '/var/www/docs_temporario/rel_pendencias_faturamento_' . date('dmYHis') . '.csv';
            $this->abrirArquivo($this->view->nomeArquivo);

            $cabecalho = array(
                "Nome do Cliente",
                "Número do Contrato",
                "Tipo do Contrato",
                "Data de Início da Vigência",
            	"Ciclo Faturamento",
                "Situação do Contrato",
                "Placa do Veículo",
                "Equipamento",
                "Subscription Id",
                "Conta do Cliente",
                "Obrigação Financeira",
                "Valor");

            $titulo = array_fill(0, count($cabecalho), '');
            $titulo[0] = "Relatório de Pré-faturamento";

            $this->gravarDados($titulo);
            $this->gravarDados(array_fill(0, count($cabecalho), ''));
            $this->gravarDados($cabecalho);

            $totalGeral = 0;
            for ($i = 0; $i < count($listaPendentes); $i++) {
                $totalGeral += $listaPendentes[$i]->valor_total;
                $listaPendentes[$i]->valor_total = number_format($listaPendentes[$i]->valor_total, 2, ',', '.');
                $this->gravarDados((array) $listaPendentes[$i]);

                // Salva no arquivo a cada 1000 registros
                if ($i % 1000 === 0) {
                    $this->salvarDadosBuffer();
                }
            }

            $rodape = array_fill(0, count($cabecalho), '');
            $rodape[count($cabecalho) - 2] = "TOTAL GERAL:";
            $rodape[count($cabecalho) - 1] = number_format($totalGeral, 2, ',', '.');

            $this->gravarDados($rodape);

            $this->fecharArquivo();

            $this->view->mostrarArquivo = true;
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();

            $this->view->mostrarArquivo = false;
        }

        $_POST['acao'] = "consultarResumo";

        $this->index();
    }

    public function gerarRelatorio() {
        try {

            $this->view->parametros = $this->tratarParametros();

            if (!isset($this->view->parametros->obrigacoesFaturar)) {
                throw new Exception("É obrigatório selecionar pelo menos uma obrigação financeira.");
            }

            $dados = $this->tratarParametrosConsulta($this->view->parametros->parametrosConsulta, $this->view->parametros->obrigacoesFaturar);

            $this->view->dadosPreFaturamento = $this->dao->gerarRelatorioPreFaturamento($dados);

            if (count($this->view->dadosPreFaturamento) > 2000) {
                throw new Exception('O limite máximo de linhas para apresentar em tela é de 2.000.');
            }

            $this->view->mostrarRelatorioPreFaturamento = true;
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();

            $this->view->mostrarRelatorioPreFaturamento = false;
        }

        $_POST['acao'] = "consultarResumo";

        $this->index();
    }

    private function tratarParametrosConsulta($parametrosConsulta, $obrigacoesFaturar) {

        $parametrosConsulta = explode('|', $parametrosConsulta);
        $parametrosConsulta[8] = implode(',', $obrigacoesFaturar);

        $dados = new stdClass();

        $dados->dataReferencia = $parametrosConsulta[0];
        $dados->tipoContrato = $parametrosConsulta[1];
        $dados->servicosFaturados = $parametrosConsulta[2];
        $dados->tipoPessoa = $parametrosConsulta[3];
        $dados->nome = $parametrosConsulta[4];
        $dados->documentoCliente = $parametrosConsulta[5];
        $dados->placa = $parametrosConsulta[6];
        $dados->idUsuario = $parametrosConsulta[7];
        $dados->obrigacoesFinanceiras = $parametrosConsulta[8];

        return $dados;
    }

    public function verificarProcesso($finalizado) {

        try {

            // Verifica concorrência entre processos
            $parametros = $this->dao->recuperarParametros($finalizado);

            if ($parametros->efvtipo_processo == 'F') {
                $msg = "Faturamento iniciado por " .
                        $parametros->nm_usuario . " às " . $parametros->inicio . " &nbsp;&nbsp;-&nbsp;&nbsp;  " .
                        number_format($parametros->efvporcentagem, 1, ',', '.') . " % concluído.";
            } else {
                $msg = "Resumo iniciado por " . $parametros->nm_usuario . " às " . $parametros->inicio;
            }

            return array(
                "codigo" => 2,
                "msg" => $msg,
                "parametros" => $parametros
            );
        } catch (Exception $e) {
            return array(
                "codigo" => 0,
                "msg" => ''
            );
        } catch (ErrorException $e) {
            return array(
                "codigo" => 1,
                "msg" => "Falha ao verificar concorrência. Tente novamente."
            );
        }
    }

    /**
     * Validar os campos obrigatórios do cadastro.
     *
     * @param stdClass $dados Dados a serem validados
     *
     * @throws Exception
     *
     * @return void
     */
    private function validarCampos(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;
		
        /* 
        // Retirada restrição de faturamento de meses anteriores - 25/07/2014 - Danielle Leite
        $dataMenorAtual = false;
		 */
        
        $tipoContratoInvalido = false;

        /**
         * Verifica os campos obrigatórios
         */
        list($mes, $ano) = explode('/', $dados->dataReferencia);

        if (!isset($dados->dataReferencia) || trim($dados->dataReferencia) == '') {
            $camposDestaques[] = array(
                'campo' => 'dataReferencia'
            );
            $error = true;
        }
        
        /*  
        // Retirada restrição de faturamento de meses anteriores - 25/07/2014 - Danielle Leite
        else if (date("Ym", strtotime(implode('-', array($ano, $mes)))) < date('Ym', time())) {
            $camposDestaques[] = array(
                'campo' => 'dataReferencia'
            );
            $dataMenorAtual = true;
        } 
         */

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        /* 
        // Retirada restrição de faturamento de meses anteriores - 25/07/2014 - Danielle Leite
        if ($dataMenorAtual) {
            $this->view->dados = $camposDestaques;
            throw new Exception('Mês e Ano inferior ao Mês e Ano Atual, não pode ser faturado.');
        }
         */

        if ($this->view->parametros->servicosFaturados == 'L' && $this->view->parametros->tipoContrato != 'L') {
            $camposDestaques[] = array(
                'campo' => 'tipoContrato'
            );
            $camposDestaques[] = array(
                'campo' => 'servicosFaturados'
            );
            $tipoContratoInvalido = true;
        }

        if ($tipoContratoInvalido) {
            $this->view->dados = $camposDestaques;
            throw new Exception('Para Serviço Faturado de Locação, Tipo de Contrato deve ser Locação.');
        }
    }

    /**
     * Buscar cliente por nome sendo ele PJ || PF
     *
     * @return array $retorno
     */
    public function buscarClienteNome() {

        $parametros = $this->tratarParametros();

        $parametros->tipo = trim($parametros->filtro) != '' ? trim($parametros->filtro) : '';
        $parametros->nome = trim($parametros->term) != '' ? trim($parametros->term) : '';

        $retorno = $this->dao->buscarClienteNome($parametros);

        echo json_encode($retorno);
        exit;
    }

    public function setarParametrosProcesso() {

        $parametros = $this->dao->recuperarParametros(false);

        $parametrosFaturamento = explode("|", $parametros->efvparametros);

        $this->parametrosFaturamento->dataReferencia = $parametrosFaturamento[0];
        $this->parametrosFaturamento->tipoContrato = $parametrosFaturamento[1];
        $this->parametrosFaturamento->servicosFaturados = $parametrosFaturamento[2];
        $this->parametrosFaturamento->tipoPessoa = $parametrosFaturamento[3];
        $this->parametrosFaturamento->nome = $parametrosFaturamento[4];
        $this->parametrosFaturamento->documentoCliente = $parametrosFaturamento[5];
        $this->parametrosFaturamento->placa = $parametrosFaturamento[6];
        $this->parametrosFaturamento->idUsuario = $parametrosFaturamento[7];
        $this->parametrosFaturamento->obrigacoesFinanceiras = $parametrosFaturamento[8];
    }

    private function apenasNumeros($num) {
        return preg_replace('/[^0-9]/', '', $num);
    }

    /**
     * Abre o arquivo
     */
    private function abrirArquivo($nomeArquivo) {
        $this->arquivo = fopen($nomeArquivo, "w");
    }

    private function fecharArquivo() {
        fclose($this->arquivo);
    }

    /**
     * Recebe um array e grava no arquivo no formato CSV separado por ';'
     *
     * @param array $dados
     */
    private function gravarDados($dados) {
        $dadosCsv = implode($dados, ';');
        $dadosCsv .= "\n";
        fwrite($this->arquivo, $dadosCsv);
    }

    /**
     * Grava os dados que estão no buffer de saída no arquivo, liberando a memória
     */
    private function salvarDadosBuffer() {
        fflush($this->arquivo);
    }

}