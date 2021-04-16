<?php

/**
 * Classe padrão para Action
 *
 * @package Intranet
 * @since   version 
 * @category Action
 */
class CadGrupoTrabalho {

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
        
        //Erros do formulário
        $this->view->errorsForm = array();
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

            //Realiza a pesquisa ao banco de dados
            $this->view->dados = $this->pesquisar($this->view->parametros);

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
        }
        
        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Cadastro/View/cad_grupo_trabalho/index.php";
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
        $this->view->parametros->gtroid = isset($this->view->parametros->gtroid) ? trim($this->view->parametros->gtroid) : '';
        //Carrega os dados do combo
        $this->view->parametros->grupos = $this->dao->pesquisar();
        
        $this->view->parametros->gtrvisualizacao_individual = isset($this->view->parametros->gtrvisualizacao_individual) ? $this->view->parametros->gtrvisualizacao_individual : '';
        $this->view->parametros->gtrlancamento_edicao       = isset($this->view->parametros->gtrlancamento_edicao)       ? $this->view->parametros->gtrlancamento_edicao       : '';

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
     * @return void
     */
    public function cadastrar() {
        
        try{

            $this->view->parametros = $this->tratarParametros();
            
            //Incializa os parametros
            $this->inicializarParametros();

            //Busca o ID do usuário
            $this->view->parametros->cd_usuario = isset($_SESSION['usuario']['oid']) && $_SESSION['usuario']['oid'] != '' ? $_SESSION['usuario']['oid'] : '';
            
            //Grava o registro
            $this->salvar($this->view->parametros);
        
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

        if ($dados->gtroid > 0) {
            //Efetua a gravação do registro
            $gravacao = $this->dao->atualizar($dados);
            
            //Seta a mensagem de atualização
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
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
        
        if (!isset($dados->gtroid) || trim($dados->gtroid) == '') {
            $camposDestaques[] = array(
                'campo' => 'gtroid'
            );
            $error = true;
        }
        
        if ($error) {
            $this->view->errorsForm = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

}

