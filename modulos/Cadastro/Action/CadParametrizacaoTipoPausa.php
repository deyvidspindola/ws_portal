<?php

/**
 * Classe padrão para Action
 * 
 * @since   version 
 * @category Action
 * @package Intranet
 */
class CadParametrizacaoTipoPausa {

    /**
     * Objeto DAO da classe.
     * 
     * @var CadParametrizacaoTipoPausaDAO
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

        //Filtros/parametros utlizados na view
        $this->view->parametros = null;

        //Status de uma transação 
        $this->view->status = false;
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

            //Inicializa os dados
            $this->inicializarParametros();

            //Carrega a pesquisa
            $this->view->dados = $this->pesquisar();
        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();
        }

        //Inclir a view padrão        
        require_once _MODULEDIR_ . "Cadastro/View/cad_parametrizacao_tipo_pausa/index.php";
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
            //Limpa o POST
            unset($_POST);
        }

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {

                //Verifica se atributo já existe e não sobrescreve.
                if (!isset($retorno->$key)) {
                    $retorno->$key = isset($_GET[$key]) ? $value : '';
                }
            }
            //Limpa o GET
            unset($_GET);
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
        // Ex. $this->view->parametros->excnome = isset($this->view->parametros->excnome) ? trim($this->view->parametros->excnome) : '';
        $this->view->parametros->hrpoid = isset($this->view->parametros->hrpoid) ? trim($this->view->parametros->hrpoid) : '';

        //Carrega os dados da combo de Grupo de Trabalho
        $this->view->parametros->comboGrupoTrabalho = $this->dao->carregarComboGrupoTrabalho();

        //Carrega os dados da combo de Tipo Pausa
        $this->view->parametros->comboTipoPausa = $this->dao->carregarComboTipoPausa();
    }

    /**
     * Responsável por tratar e retornar o resultado da pesquisa. 
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * 
     * @return array
     */
    private function pesquisar(stdClass $filtros = null) {

        $resultadoPesquisa = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0 && $this->view->parametros->acao != 'excluir' && empty($this->view->mensagemAlerta)) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->status = true;

        return $resultadoPesquisa;
    }

    /**
     * Responsável por receber exibir o formulário de cadastro ou invocar
     * o metodo para salvar os dados
     * 
     * @param stdClass $parametros Dados do cadastro, para edição (opcional)
     * 
     * @return void
     */
    public function cadastrar($parametros = null) {

        //identifica se o registro foi gravado
        $registroGravado = false;
        try {

            if (is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = $parametros;
            }
            
            //Incializa os parametros
            $this->inicializarParametros();

            //Verificar se foi submetido o formulário e grava o registro em banco de dados 
            if (isset($this->view->parametros->bt_confirmar)) {
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

        $this->index();
        
    }

    /**
     * Responsável por receber exibir o formulário de edição ou invocar
     * o metodo para salvar os dados
     * 
     * @return void
     */
    public function editar() {

        try {
            //Parametros 
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id do cadastro
            if (isset($parametros->hrpoid) && intval($parametros->hrpoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->hrpoid = (int) $parametros->hrpoid;

                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->hrpoid);

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
     * 
     * @return void
     */
    private function salvar(stdClass $dados) {

        //Validar os campos
        $this->validarCamposCadastro($dados);

        $dados->hrpusuoid = $_SESSION['usuario']['oid'];

        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        if ($dados->hrpoid > 0) {
            //Efetua a gravação do registro
            $gravacao = $this->dao->atualizar($dados);

            //Seta a mensagem de atualização
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        } else {

            //Verificação para registro duplicado
            $pausaJaCadastrada = $this->dao->pesquisarPausaCadastrada($dados);

            if ($pausaJaCadastrada) {
                throw new Exception('Já existe um registro com o grupo de trabalho e tipo de pausa cadastrado.');
            }

            //Efetua a inserção do registro
            $gravacao = $this->dao->inserir($dados);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        }

        //Comita a transação
        $this->dao->commit();

        unset($this->view->parametros);

        return $gravacao;
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
    private function validarCamposCadastro(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $erros = array();

        //Verifica se houve erro
        $obrigatoriosPreenchidos = true;

        /**
         * Verifica os campos obrigatórios
         */
        //Grupo de Trabalho
        if (!isset($dados->gtroid) || empty($dados->gtroid)) {
            $obrigatoriosPreenchidos = false;
            $erros[] = array(
                'campo' => 'gtroid',
                'mensagem' => utf8_encode('Campo obrigatório.')
            );
        }

        //Tipo Trabalho
        if (!isset($dados->motaoid) || empty($dados->motaoid)) {
            $obrigatoriosPreenchidos = false;
            $erros[] = array(
                'campo' => 'motaoid',
                'mensagem' => utf8_encode('Campo obrigatório.')
            );
        }

        //Exibe Alerta
        if (!isset($dados->hrpexibe_alerta) || $dados->hrpexibe_alerta == "") {
            $obrigatoriosPreenchidos = false;
            $erros[] = array(
                'campo' => 'hrpexibe_alerta',
                'mensagem' => utf8_encode('Campo obrigatório.')
            );
        }

        //Cadastro Obrigatório
        if (!isset($dados->hrpcadastro_obrigatorio) || $dados->hrpcadastro_obrigatorio == "") {
            $obrigatoriosPreenchidos = false;
            $erros[] = array(
                'campo' => 'hrpcadastro_obrigatorio',
                'mensagem' => utf8_encode('Campo obrigatório.')
            );
        }

        //Tolerância
        if (!isset($dados->hrptolerancia) || $dados->hrptolerancia == "") {
            $obrigatoriosPreenchidos = false;
            $erros[] = array(
                'campo' => 'hrptolerancia',
                'mensagem' => utf8_encode('Campo obrigatório.')
            );
        }

        //Tempo
        if (!isset($dados->hrptempo) || trim($dados->hrptempo) == "") {
            $obrigatoriosPreenchidos = false;
            $erros[] = array(
                'campo' => 'hrptempo',
                'mensagem' => utf8_encode('Campo obrigatório.')
            );
        }

        if (!$obrigatoriosPreenchidos) {
            $this->view->erros = $erros;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
     * Executa a exclusão de registro.
     * 
     * @return void 
     */
    public function excluir() {
        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            $this->view->parametros->acao = $parametros->acao;

            //Verifica se foi informado o id
            if (!isset($parametros->hrpoid) || trim($parametros->hrpoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->hrpoid = (int) $parametros->hrpoid;

            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->hrpoid);

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

