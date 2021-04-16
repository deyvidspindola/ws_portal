<?php

/**
 * Classe Controladora e  Regras de negocio
 *
 * @package  Atendimento
 * @author   André L. Zilz <andre.zilz@sascar.com.br>
 * @since 25/09/20014
 *
 */
class AteVinculoPerfilPortal {

    /*
     * Propriedades
     */
    private $dao;
    private $view;
    private $permissao;


    /**
     * Mensagens do sistema
     */
     const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
     const MENSAGEM_SUCESSO_INCLUIR = "Registro incluí­do com sucesso.";
     const MENSAGEM_SUCESSO_EXCLUIR = "Registro inativado com sucesso.";
     const MENSAGEM_NENHUM_REGISTRO = "Nenhum registro encontrado.";
     const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    /**
     * construtor da classe
     * @param $dao | Objeto da classe de persistencia AteVinculoPerfilPortalDAO
     */
    public function __construct($dao) {

        //Objeto de persistência de dados
        $this->dao = $dao;

        //Cria objeto da view
        $this->view = new stdClass();

        //Mensagens
        $this->view->mensagemErro = '';
        $this->view->mensagemAlerta = '';
        $this->view->mensagemSucesso = '';

        //Dados para view
        $this->view->dados = null;

        //Filtros/parametros utlizados na view
        $this->view->parametros = null;

        //Dados da combo Atendente
        $this->view->comboAtendente = array();

    }

    /**
     * Trata os parametros do POST/GET
     *
     * @return stdClass | Parametros tradados
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

                //Verifica se atributo ja existe e nao sobrescreve.
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
     * Popula os objetos da view
     * @return void
     */
    private function inicializarParametros() {

        if(isset($_SESSION['funcao']['vincular_outros_atendentes_instalador']) && $_SESSION['funcao']['vincular_outros_atendentes_instalador'] == 1) {
             $this->permissao = true;
             $this->view->parametros->usuoid = isset($this->view->parametros->usuoid) ? trim($this->view->parametros->usuoid) : '';
             $this->view->parametros->usuoid_psq = isset($this->view->parametros->usuoid_psq) ? trim($this->view->parametros->usuoid_psq) : '';
        } else {
            $this->permissao = false;
            $this->view->parametros->usuoid = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
            $this->view->parametros->usuoid_psq = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
        }

        if((isset($_SESSION['funcao']['vincular_outros_atendentes_instalador']) && $_SESSION['funcao']['vincular_outros_atendentes_instalador'] == 1)
            || (isset($_SESSION['funcao']['vincular_atendente_instalador']) && $_SESSION['funcao']['vincular_atendente_instalador'] == 1)) {
             $this->view->bloqueioBotoes = false;
         } else{
            $this->view->bloqueioBotoes = true;
         }


        $this->view->parametros->usuario = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        $this->view->parametros->aprrepoid      = isset($this->view->parametros->aprrepoid) ? trim($this->view->parametros->aprrepoid) : '';
        $this->view->parametros->repnome        = isset($this->view->parametros->repnome) ? trim($this->view->parametros->repnome) : '';
        $this->view->parametros->repnome_psq    = isset($this->view->parametros->repnome_psq) ? trim($this->view->parametros->repnome_psq) : '';
        $this->view->parametros->apritloid      = isset($this->view->parametros->apritloid) ? trim($this->view->parametros->apritloid) : '';
        $this->view->parametros->itlnome        = isset($this->view->parametros->itlnome) ? trim($this->view->parametros->itlnome) : '';
        $this->view->parametros->data_inicial   = isset($this->view->parametros->data_inicial) ? trim($this->view->parametros->data_inicial) : '';
        $this->view->parametros->data_final     = isset($this->view->parametros->data_final) ? trim($this->view->parametros->data_final) : '';
        $this->view->parametros->registros      = isset($this->view->parametros->registros) ? trim($this->view->parametros->registros) : 'A';
        $this->view->parametros->tela           = isset($this->view->parametros->tela) ? trim($this->view->parametros->tela) : 'pesquisa';
        $this->view->parametros->aprmotivo      = isset($this->view->parametros->aprmotivo) ? trim($this->view->parametros->aprmotivo) : '';
        $this->view->parametros->aproid         = isset($this->view->parametros->aproid) ? trim($this->view->parametros->aproid) : '';

        //DAdos da pesquisa
        $this->view->dados = isset($this->view->dados) ? trim($this->view->dados) : array();

        //Combos
        $this->view->comboAtendente = $this->recuperarAtendente();

    }

