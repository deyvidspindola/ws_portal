<?php

/**
 * Classe PrnRegraAberturaOs.
 * Camada de regra de negócio.
 *
 * @package  Principal
 * @author   LUIZ FERNANDO PONTARA <fernandopontara@brq.com>
 *
 */
class PrnRegraAberturaOs {

    /** Objeto DAO da classe */
    private $dao;

	/** propriedade para dados a serem utilizados na View. */
    private $view;

	/** Usuario logado */
	private $usuarioLogado;

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_ALERTA_CADASTRADO          = "Parametrização já cadastrada.";
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

            //popula filtros
            $this->view->filtros = new stdClass;
            //campo tipo
            $this->view->filtros->tipo = $this->dao->getTipo();

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
        require_once _MODULEDIR_ . "Principal/View/prn_regra_abertura_os/index.php";
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
		$this->view->parametros->ordraoid = isset($this->view->parametros->ordraoid) ? $this->view->parametros->ordraoid : "" ; 		
        $this->view->parametros->ordraostoid = isset($this->view->parametros->ordraostoid) ? $this->view->parametros->ordraostoid : "" ; 		
        $this->view->parametros->ordrapermite_ordens_simultaneas = isset($this->view->parametros->ordrapermite_ordens_simultaneas) ? $this->view->parametros->ordrapermite_ordens_simultaneas : "" ; 		
        $this->view->parametros->ordrapermite_tipo_motivo_distinto = isset($this->view->parametros->ordrapermite_tipo_motivo_distinto) ? $this->view->parametros->ordrapermite_tipo_motivo_distinto : "" ; 		
        $this->view->parametros->ordradt_cadastro = isset($this->view->parametros->ordradt_cadastro) ? $this->view->parametros->ordradt_cadastro : "" ; 		
        $this->view->parametros->ordradt_exclusao = isset($this->view->parametros->ordradt_exclusao) ? $this->view->parametros->ordradt_exclusao : "" ; 		
        $this->view->parametros->ordrausuoid_inclusao = isset($this->view->parametros->ordrausuoid_inclusao) ? $this->view->parametros->ordrausuoid_inclusao : "" ; 		
        $this->view->parametros->ordrausuoid_exclusao = isset($this->view->parametros->ordrausuoid_exclusao) ? $this->view->parametros->ordrausuoid_exclusao : "" ; 

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

            $this->view->dados = new stdClass;
            //para campo Tipo OS
            $this->view->dados->tipoCadastro = $this->dao->getTipoCadastro($this->view->parametros->ordraostoid);

            //para campo Tipo Permitidos
            $this->view->dados->tipo = $this->dao->getTipo();

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

