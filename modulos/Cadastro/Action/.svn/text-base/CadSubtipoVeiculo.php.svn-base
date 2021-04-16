<?php

/**
 * Classe CadSubtipoVeiculo.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   Davi Junior <davi.junior.ext@sascar.com.br>
 *
 */
class CadSubtipoVeiculo {

    private $dao;
    private $view;
    private $usuarioLogado;
    private $urlPagina;

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Registro excluído com sucesso.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";

    public function __construct($dao = null) {


        $this->dao                   = (is_object($dao)) ? $dao : NULL;
        $this->view                  = new stdClass();
        $this->view->mensagemErro    = '';
        $this->view->mensagemAlerta  = '';
        $this->view->mensagemSucesso = '';
        $this->view->dados           = null;
        $this->view->parametros      = null;
        $this->urlPagina             = "cad_subtipo_veiculo.php";
        $this->usuarioLogado         = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
    }

    /**
     * Reponsável também por realizar a pesquisa invocando o método privado
     * @return void
     */
    public function index() {

        try {

            $this->view->parametros = $this->tratarParametros();
            $this->inicializarParametros();

            //Valida se o departamento e cargo do susuário logado pussuem permissão para a pagina acessada
            if (!validarPermissaoPagina($this->usuarioLogado, $this->urlPagina) ){
                header('Location: acesso_invalido.php');
            }

            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        require_once _MODULEDIR_ . "Cadastro/View/cad_subtipo_veiculo/index.php";
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

        return $retorno;
    }

    /**
     * Popula e trata os parametros bidirecionais entre view e action
     * @return void
     */
    private function inicializarParametros() {

        $this->view->parametros->vstdescricao    = isset($this->view->parametros->vstdescricao)    ? trim($this->view->parametros->vstdescricao)  : '';
        $this->view->parametros->vstoid          = isset($this->view->parametros->vstoid)          ? $this->view->parametros->vstoid              : 0;
        $this->view->parametros->tipvdescricao   = isset($this->view->parametros->tipvdescricao)   ? trim($this->view->parametros->tipvdescricao) : '';
        $this->view->parametros->tipvoid         = isset($this->view->parametros->tipvoid)         ? $this->view->parametros->tipvoid             : 0;
        $this->view->parametros->acao            = isset($this->view->parametros->acao)            ? $this->view->parametros->acao                : 'index';

        if($this->view->parametros->acao == 'cadastrar' || $this->view->parametros->acao == 'editar') {
            $this->view->listaTipo = $this->dao->recuperarListaTipo();
        }

    }


    private function pesquisar(stdClass $filtros) {

        $ordenacao = array(
            ''                => 'Escolha',
            'vstdescricao'    => 'Subtipo',
            'tipvdescricao'   => 'Tipo'
        );

        $quantidade = array(10, 25, 50, 100);

        $resultadoPesquisa = $this->dao->pesquisar( $filtros );

        if ( $resultadoPesquisa->total == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';

        $paginacao = new PaginacaoComponente();
        $paginacao->setarCampos($ordenacao);
        $paginacao->setQuantidadesArray($quantidade);
        $this->view->ordenacao = $paginacao->gerarOrdenacao();
        $this->view->paginacao = $paginacao->gerarPaginacao($resultadoPesquisa->total);
        $this->view->totalResultados = $resultadoPesquisa->total;

        $resultadoPesquisa = $this->dao->pesquisar(
            $filtros, $paginacao->buscarPaginacao(), $paginacao->buscarOrdenacao()
        );

        return $resultadoPesquisa;
    }


    public function cadastrar($parametros = null) {


        $registroGravado = FALSE;

        try{

            if (is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = $parametros;
            }

            $this->inicializarParametros();

            if (isset($_POST) && !empty($_POST) ) {
                $registroGravado = $this->salvar($this->view->parametros);
            }

        } catch (ErrorException $e) {

            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
        }

        if ( $registroGravado ) {
            $this->index();
        } else {

            require_once _MODULEDIR_ . "Cadastro/View/cad_subtipo_veiculo/cadastrar.php";
        }

    }

    public function editar() {

        try {
            $parametros = $this->tratarParametros();

            if (isset($parametros->vstoid) && intval($parametros->vstoid) > 0) {
                $parametros->vstoid = (int) $parametros->vstoid;

                $dados = $this->dao->pesquisarPorID( $parametros->vstoid );
                $dados->acao = $parametros->acao;

                $this->cadastrar($dados);
            } else {
                $this->index();
            }

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        }
    }

    private function salvar(stdClass $dados) {

        $this->validarCamposCadastro($dados);

        $isExisteDuplicidade = $this->dao->verificarDuplicidade($dados);

        if( $isExisteDuplicidade ) {
            throw new Exception('Já existe um subtipo cadastrado com essa descrição.');
        }

        $this->dao->begin();
        $gravacao = null;

        if ($dados->vstoid > 0) {
            $gravacao = $this->dao->atualizar($dados);

            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        } else {
            $gravacao = $this->dao->inserir($dados);

            if( $gravacao ) {
                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
            } else {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

        }

        $this->dao->commit();

        return $gravacao;
    }

    private function validarCamposCadastro(stdClass $dados) {

        $camposDestaques = array();

        if (!isset($dados->vstdescricao) || trim($dados->vstdescricao) == '') {
            $camposDestaques[] = array(
                'campo' => 'vstdescricao'
            );
        }

        if (!isset($dados->tipvoid) || trim($dados->tipvoid) == '') {
            $camposDestaques[] = array(
                'campo' => 'tipvoid'
            );
        }


        if (!empty($camposDestaques)) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    public function excluir() {

        try {

            $parametros = $this->tratarParametros();

            if (!isset($parametros->vstoid) || empty($parametros->vstoid) ) {
                echo 'ERRO';
                exit;
            }

            $this->dao->begin();

            $confirmacao = $this->dao->excluir( $parametros->vstoid );

            if ($confirmacao) {

                $this->dao->commit();

                echo 'OK';
                exit;

            }

        } catch (ErrorException $e) {
            $this->dao->rollback();
            echo 'ERRO';
            exit;

        } catch (Exception $e) {
            $this->dao->rollback();
            echo 'ERRO';
            exit;
        }
    }

}

