<?php
require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';

/**
 * @author Thomas de Lima <thomas.lima.ext@sascar.com.br>
 **/
class RelLinhasReativacao
{

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS   = "Existem campos obrigatórios não preenchidos ou inválidos.";
    const MENSAGEM_ALERTA_PERIODO_DATA          = "Data inicial não pode ser maior que a data final.";
    const MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO   = "Houve um erro no processamento do arquivo.";
    const MENSAGEM_NENHUM_REGISTRO              = "Nenhum registro encontrado.";

    /**
     * Metodo construtor.
     */
    public function __construct($dao = null)
    {

        //Verifica o se a variavel e um objeto e a instancia na atributo local
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

        //Status de uma transacao
        $this->view->statusPesquisa = false;

         // Ordenção e paginação
        $this->view->ordenacao = null;
        $this->view->paginacao = null;
        $this->view->totalResultados = 0;

        //Usuario logado
        $this->usuoid = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

    }


    /**
     * Metodo padrao da classe.
     * Reponsavel tambem por realizar a pesquisa invocando o metodo privado
     */
    public function index($parametros = null)
    {
         try {

            if(is_null($parametros)){

                $this->view->parametros = $this->tratarParametros();

            } else {
                $this->view->parametros = $parametros;
            }
            //Inicializa os dados
            $this->inicializarParametros();

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }
        require_once _MODULEDIR_ . "Relatorio/View/rel_linhas_reativacao/index.php";
    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     */
    private function tratarParametros()
    {

       $retorno = new stdClass();

       if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {
                //Verifica se atributo ja existe e nao sobrescreve.
                if (!isset($retorno->$key)) {
                     $retorno->$key = isset($_GET[$key]) ? trim($value) : '';
                }
            }
        }

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {

                if(is_array($value)) {
                    //Tratamento de POST com Arrays
                    foreach ($value as $chave => $valor) {
                        $value[$chave] = trim($valor);
                    }
                    $retorno->$key = isset($_POST[$key]) ? $_POST[$key] : array();

                } else {
                    $retorno->$key = isset($_POST[$key]) ? trim($value) : '';
                }
            }
        }
        
