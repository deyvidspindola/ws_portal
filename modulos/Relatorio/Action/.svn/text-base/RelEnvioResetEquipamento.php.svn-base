<?php
require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';

/**
 * @author Thomas de Lima <thomas.lima.ext@sascar.com.br>
 **/
class RelEnvioResetEquipamento
{
    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS   = "Existem campos obrigat&oacute;rios n&atilde;o preenchidos ou inv&aacute;lidos.";
    const MENSAGEM_ALERTA_PERIODO_INVALIDO      = "Atenção: A data de início do período não pode ser maior que a data final do período.";
    const MENSAGEM_ALERTA_PERIODO               = "Atenção: O período não pode ser superior a 30 dias.";
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
        require_once _MODULEDIR_ . "Relatorio/View/rel_envio_reset_equipamento/index.php";
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
        $intervalo = new DateInterval('P30D');
        
        // Verificando se o filtro período foi setado
        $this->view->parametros->data_inicial = (empty($this->view->parametros->data_inicial) ? $data->sub($intervalo)->format("d/m/Y") : $this->view->parametros->data_inicial);
        $this->view->parametros->data_final   = (empty($this->view->parametros->data_final) ? $data->add($intervalo)->format("d/m/Y") : $this->view->parametros->data_final);
        $this->view->comboEquipamentosProjeto = $this->dao->recuperarEquipamentosProjeto();

    }


    /**
     * Responsavel por tratar e retornar o resultado das pesquisas
     * @author Thomas de Lima
     */
    public function pesquisar() {

        $this->view->statusPesquisa = FALSE;
        $this->view->dados = array();

        //tratamento para contornar problema com o componente de paginacao
        if(!empty($_POST)) {
            unset($_GET);
        }

        try {

            $this->view->parametros = $this->tratarParametros();
            $this->inicializarParametros();
            $this->pesquisarResultado($this->view->parametros);
            $this->view->statusPesquisa = TRUE;

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
        }

        require_once _MODULEDIR_ . "Relatorio/View/rel_envio_reset_equipamento/index.php";

    }


    /**
     * Pesquisa por linhas para reativação
     */
    private function pesquisarResultado($filtros) {

        $paginacao = new PaginacaoComponente();
        $this->validarCamposObrigatorios($filtros);
        $resultadoPesquisa = $this->dao->pesquisarResultado($filtros);
        $this->view->totalResultados = $resultadoPesquisa[0]->total_registros;

        //Valida se houve resultado na pesquisa
        if ($this->view->totalResultados == 0) {
            throw new Exception(utf8_decode(self::MENSAGEM_NENHUM_REGISTRO));
        }

        // Campos para opção de ordenação
         $campos = array(
                '' => 'Escolha',
                'enrdt_envio' => 'Data de envio do comando',
                'veiplaca' => 'Placa',
                'clinome' => 'Cliente',
                'eprnome' => 'Equipamento'
                );

         if ($paginacao->setarCampos($campos)) {
            $this->view->ordenacao = $paginacao->gerarOrdenacao('enrdt_envio');
            $this->view->paginacao = $paginacao->gerarPaginacao($this->view->totalResultados);
        }

        $this->view->dados = $this->dao->pesquisarResultado($filtros, $paginacao->buscarPaginacao(), $paginacao->buscarOrdenacao());

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
        if ( ($dados->clinome_pesq == '') && 
             ($dados->eproid == '') && 
             ($dados->placa == '') && 
             (($dados->data_inicial == '') || ($dados->data_final == '')) ) {

            $camposDestaques[] = array('campo' => 'data_inicial');
            $camposDestaques[] = array('campo' => 'data_final');

        } else if(($dados->data_inicial != '') && ($dados->data_final != '')){

            if(!$this->validarDatas($dados->data_inicial, $dados->data_final)){
                $camposDestaques[] = array('campo' => 'data_inicial');
                $camposDestaques[] = array('campo' => 'data_final');
                $this->view->campos = $camposDestaques;
                throw new Exception(utf8_decode(self::MENSAGEM_ALERTA_PERIODO_INVALIDO));
            }

            if(!$this->validarPeriodo($dados->data_inicial, $dados->data_final)){
                $camposDestaques[] = array('campo' => 'data_inicial');
                $camposDestaques[] = array('campo' => 'data_final');
                $this->view->campos = $camposDestaques;
                throw new Exception(utf8_decode(self::MENSAGEM_ALERTA_PERIODO));
            }

            
        }

        if (!empty($camposDestaques)) {
            $this->view->campos = $camposDestaques;
            throw new Exception(utf8_decode(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS));
        }
    }

    /**
    * Validar as datas digitadas
    * @author Thomas Lima
    * @param string $dataInicial
    * @param string $dataFinal
    * @return boolean
    */
    private function validarDatas($dataInicial, $dataFinal)
    {

        $dataInicial = implode('-', array_reverse(explode('/', substr($dataInicial, 0, 10)))).substr($dataInicial, 10);
        $dataFinal = implode('-', array_reverse(explode('/', substr($dataFinal, 0, 10)))).substr($dataFinal, 10);

        if ($dataInicial > $dataFinal){
            return false;
        }

        return true;
    }

    
    /**
    * Validar o periodo entre datas
    * @author Thomas Lima
    * @param string $dataInicial
    * @param string $dataFinal
    * @return boolean
    */
    private function validarPeriodo($dataInicial, $dataFinal)
    {

        $dataInicial = implode('-', array_reverse(explode('/', substr($dataInicial, 0, 10)))).substr($dataInicial, 10);
        $dataFinal = implode('-', array_reverse(explode('/', substr($dataFinal, 0, 10)))).substr($dataFinal, 10);

        if ($dataInicial > $dataFinal){
            return false;
        }

        $dataInicial = new DateTime($dataInicial);
        $dataFinal = new DateTime($dataFinal);

        $intervalo = $dataInicial->diff($dataFinal);
        $diferenca = $intervalo->days;

        if($diferenca > 30) {
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