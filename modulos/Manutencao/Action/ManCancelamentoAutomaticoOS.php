<?php

/**
 * Classe padrão para Action
 *
 * @package  Cadastro
 * @author   Robson Aparecido Trizotte da Silva <robson.silva@meta.com.br>
 * 
 */
class ManCancelamentoAutomaticoOS {

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
    const MENSAGEM_SUCESSO_ATUALIZAR = "Parametrização salva com sucesso.";

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
            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            //Verificar se a ação pesquisar e executa pesquisa
            if (isset($this->view->parametros->acao) && $this->view->parametros->acao == 'gravar') {
                $this->salvar($this->view->parametros);
            }

            $this->view->dados = $this->dao->pesquisarParametrizacao();

            //Transforma em array os campos
            $this->view->dados->pcaotipos_de = !empty($this->view->dados->pcaotipos_de) ? explode(',', $this->view->dados->pcaotipos_de) : '';
            $this->view->dados->pcaotipos_para = !empty($this->view->dados->pcaotipos_para) ? explode(',', $this->view->dados->pcaotipos_para) : '';
            $this->view->dados->pcaostatus_de = !empty($this->view->dados->pcaostatus_de) ? explode(',', $this->view->dados->pcaostatus_de) : '';
            $this->view->dados->pcaostatus_para = !empty($this->view->dados->pcaostatus_para) ? explode(',', $this->view->dados->pcaostatus_para) : '';
            
        } catch (ErrorException $e) {

            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
        }

        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Manutencao/View/man_cancelamento_automatico_os/index.php";
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
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     * 
     * @return stdClass Tipo Contrato
     * 
     * @retrun stdClass
     */
    private function buscarTiposContrato() {

        return $this->dao->pesquisar();
    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     * 
     * @return stdClass Tipo Contrato
     * 
     * @retrun stdClass
     */
    private function buscarContratoSituacao() {

        return $this->dao->pesquisarContratoSituacao();
    }

    /**
     * Popula os arrays para os combos de estados e cidades
     * 
     * @return void
     */
    private function inicializarParametros() {

        //Verifica se os parametro existem, senão iniciliza todos
        $this->view->parametros->tpcoid = isset($this->view->parametros->tpcoid) ? trim($this->view->parametros->tpcoid) : '';
        $this->view->parametros->tpcdescricao = isset($this->view->parametros->tpcdescricao) ? trim($this->view->parametros->tpcdescricao) : '';
        $this->view->tiposContrato = $this->buscarTiposContrato();
        $this->view->contratoSituacao = $this->buscarContratoSituacao();
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
     * Responsável por tratar e retornar o resultado da pesquisa. 
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * 
     * @return array
     */
    private function pesquisarContratoSituacao(stdClass $filtros) {

        $resultadoPesquisa = $this->dao->pesquisarContratoSituacao($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->status = TRUE;

        return $resultadoPesquisa;
    }

    /**
     * Grava os dados na base de dados.
     * 
     * @param stdClass $dados Dados a serem gravados
     * 
     * @return void
     */
    private function salvar(stdClass $dados) {
        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        //pega o usuário logado
        $dados->pcaousuoid_cadastro = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Efetua a gravação do registro
        $gravacao = $this->dao->salvar($dados);

        //Seta a mensagem de atualização
        $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        //Comita a transação
        $this->dao->commit();
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

            //Verifica se foi informado o id
            if (!isset($parametros->osmcoid) || trim($parametros->osmcoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->osmcoid = (int) $parametros->osmcoid;

            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->osmcoid);

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