        if (count($_FILES) > 0) {
           foreach ($_FILES as $key => $value) {
               //Verifica se atributo já existe e não sobrescreve.
               if (!isset($retorno->$key)) {
                    $retorno->$key = isset($_FILES[$key]) ? $value : '';
               }
           }
        }
        return $retorno;
    }

    public function inicializarParametros()
    {
        $data = new DateTime();
        $intervalo = new DateInterval('P1D');
        
        // Verificando se o filtro período foi setado
        $this->view->parametros->data_inicial = (empty($this->view->parametros->data_inicial) ? $data->sub($intervalo)->format("d/m/Y") : $this->view->parametros->data_inicial);
        $this->view->parametros->data_final   = (empty($this->view->parametros->data_final) ? $data->add($intervalo)->format("d/m/Y") : $this->view->parametros->data_final);
        // Recuperando os status da linha q será utilizado
        $this->view->comboStatusLinha = $this->dao->recuperarStatusLinha();
        // Recuperando os status do contrato que será utilizado
        $this->view->comboStatusContrato = $this->dao->recuperarStatusContrato();
    }


    /**
     * Responsavel por tratar e retornar o resultado das pesquisas
     * @author Thomas de Lima
     */
    public function pesquisar() {

        $this->view->statusPesquisa = FALSE;
        $this->view->dados = array();
        $this->view->arquivoCSV = array();

        //tratamento para contornar problema com o componente de paginacao
        if(!empty($_POST)) {
            unset($_GET);
        }

        try {

            $this->view->parametros = $this->tratarParametros();
            $this->inicializarParametros();
            $this->pesquisarLinhasReativacao($this->view->parametros);
            $this->view->statusPesquisa = TRUE;

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();

        }

        require_once _MODULEDIR_ . "Relatorio/View/rel_linhas_reativacao/index.php";

    }


    /**
     * Pesquisa por linhas para reativação
     */
    private function pesquisarLinhasReativacao($filtros) {

        $paginacao = new PaginacaoComponente();
        $this->validarCamposObrigatorios($filtros);
        $resultadoPesquisa = $this->dao->pesquisarLinhasReativacao($filtros);
        $this->view->totalResultados = $resultadoPesquisa[0]->total_registros;

        //Valida se houve resultado na pesquisa
        if ($this->view->totalResultados == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        // T = TELA
        if($filtros->tipo_resultado != 'A') {

            // Campos para opção de ordenação
             $campos = array(
                    '' => 'Escolha',
                    'linreadt_cadastro' => 'Data',
                    'cslstatus' => 'Status da linha',
                    'csidescricao' => 'Status do contrato',
                    'linreaclioid' => 'Cliente'
                    );

             if ($paginacao->setarCampos($campos)) {
                $this->view->ordenacao = $paginacao->gerarOrdenacao('linreadt_cadastro');
                $this->view->paginacao = $paginacao->gerarPaginacao($this->view->totalResultados);
            }

            $this->view->dados = $this->dao->pesquisarLinhasReativacao($filtros, $paginacao->buscarPaginacao(), $paginacao->buscarOrdenacao());

        } 
        // A = GERAR ARQUIVO CSV
        else {
            $resultadoPesquisa = $this->dao->pesquisarLinhasReativacaoCsv($filtros);

            $this->view->arquivoCSV = $this->gerarArquivoCSV($resultadoPesquisa);

        }

    }



    /**
    * Gera um arquivo CSV
    */
    private function gerarArquivoCSV($dados) {

        require_once "lib/Components/CsvWriter.php";
        $arquivo = "linhas_reativacao_".date('Ymmhis').".csv";
        $diretorio = '/var/www/docs_temporario/';

        try {
            if (is_dir($diretorio) && is_writable($diretorio)) {

                // Gerar o arquivo CSV
                $csvWriter = new CsvWriter( $diretorio.$arquivo, ';', '', true);

                //Cabecalho
                $csvWriter->addLine(array(
                    'ID', 
                    'Data',
                    'Cliente',
                    'DDD', 
                    'Linha',
                    'Status da Linha',
                    'Status do contrato',
                    'Tipo do contrato',
                    'Placa',
                    'Contrato',
                    'Usuário'
                ));

                //Gravar as linhas
                foreach($dados as $dado) {

                    $linha[0] = $dado->linreaoid;
                    $linha[1] = $dado->data_cadastro;
                    $linha[2] = $dado->cliente;
                    $linha[3] = $dado->ddd;
                    $linha[4] = $dado->linha;
                    $linha[5] = $dado->statuslinha;
                    $linha[6] = $dado->statuscontrato;
                    $linha[7] = $dado->tipocontrato;
                    $linha[8] = $dado->placa;
                    $linha[9] = $dado->contrato;
                    $linha[10] = $dado->usuario;

                    $csvWriter->addLine($linha);
                }

                //Verifica se o arquivo foi gerado
                $arquivoGerado = file_exists( $diretorio.$arquivo);
                if ($arquivoGerado === false) {
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                }

                return array('caminho' => $diretorio . $arquivo , 'arquivo' => $arquivo);
            }
            else {
                throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
            }

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }

    }



    /**
     * Validar os campos obrigatórios do cadastro.
     */
    private function validarCamposObrigatorios(stdClass $dados)
    {
        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        /**
         * Verifica os campos obrigatórios
         */
        if ( ($dados->clinome_pesq == '') && ($dados->connumero == '') && ($dados->linnumero == '') && ($dados->csloid == '')
            && ($dados->csioid == '') && ($dados->placa == '') && (($dados->data_inicial == '') || ($dados->data_final == '')) ) {

            $camposDestaques[] = array('campo' => 'data_inicial');
            $camposDestaques[] = array('campo' => 'data_final');

        } else if(($dados->data_inicial != '') && ($dados->data_final != '')){

            if(!$this->validarPeriodo($dados->data_inicial, $dados->data_final)){
                $camposDestaques[] = array('campo' => 'data_inicial');
                $camposDestaques[] = array('campo' => 'data_final');
            }

            if ($error) {
                $this->view->campos = $camposDestaques;
                throw new Exception(self::MENSAGEM_ALERTA_PERIODO_DATA);
            }
        }

        if (!empty($camposDestaques)) {
            $this->view->campos = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
    * Validar o periodo entre datas
    * @author Andre L. Zilz
    * @param string $dataInicial
    * @param string $dataFinal
    * @return boolean
    */
    private function validarPeriodo($dataInicial, $dataFinal){

        $dataInicial = implode('-', array_reverse(explode('/', substr($dataInicial, 0, 10)))).substr($dataInicial, 10);
        $dataFinal = implode('-', array_reverse(explode('/', substr($dataFinal, 0, 10)))).substr($dataFinal, 10);

        if($dataInicial > $dataFinal) {
            return false;
        }

        return true;
    }

    /**
    * Destroi o objeto de sessao com os dados de paginacao
    */
    public function destruirSessaoPaginacao()
    {

        if(isset($_SESSION['paginacao'])) {
            unset($_SESSION['paginacao']);
        }
    }

    /**
    * Buscar dados clientes - AJAX
    */
    public function recuperarCliente()
    {

        $parametros = $this->tratarParametros();

        if(!empty($parametros->tipo_pessoa)){
            $parametros->texto = preg_replace('/\D/', '', $parametros->term);
        } else {
            $parametros->texto = $this->tratarTextoInput($parametros->term, true);
        }

        $retorno = $this->dao->recuperarCliente($parametros);

        echo json_encode($retorno);
        exit;
    }
    /**
    * Tratamento de input de dados, contra injection code e acentos
    **/
    private function tratarTextoInput($dado, $autocomplete = false)
    {

        //Elimina acentos para pesquisa
        if($autocomplete){
            $dado = utf8_decode($dado);
        }
        $dado  = trim($dado);
        $dado  = str_replace("'", '', $dado);
        $dado  = str_replace('\\', '', $dado);
        $dado  = strip_tags($dado);

        return $dado;
    }

}