    /**
     * Metodo padrao da classe.
     * @return void
     */
    public function index() {

        try {

            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

        } catch (ErrorException $e) {

            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
        }

        require_once _MODULEDIR_ . "Atendimento/View/ate_vinculo_perfil_portal/index.php";
    }

    /**
    * Pesquisa os vinculos realizados conforme filtro em tela
    *
    * @return stdClass
    */
    public function pesquisar() {

         try {
            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            $this->view->dados = $this->dao->recuperarVinculos($this->view->parametros);

            if(empty($this->view->dados)){
                $this->view->mensagemAlerta = $this::MENSAGEM_NENHUM_REGISTRO;
            }

        } catch (ErrorException $e) {

            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
        }

        require_once _MODULEDIR_ . "Atendimento/View/ate_vinculo_perfil_portal/index.php";

    }

    /**
    * Inclui um novo registro de perfil - AJAX
    *
    * @return string
    */
    public function incluirPerfil() {

        $this->view->parametros = $this->tratarParametros();

        //Inicializa os dados
        $this->inicializarParametros();

        //Tratamento contra injection e charset
        $this->view->parametros->aprmotivo = $this->tratarTextoInput($this->view->parametros->aprmotivo, true);

        try{

            //Verifica se ja existe um perfil ativo
            $retorno = $this->dao->validarInclusaoPerfil($this->view->parametros);

            if($retorno) {
                echo 'EXISTE';
                exit;
            }

            $this->dao->begin();

            $aproid = $this->dao->incluirPerfil($this->view->parametros);

            if(!empty( $aproid)){
                $this->dao->commit();

                echo $aproid;

            } else {
                echo 'ERRO';
            }

        }catch(Exception $e) {
            $this->dao->rollback();
            echo 'ERRO';
        }

        exit;
    }

    /**
    * inativar um registro de perfil - AJAX
    *
    * @return string
    */
    public function inativarPerfil() {

        $this->view->parametros = $this->tratarParametros();

        //Inicializa os dados
        $this->inicializarParametros();

        try{

            $this->dao->begin();

            if($this->dao->inativarPerfil($this->view->parametros)) {
                $this->dao->commit();
                echo 'OK';

            } else {
                echo 'ERRO';
            }

        }catch(Exception $e) {
            $this->dao->rollback();
            echo 'ERRO';
        }

        exit;

    }

    /**
    * Recupera dados para  popular combo Atendente
    *
    * @return array
    *
    */
    private function recuperarAtendente() {

        if( !$this->permissao ) {
            $usuoid = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
        } else {
            $usuoid = '';
        }

        return  $this->dao->recuperarAtendente($usuoid);
    }


    /**
     * Buscar dados representante - AJAX
     *
     * @return array $retorno
     */
    public function recuperarRepresentante() {

        $parametros = $this->tratarParametros();

        $parametros->nome = $this->tratarTextoInput($parametros->term, true);

        $retorno = $this->dao->recuperarRepresentante($parametros);

        echo json_encode($retorno);
        exit;
    }

     /**
     * Buscar dados instalador - AJAX
     *
     * @return array $retorno
     */
    public function recuperarInstalador() {

        $parametros = $this->tratarParametros();

        $retorno = $this->dao->recuperarInstalador($parametros->aprrepoid);

        echo json_encode($retorno);
        exit;
    }

    /**
    * Tratamento de input de dados, contra injection code
    * @param string $dado
    * @return string
    */
    private function tratarTextoInput($dado, $autocomplete = false){

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

