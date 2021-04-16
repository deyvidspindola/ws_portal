<?php

/**
 * Classe CadModeloEbs.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   LUIZ FERNANDO PONTARA <fernandopontara@brq.com>
 *
 */

require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';
require_once "lib/funcoes.php";

class CadModeloEbs {

    /** Objeto DAO da classe */
    private $dao;

	/** propriedade para dados a serem utilizados na View. */
    private $view;

	/** Usuario logado */
	private $usuarioLogado;

    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Registro excluído com sucesso.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_ALERTA_CAMPOS_TAMANHO      = "A Descrição deve ter no mínimo dois dígitos.";
    const MENSAGEM_ALERTA_DUPLICIDADE         = "Já existe um registro com a mesma descrição.";


    /**
     * Método construtor.
     * @param $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {


        $this->dao                   = (is_object($dao)) ? $this->dao = $dao : NULL;
        $this->view                  = new stdClass();
        $this->view->mensagemErro    = '';
        $this->view->mensagemAlerta  = '';
        $this->view->mensagemSucesso = '';
        $this->view->dados           = null;
        $this->view->parametros      = null;
        $this->view->paginacao       = null;
        $this->view->status          = false;
        $this->usuarioLogado         = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO (para CRON e WebService)
        $this->usuarioLogado         = (empty($this->usuarioLogado)) ? 2750 : intval($this->usuarioLogado);
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

            $this->view->marcas = $this->dao->getMarcas();

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
        require_once _MODULEDIR_ . "Cadastro/View/cad_modelo_ebs/index.php";
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
        $this->view->parametros->modedescricao = isset($this->view->parametros->modedescricao) && !empty($this->view->parametros->modedescricao) ? trim($this->view->parametros->modedescricao) : "";
        $this->view->parametros->modemmeoid  = isset($this->view->parametros->modemmeoid) && !empty($this->view->parametros->modemmeoid) ? trim($this->view->parametros->modemmeoid) : "";
        $this->view->parametros->modeobroid  = isset($this->view->parametros->modeobroid) && !empty($this->view->parametros->modeobroid) ? trim($this->view->parametros->modeobroid) : "";
    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     * @param stdClass $filtros Filtros da pesquisa
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        $paginacao = new PaginacaoComponente();

        $filtros = $this->tratarParametros();

        $totalRegistros = $this->dao->pesquisar($filtros);

        $this->view->totalResultados = count($totalRegistros);

        //Valida se houve resultado na pesquisa
        if (count($totalRegistros) == 0) {

            $this->view->mensagemAlerta = self::MENSAGEM_NENHUM_REGISTRO;
        }

        // Desabilita combo de classificacao
            $paginacao->desabilitarComboClassificacao();
            $this->view->paginacao = $paginacao->gerarPaginacao($this->view->totalResultados);

            $resultadoPesquisa = $this->dao->pesquisar($filtros, $paginacao->buscarPaginacao());

        $this->view->filtros = $filtros;
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

        $this->view->marcas = $this->dao->getMarcas();

        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado){
            $this->index();
        } else {

            require_once _MODULEDIR_ . "Cadastro/View/cad_modelo_ebs/cadastrar.php";
        }
    }

    /**
     * Responsável por receber exibir o formulário de cadastro ou invocar
     * o metodo para salvar os dados
     * @param stdClass $parametros
     * @return void
     */
    public function cadastrarMarca($parametros = null) {

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

                    $registroGravado = $this->salvarMarca($this->view->parametros);
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


        $this->view->marcas = $this->dao->getMarcas();
        $this->view->status = TRUE;

        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado){
            $this->index();
        } else {

            require_once _MODULEDIR_ . "Cadastro/View/cad_modelo_ebs/cadastrar_marca.php";
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
            if (isset($parametros->modeoid) && intval($parametros->modeoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->modeoid = (int) $parametros->modeoid;

                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->modeoid);

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

        if ((int)$dados->modeoid > 0) {

            //verifica duplicidade
            $registroDuplicado = $this->dao->verificaDuplicidade($dados,2);

            if($registroDuplicado){
                //Seta a mensagem de alerta
                $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_DUPLICIDADE;
            }else{
                //Efetua a gravação do registro
                $gravacao = $this->dao->atualizar($dados);

                //Seta a mensagem de atualização
                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
            }
        } else {

            //verifica duplicidade
            $registroDuplicado = $this->dao->verificaDuplicidade($dados);

            if($registroDuplicado){
                //Seta a mensagem de duplicidade
                $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_DUPLICIDADE;
            }else{
                //Efetua a inserção do registro
                $gravacao = $this->dao->inserir($dados);

                //Seta a mensagem de sucesso
                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
            }
        }

        //Comita a transação
        $this->dao->commit();

        unset($_GET);
        unset($_POST);

        return $gravacao;
    }


    /**
     * Grava os dados na base de dados.
     *
     * @param stdClass $dados Dados a serem gravados
     * @return void
     */
    private function salvarMarca(stdClass $dados) {

        //Validar os campos
        $this->validarCamposCadastroMarca($dados);

        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        if ((int)$dados->mmeoid > 0) {

            //verifica duplicidade
            $registroDuplicado = $this->dao->verificaDuplicidadeMarca($dados,2);

            if($registroDuplicado){
                //Seta a mensagem de alerta
                $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_DUPLICIDADE;
            }else{
                //Efetua a gravação do registro
                $gravacao = $this->dao->atualizarMarca($dados);

                //Seta a mensagem de atualização
                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
            }
        } else {

            //verifica duplicidade
            $registroDuplicado = $this->dao->verificaDuplicidadeMarca($dados);

            if($registroDuplicado){
                //Seta a mensagem de duplicidade
                $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_DUPLICIDADE;
            }else{
                //Efetua a inserção do registro
                $gravacao = $this->dao->inserirMarca($dados);

                //Seta a mensagem de sucesso
                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
            }
        }

        //Comita a transação
        $this->dao->commit();

        unset($_GET);
        unset($_POST);

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
        $camposDestaquesTamanho = array();
        
        //Verifica os campos obrigatórios
        if (!isset($dados->modedescricao) || trim($dados->modedescricao) == '') {
            $camposDestaques[] = array(
                'campo' => 'modedescricao'
            );
        }

        //Verifica os campos obrigatórios
        if (!isset($dados->modemmeoid) || trim($dados->modemmeoid) == '') {
            $camposDestaques[] = array(
                'campo' => 'modemmeoid'
            );
        }

        //Verifica os campos obrigatórios
        if (!isset($dados->modeobroid) || trim($dados->modeobroid) == '') {
            $camposDestaques[] = array(
                'campo' => 'modeobroid'
            );
        }

        if (!empty($camposDestaques)) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        //verifica tamanho dos campos
        if (!isset($dados->modedescricao) || strlen(trim($dados->modedescricao)) < 2) {
            $camposDestaquesTamanho[] = array(
                'campo' => 'modedescricao'
            );
        }

        if (!empty($camposDestaquesTamanho)) {
            $this->view->dados = $camposDestaquesTamanho;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_TAMANHO);
        }
    }


