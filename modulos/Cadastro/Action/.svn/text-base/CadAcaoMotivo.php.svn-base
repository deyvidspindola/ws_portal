<?php

/**
 * Classe padrão para Action
 *
 * @package Intranet
 * @since   version 
 * @category Action
 */
class CadAcaoMotivo {

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
    public function index($pesquisar = FALSE) {
        try {
            
            if (!$pesquisar){
                //Busca os parametros
                $this->view->parametros = $this->tratarParametros();
            }
            
            //Inicializa os dados
            $this->inicializarParametros();

            //Verificar se a ação pesquisar e executa pesquisa
            if (isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar') {
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }
        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();
        }

        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Cadastro/View/cad_acao_motivo/index.php";
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
    private function inicializarParametros($cadastrar = FALSE) {

        //Verifica se os parametro existem, senão iniciliza todos
        $this->view->parametros->aoamoid = isset($this->view->parametros->aoamoid) ? trim($this->view->parametros->aoamoid) : '';
        $this->view->parametros->aoamdescricao = isset($this->view->parametros->aoamdescricao) ? trim($this->view->parametros->aoamdescricao) : '';

        /**
         * Carrega dados combo Ações.
         * Apenas para view de pesquisa
         */
        if (!$cadastrar){
            $this->view->parametros->acoes = isset($this->view->parametros->acoes) ? $this->view->parametros->acoes : $this->dao->buscarAcoes();

            $filtroMotivos = new stdClass();

            if (intval($this->view->parametros->aoamoid) > 0) {
                $filtroMotivos->aoampai = intval($this->view->parametros->aoamoid);
                $this->view->parametros->motivos = $this->dao->buscarMotivos($filtroMotivos);
            } else {

                $filtroMotivos->aoampai = '';
                $this->view->parametros->motivos = $this->dao->buscarMotivos($filtroMotivos);
            }
        }
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
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->status = TRUE;

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
        $registroGravado = FALSE;
        try {

            if (is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = $parametros;
            }

            //Incializa os parametros
            $this->inicializarParametros(true);
            

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

        //Verifica se o registro foi gravado e chama o metodo para editar
        if ($registroGravado !== false) {

            $this->editar($registroGravado);
        } else {

            require_once _MODULEDIR_ . "Cadastro/View/cad_acao_motivo/cadastrar.php";
        }
    }

    /**
     * Responsável por receber exibir o formulário de edição ou invocar
     * o metodo para salvar os dados
     * 
     * @return void
     */
    public function editar($id = NULL) {

        try {
            //Verifica se o id foi passado por parametro
            if (is_null($id)) {
                //Busca os parametros do POST/GET
                $parametros = $this->tratarParametros();
                $parametros->aoamoid = $parametros->aoamoid;
            } else {
                $parametros = new stdClass();
                $parametros->aoamoid = $id;
            }
            
            //Verifica se foi informado o id do cadastro
            if (isset($parametros->aoamoid) && intval($parametros->aoamoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->aoamoid = (int) $parametros->aoamoid;

                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->aoamoid);
                
                
                //Verifica se retornou registro na pesquisa
                if (isset($dados->aoamoid)){
                    $filtroMotivos = new stdClass();
                    $filtroMotivos->aoampai = intval($parametros->aoamoid);
                    //Dados dos motivos
                    $dados->motivos = $this->dao->buscarMotivos($filtroMotivos);
                    

                    //Chama o metodo para edição passando os dados do registro por parametro.
                    $this->cadastrar($dados);
                } else {
                    $this->index();
                }


            } else {
                $this->index();
            }
        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        }
    }

    public function cadastrarMotivo() {

        //identifica se o registro foi gravado
        $registroGravado = FALSE;
        try {

            $this->view->parametros = $this->tratarParametros();


            //Incializa os parametros
            $this->inicializarParametros();

            //Grava o registro
            $registroGravado = $this->salvar($this->view->parametros);
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }

        $this->editar($this->view->parametros->aoampai);
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

        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        //Busca o id do usuário
        $dados->cd_usuario = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        if ($dados->aoamoid > 0) {
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
     * 
     * @throws Exception
     * 
     * @return void
     */
    private function validarCamposCadastro(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        /**
         * Verifica os campos obrigatórios
         */
        if (!isset($dados->aoamdescricao) || trim($dados->aoamdescricao) == '') {

            $idCampoDescricao = ($dados->acao == 'cadastrarMotivo') ? 'aoamdescricao_motivo' : 'aoamdescricao';

            $camposDestaques[] = array(
                'campo' => $idCampoDescricao
            );
            $error = true;
        }


        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
     * Executa a ação
     * 
     * @return void 
     */
    public function excluir() {
        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($parametros->acao_id) || trim($parametros->acao_id) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();
            
            //Filtro para exclusão
            $dados = new stdClass();
            
            //Realiza o CAST do parametro
            $dados->acao_id = (int) $parametros->acao_id;
            
            //Usuário que realizou a exclusão
            $dados->cd_usuario = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

            //Remove o registro
            $confirmacao = $this->dao->excluir($dados);

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

        $this->view->parametros = $parametros;
        $this->view->parametros->acao = 'pesquisar';

        $this->index(TRUE); 
    }

    /**
     * Executa a exclusão de registro Motivo.
     * 
     * @return void 
     */
    public function excluirMotivo() {

        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($parametros->motivo_id) || trim($parametros->motivo_id) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Filtro para exclusão
            $dados = new stdClass();
            
            //Realiza o CAST do parametro
            $dados->acao_id = (int) $parametros->motivo_id;
            
            //Usuário que realizou a exclusão
            $dados->cd_usuario = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

            //Remove o registro
            $confirmacao = $this->dao->excluir($dados);

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
        
        if ( isset($parametros->postTelaEdicao) ){
            
            $this->editar($parametros->aoamoid);
            
        } else {
            
            $this->view->parametros = $parametros;
            $this->view->parametros->acao = 'pesquisar';
            
            if ($this->view->parametros->aoamoid == 0) {
                unset($this->view->parametros->aoamoid);
            }

            $this->index(TRUE); 
        }
    }

    /**
     * Método carregarMotivos()
     * Responsável por carregar os motivos conforme a ação informada
     * 
     * @return void
     */
    public function carregarMotivos() {
        try {

            $retorno = array();

            $parametros = $this->tratarParametros();

            $dados = $this->dao->buscarMotivos($parametros);

            foreach ($dados as $motivo) {
                $retorno[] = array(
                    'id' => $motivo->aoamoid,
                    'label' => utf8_encode($motivo->aoamdescricao)
                );
            };

            echo json_encode($retorno);
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();
            echo json_encode($retorno);
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();
            echo json_encode($retorno);
        }
        exit;
    }

}