            require_once _MODULEDIR_ . "Principal/View/prn_regra_abertura_os/cadastrar.php";
        }
    }


    public function cadastrarParametrizacao(){

        $retorno = "OK";

        try{

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Validar os campos
            $this->validarCamposCadastro($parametros);
            
            //verifica se parametrização já está cadastrada

            if(!$this->dao->parametrizacaoExistente($parametros->ordraostoid)){

                //Inicia a transação
                $this->dao->begin();

                //Remove o registro
                $confirmacao = $this->dao->inserir($parametros);

                if (!$confirmacao) {
                    $retorno = "ERRO";
                }else{
                    //Comita a transação
                    $this->dao->commit();

                    $retorno = $confirmacao;
                }
            }else{
                $retorno = "ERRO";
            }

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $retorno = "ERRO";
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $retorno = "ERRO";
        }

        echo $retorno;

        exit;
    }

    public function editarParametrizacao() {
        $retorno = "OK";

        try{

            $parametros = $this->tratarParametros();

            $this->validarCamposCadastro($parametros);
            

            if($this->dao->parametrizacaoExistente($parametros->ordraostoid)){

                $this->dao->begin();

                if($parametros->ordrapermite_ordens_simultaneas == 'false') {
                    $this->dao->excluirTodasSimultaneas($parametros->ordraoid);
                }
                if($parametros->ordrapermite_tipo_motivo_distinto == 'false') {
                    $this->dao->excluirTodosMotivos($parametros->ordraoid, $parametros->ordraostoid);
                }

                $confirmacao = $this->dao->editarParametrizacao($parametros);

                if (!$confirmacao) {
                    $retorno = "ERRO";
                }else{
                    $this->dao->commit();

                    $retorno = $confirmacao;
                }
            }else{
                $retorno = "ERRO";
            }

        } catch (ErrorException $e) {
            $this->dao->rollback();

            $retorno = "ERRO";
        } catch (Exception $e) {
            $this->dao->rollback();

            $retorno = "ERRO";
        }

        echo $retorno;

        exit;
    }


    /**
     * Cadastrar regra onde Permite O.S. Simultânea
     */
    public function cadastrarRegraSimultanea(){

        $retorno = "OK";

        try{

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            if(!$this->dao->simultaneaExistente($parametros)) {

                //Inicia a transação
                $this->dao->begin();

                //Remove o registro
                $confirmacao = $this->dao->inserirRegraSimultanea($parametros);

                if (!$confirmacao) {
                    $retorno = "ERRO";
                }else{
                    //Comita a transação
                    $this->dao->commit();
                }
            } else {
                $retorno = "DUPLICADO";
            }

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $retorno = "ERRO";
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $retorno = "ERRO";
        }

        echo $retorno;

        exit;

    }

    public function cadastrarMotivo(){

        $retorno = "OK";

        try{

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            if(!$this->dao->motivoExistente($parametros)) {

                //Inicia a transação
                $this->dao->begin();

                //Remove o registro
                $confirmacao = $this->dao->inserirMotivo($parametros);

                if (!$confirmacao) {
                    $retorno = "ERRO";
                }else{
                    //Comita a transação
                    $this->dao->commit();
                }
            } else {
                $retorno = "DUPLICADO";
            }

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $retorno = "ERRO";
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $retorno = "ERRO";
        }

        echo $retorno;

        exit;

    }

    public function recuperaParametrizacoesCadastradas() {
        $parametros = $this->tratarParametros();

        echo json_encode($this->dao->recuperaParametrizacoesCadastradas($parametros->ordraoid));

        exit;
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
            if (isset($parametros->ordraoid) && intval($parametros->ordraoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->ordraoid = (int) $parametros->ordraoid;

                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->ordraoid);

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
        if (!isset($dados->ordraostoid) || trim($dados->ordraostoid) == '') {
            $camposDestaques[] = array(
                'campo' => 'ordraostoid'
            );
        }

        if (!empty($camposDestaques)) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    public function excluirRegra() {
        $retorno = "OK";

        try {

            $parametros = $this->tratarParametros();

            if (!isset($parametros->id) || trim($parametros->id) == '' ||
                !isset($parametros->tipo) || trim($parametros->tipo) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            $this->dao->begin();

            if($parametros->tipo == 'Motivo Distinto') {
                $confirmacao = $this->dao->excluirMotivo($parametros->id);
            } else {
                $confirmacao = $this->dao->excluirSimultanea($parametros->id);
            }

            if (!$confirmacao) {
                $retorno = "ERRO";
            }else{
                $this->dao->commit();
            }

        } catch (ErrorException $e) {
            $this->dao->rollback();

            $retorno = "ERRO";
        } catch (Exception $e) {
            $this->dao->rollback();

            $retorno = "ERRO";
        }

        echo $retorno;

        exit;
    }

    /**
     * Executa a exclusão de registro.
     * @return void
     */
    public function excluir() {

        $retorno = "OK";

        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($parametros->ordraoid) || trim($parametros->ordraoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->ordraoid = (int) $parametros->ordraoid;

            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->ordraoid);

            if (!$confirmacao) {
                $retorno = "ERRO";
            }else{
                //Comita a transação
                $this->dao->commit();
            }

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $retorno = "ERRO";
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $retorno = "ERRO";
        }

        echo $retorno;

        exit;
    }

}