    /**
     * Validar os campos obrigatórios do cadastro.
     *
     * @param stdClass $dados Dados a serem validados
     * @throws Exception
     * @return void
     */
    private function validarCamposCadastroMarca(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();
        $camposDestaquesTamanho = array();
        
        //Verifica os campos obrigatórios
        if (!isset($dados->mmedescricao) || trim($dados->mmedescricao) == '') {
            $camposDestaques[] = array(
                'campo' => 'mmedescricao'
            );
        }

        if (!empty($camposDestaques)) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        //verifica tamanho dos campos
        if (!isset($dados->mmedescricao) || strlen(trim($dados->mmedescricao)) < 2) {
            $camposDestaquesTamanho[] = array(
                'campo' => 'mmedescricao'
            );
        }

        if (!empty($camposDestaquesTamanho)) {
            $this->view->dados = $camposDestaquesTamanho;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_TAMANHO);
        }
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
            if (!isset($parametros->modeoid) || trim($parametros->modeoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->modeoid = (int) $parametros->modeoid;

            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros);

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

    /**
     * Executa a exclusão de registro.
     * @return void
     */
    public function excluirMarca() {

        $retorno = "OK";

        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($parametros->mmeoid) || trim($parametros->mmeoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->mmeoid = (int) $parametros->mmeoid;

            //Valida exclusao
            if($this->dao->verificaExclusaoMarca($parametros)){
                //Remove o registro
                $confirmacao = $this->dao->excluirMarca($parametros);

                if (!$confirmacao) {
                    $retorno = "ERRO";
                }else{
                    //Comita a transação
                    $this->dao->commit();
                }

            }else{
                $retorno = "INVALIDO";
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

        exit();
    }

    /**
     * Buscar Obrigações Financeiras
     * @return json
     */
    public function buscarObrigacaoFinanceira()
    {
        $resultado = array();
        $parametros = $this->tratarParametros();

        if (strlen($parametros->term) > 2) {
            $resultado = $this->dao->getObrigacaoFinanceira($parametros->term);
        }

        header('Content-Type: application/json');
        echo json_encode($resultado);

        exit();
    }

}

