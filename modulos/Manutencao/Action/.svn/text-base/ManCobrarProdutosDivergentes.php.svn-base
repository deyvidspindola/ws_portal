<?php

/**
 * @author   André Zilz <andre.zilz@sascar.com.br>
 *
 */
class ManCobrarProdutosDivergentes {

    //Objeto referente a  classe de persitencia de dados
    private $dao;

    //Objetos para exibicao e dados em telas
    private $view;

    //Usuario logado
    private $usuoid;

    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";
    const MENSAGEM_SUCESSO_COBRANCA           = "Cobrança realizada com sucesso.";

    /**
     * Construtor da Classe
     * @author   André Zilz <andre.zilz@sascar.com.br>
     * @param ManCobrarProdutosDivergentesDAO $dao
     */
     public function __construct($dao = NULL) {

        //Verifica o se a variavel e um objeto e a instancia na atributo local
        if (is_object($dao)) {
            $this->dao = $dao;
        }

        //Cria objeto da view
        $this->view = new stdClass();

        //Mensagens Tela
        $this->view->mensagemErro = '';
        $this->view->mensagemAlerta = '';
        $this->view->mensagemSucesso = '';

        //Dados para view
        $this->view->dados = array();

        //Filtros/parametros utlizados na view
        $this->view->parametros = null;

        //Usuario logado
        $this->usuoid = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
    }


    /**
     * Metodo padrao da classe.
     * @author   André Zilz <andre.zilz@sascar.com.br>
     * @return void
     */
    public function index() {

        try {

            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            if(! empty($this->view->parametros->invoid) ) {
                 $this->view->dados = $this->dao->pesquisarEstoqueDivergente($this->view->parametros->invoid);
                 $this->view->dados = $this->tratarDados($this->view->dados);
            }

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
        }

        require_once _MODULEDIR_ . "Manutencao/View/man_cobranca_produtos_divergentes/index.php";
    }


    /**
     * Tratamento de parametros inputados em tela ou URL
     * @author André Zilz <andre.zilz@sascar.com.br>
     * @return stdClass
     */
    private function tratarParametros() {

        $retorno = new stdClass();

       if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {

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

        return $retorno;
    }

     /**
     * Tratamento dos dados enviados e consumidos pelas Views
     * @author   André Zilz <andre.zilz@sascar.com.br>
     * @return void
     */
    private function inicializarParametros() {

        //Verifica se os parametro existem, senao inicializa todos
        $this->view->parametros->invoid = isset($this->view->parametros->invoid) ? $this->view->parametros->invoid : '0' ;

    }

    /**
     * Funcao que processa a persistencia da cobranca no DB. Cahamada por Ajax
     * @return string
     */
    public function inserirDesconto() {


        $dados = array();

        $parametros = $this->tratarParametros();

        try {

            $dados = $this->dao->recuperarDadosCobranca($parametros->invoid, $parametros->produtos);

            if(! empty($dados) ) {

                $obs = "Ref. Inventário ".$parametros->invoid." realizado na data " . $dados[0]->data_ajuste;

                $data = date('d/m/Y', strtotime("+1 month",strtotime(date('d-m-Y'))));
                $data = explode('/', $data);
                $vencimento = $data[2] . '-'. $data[1] . '-01';

                $this->dao->begin();

                    $this->dao->inserirDesconto($dados, $vencimento, $obs);

                    $this->dao->atualizarInventario($parametros->invoid, $dados[0]->total_cobrar);

                $this->dao->commit();

                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_COBRANCA;
            } else {
                $this->view->mensagemSucesso = '';
            }


        } catch (Exception $e) {
            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();
        }

        $this->index();
    }


    /**
     * Realiza formatacoes especificaas para os dados vindos do  DB para exibir em tela
     * @author   André Zilz <andre.zilz@sascar.com.br>
     * @param  stdClass $dados
     * @return stdClass
     */
    private function tratarDados($dados) {

        foreach ($dados as $dado) {
            $dado->valor_cobrar = formatarMoeda($dado->valor_cobrar, 'A2B');
            $dado->custo_medio = formatarMoeda($dado->custo_medio, 'A2B');
            $dado->total_cobrar = formatarMoeda($dado->total_cobrar, 'A2B');
        }

        return $dados;

    }

}