<?php

/**
 * Classe RelEquipInstaladoSemSubscription.
 * Camada de regra de negócio.
 *
 * @package  Relatorio
 * @author   André Luiz Zilz <andre.zilz@meta.com.br>
 *
 */
class RelEquipInstaladoSemSubscription {

    private $dao;
    private $view;

    const MENSAGEM_NENHUM_REGISTRO_COM_DATA = "Não existem equipamentos instalados sem subscprition Id para o período selecionado.";
    const MENSAGEM_NENHUM_REGISTRO_SEM_DATA = "Não existem equipamentos instalados sem subscprition Id.";
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";


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

        //Status de uma transação
        $this->view->status = false;

        //Nome do arquivo CSV gerado
        $this->view->xls = '';
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

            if (isset($_SESSION['dados_csv'])) {
                unset($_SESSION['dados_csv']);
            }

            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
                $this->view->dados = $this->pesquisar($this->view->parametros);

                if (isset($_SESSION)) {
                    $_SESSION['dados_csv'] = $this->view->dados;
                }

            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        //Inclir a view padrão
        require_once _MODULEDIR_ . "Relatorio/View/rel_equip_instalado_sem_subscription/index.php";
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

    /**
     * Popula os arrays para os combos de estados e cidades
     *
     * @return void
     */
    private function inicializarParametros() {

        //Verifica se os parametro existem, senão iniciliza todos
		$this->view->parametros->dataInicial = isset($this->view->parametros->dataInicial) ? $this->view->parametros->dataInicial : "" ;
        $this->view->parametros->dataFinal = isset($this->view->parametros->dataFinal) ? $this->view->parametros->dataFinal : "" ;

    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     *
     * @param stdClass $filtros Filtros da pesquisa
     *
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        $resultadoPesquisa = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {

            if ((!empty($filtros->dataInicial)) || (!empty($filtros->dataFinal))) {
                throw new Exception(self::MENSAGEM_NENHUM_REGISTRO_COM_DATA);
            } else {
                throw new Exception(self::MENSAGEM_NENHUM_REGISTRO_SEM_DATA);
            }
        }

        $this->view->status = TRUE;

        return $resultadoPesquisa;
    }

    /**
     * Cria um arquivo CSV com o resultado da pesqusia
     *
     * @throws Exception
     */
    public function gerarArquivoCSV() {

        $this->view->parametros = $this->tratarParametros();
        $this->inicializarParametros();

        require_once "lib/Components/CsvWriter.php";
        $arquivo = "equipamentosinstaladossemsubscriptionidVIVO_".date('Ymd').".csv";
		$diretorio = '/var/www/docs_temporario/';

        if ( (isset($_SESSION['dados_csv'])) && (count($_SESSION['dados_csv']) > 0) ) {

            $dados = $_SESSION['dados_csv'];
            try {
                if (is_dir($diretorio) && is_writable($diretorio)) {

                    // Gera CSV
                    $csvWriter = new CsvWriter($diretorio.$arquivo, ';', '', true);

                    //Cabeçalho
                    $csvWriter->addLine(array(
                        'Data Instalação',
                        'Núm. Contrato',
                        'CPF/CNPJ',
                        'Nome do Cliente',
                        'Placa',
                        'Núm. Série Equipamento'
                    ));

                    $dadosLinha = array();

                    //Dados
                    foreach ($dados as $linha) {
                        foreach ($linha as $chave => $dado) {

                            //Reordenar conforme as colunas
                            switch ($chave) {
                                case 'data_instalacao':
                                    $dadosLinha[0] = $dado;
                                    break;
                                case 'contrato':
                                    $dadosLinha[1] = $dado;
                                    break;
                                case 'cpf_cnpj':
                                    $dadosLinha[2] = $dado;
                                    break;
                                case 'clinome':
                                    $dadosLinha[3] = $dado;
                                    break;
                                case 'placa':
                                    $dadosLinha[4] = $dado;
                                    break;
                                case 'serie':
                                    $dadosLinha[5] = $dado;
                                    break;
                            }
                        }

                        ksort($dadosLinha);
                        $csvWriter->addLine($dadosLinha);
                    }

                    //Verifica se o arquivo foi gerado
                    $arquivoGerado = file_exists( $diretorio.$arquivo);
                    if ($arquivoGerado === false) {
                        echo '';
                    } else {
                        echo $diretorio.$arquivo;
                    }

                } else {
                     throw new ErrorException("Erro ao gravar o arquivo" . " " . $diretorio);
                }

            } catch (Exception $e){
                throw new Exception($e->getMessage());
            }
        }
    }

}

