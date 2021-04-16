<?php

/**
 * Classe CadVersaoTrava5Roda.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   ERIK VANDERLEI <erik.vanderlei@sascar.com.br>
 *
 */
class CadVersaoTrava5Roda {

    /** Objeto DAO da classe */
    private $dao;

	/** propriedade para dados a serem utilizados na View. */
    private $view;

	/** Usuario logado */
	private $usuarioLogado;

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Registro excluído com sucesso.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";

    /**
     * Método construtor.
     * @param $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {


        $this->dao                   = (is_object($dao) ? $this->dao = $dao : NULL);
        $this->view                  = new stdClass();
        $this->view->mensagemErro    = '';
        $this->view->mensagemAlerta  = '';
        $this->view->mensagemSucesso = '';
        $this->view->dados           = null;
        $this->view->parametros      = null;
        $this->view->status          = false;
        $this->usuarioLogado         = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO (para CRON e WebService)
        $this->usuarioLogado         = (empty($this->usuarioLogado) ? 2750 : intval($this->usuarioLogado));
    }

    /**
     * Reponsável também por realizar a pesquisa invocando o método privado
     * @return void
     */
    public function index() {

        try {
            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        //Incluir a view padrão
        require_once _MODULEDIR_ . "Cadastro/View/cad_versao_trava5_roda/index.php";
    }

    /**
     * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros
     *
     * @return stdClass Parametros tradados
     * @return stdClass
     */
    private function tratarParametros() {

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

    /**
     * Popula e trata os parametros bidirecionais entre view e action
     * @return void
     */
    private function inicializarParametros() {

        //Verifica se os parametro existem, senão iniciliza todos
        $this->view->parametros->trvoid = isset($this->view->parametros->trvoid) ? $this->view->parametros->trvoid : "";
        $this->view->parametros->trvcadastro = isset($this->view->parametros->trvcadastro) ? $this->view->parametros->trvcadastro : "";
        $this->view->parametros->trvdescricao = isset($this->view->parametros->trvdescricao) ? $this->view->parametros->trvdescricao : "";
        $this->view->parametros->trvexclusao = isset($this->view->parametros->trvexclusao) ? $this->view->parametros->trvexclusao : "";
        $this->view->parametros->trvusuoid_excl = isset($this->view->parametros->trvusuoid_excl) ? $this->view->parametros->trvusuoid_excl : "";
    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     * @param stdClass $filtros Filtros da pesquisa
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        $resultadoPesquisa = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->status = TRUE;

        return $resultadoPesquisa;
    }

    /**
     * Responsável por receber exibir o formulário de cadastro ou invocar
     * o metodo para salvar os dados
     * @param stdClass $parametros
     * @return void
     */
    public function cadastrar($parametros = null) {

        //identifica se o registro foi gravado
        $registroGravado = FALSE;
        try{

            if (is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = $parametros;
            }

            //Incializa os parametros
            $this->inicializarParametros();


            //Verificar se foi submetido o formulário e grava o registro em banco de dados
            if (isset($_POST) && !empty($_POST)) {
                $registroGravado = $this->salvar($this->view->parametros);
            }

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }

        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado){
            $this->index();
        } else {

            require_once _MODULEDIR_ . "Cadastro/View/cad_versao_trava5_roda/cadastrar.php";
        }
    }

    /**
     * Responsável por receber exibir o formulário de edição ou invocar
     * o metodo para salvar os dados
     * @return void
     */
    public function editar() {

        try {
            //Parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id do cadastro
            if (isset($parametros->trvoid) && intval($parametros->trvoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->trvoid = (int) $parametros->trvoid;

                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->trvoid);

                //Chama o metodo para edição passando os dados do registro por parametro.
                $this->cadastrar($dados);
            } else {
                $this->index();
            }

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        }
    }

    /**
     * Grava os dados na base de dados.
     *
     * @param stdClass $dados Dados a serem gravados
     * @return void
     */
    private function salvar(stdClass $dados) {

        //Validar os campos
        $this->validarCamposCadastro($dados);

        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        if ($dados->trvoid > 0) {
            //Efetua a gravação do registro
            $gravacao = $this->dao->atualizar($dados);

            //Seta a mensagem de atualização
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        } else {
            //Efetua a inserção do registro
            $gravacao = $this->dao->inserir($dados);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        }

        //Comita a transação
        $this->dao->commit();

        return $gravacao;
    }

    /**
     * Validar os campos obrigatórios do cadastro.
     *
     * @param stdClass $dados Dados a serem validados
     * @throws Exception
     * @return void
     */
    private function validarCamposCadastro(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        /**
         * Verifica os campos obrigatórios
         */
        if (!isset($dados->trvdescricao) || trim($dados->trvdescricao) == '') {
            $camposDestaques[] = array(
                'campo' => 'trvdescricao'
            );
        }

        if (!empty($camposDestaques)) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
     * Executa a exclusão de registro.
     * @return void
     */
    public function excluir() {

        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($parametros->trvoid) || trim($parametros->trvoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->trvoid = (int) $parametros->trvoid;

            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->trvoid);

            //Comita a transação
            $this->dao->commit();

            if ($confirmacao) {

                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_EXCLUIR;
            }

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }

        $this->index();
    }


}

