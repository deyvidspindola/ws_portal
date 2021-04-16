<?php

/**
 * Classe CadSubTipoVeiculoObrigacaoFinanceira.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   ANDRE LUIZ ZILZ <andre.zilz@sascar.com.br>
 *
 */
class CadSubTipoVeiculoObrigacaoFinanceira {

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
        $this->view->status          = false;
        $this->usuarioLogado         = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
        $this->urlPagina               = "cad_subtipo_veiculo_obrigacao_financeira.php";
    }

    public function index() {

        try {
            $this->view->parametros = $this->tratarParametros();
            $this->inicializarParametros();

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

        require_once _MODULEDIR_ . "Cadastro/View/cad_subtipo_veiculo_obrigacao_financeira/index.php";
    }

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

    private function inicializarParametros() {

       $this->view->listaTipo = array();
       $this->view->listaSubTipo = array();

       $this->view->parametros->acao    = isset($this->view->parametros->acao)    ? trim($this->view->parametros->acao) : 'index';
       $this->view->parametros->tipvoid = isset($this->view->parametros->tipvoid) ? $this->view->parametros->tipvoid    : 0;
       $this->view->parametros->vstoid  = isset($this->view->parametros->vstoid)  ? $this->view->parametros->vstoid     : 0;
       $this->view->parametros->obroid  = isset($this->view->parametros->obroid)  ? $this->view->parametros->obroid     : 0;
       $this->view->parametros->cadastro  = isset($this->view->parametros->cadastro)  ? $this->view->parametros->cadastro : 'N';
       $this->view->parametros->vstdescricao  = isset($this->view->parametros->vstdescricao)  ? $this->view->parametros->vstdescricao : '';
       $this->view->parametros->tipvdescricao = isset($this->view->parametros->tipvdescricao) ? $this->view->parametros->tipvdescricao : '';
       $this->view->parametros->lista_obrigacao = isset($this->view->parametros->lista_obrigacao) ? $this->view->parametros->lista_obrigacao : array();

       $this->view->listaObrigacaoFinanceira = $this->dao->recuperarObrigacaoFinanceira();

       if( $this->view->parametros->tipvoid > 0 ) {
            $this->view->listaSubTipo = $this->dao->recuperarListaSubTipo( $this->view->parametros->tipvoid );
            $this->view->listaSubTipoNovo = $this->dao->recuperarListaSubTipoNovo( $this->view->parametros->tipvoid );
       }

       $this->view->listaTipo = $this->dao->recuperarListaTipo();
       $this->view->listaTipoNovo = $this->dao->recuperarListaTipoNovo();

    }

    public function recuperarListaSubTipoAjax() {

        $parametros = $this->tratarParametros();
        $dados = array();

        if($parametros->cadastro == 'S') {
            $retorno = $this->dao->recuperarListaSubTipoNovo( $parametros->tipvoid );
        } else {
            $retorno = $this->dao->recuperarListaSubTipo( $parametros->tipvoid );
        }

        foreach ($retorno as $i => $value) {
            $dados[$i]['chave'] = $value->vstoid;
            $dados[$i]['valor'] = utf8_encode($value->vstdescricao);
        }
        echo json_encode($dados);exit();
        exit;

    }


    private function pesquisar(stdClass $filtros) {

        $ordenacao = array(
            ''        => 'Escolha',
            'tipvdescricao' => 'Tipo',
            'vstdescricao'  => 'subtipo'
        );

        $quantidade = array(10, 25, 50, 100);

        $resultadoPesquisa = $this->dao->pesquisar($filtros);

        if ( $resultadoPesquisa->total == 0 ) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';

        $paginacao = new PaginacaoComponente();
        $paginacao->setarCampos($ordenacao);
        $paginacao->setQuantidadesArray($quantidade);
        $this->view->ordenacao = $paginacao->gerarOrdenacao();
        $this->view->paginacao = $paginacao->gerarPaginacao( $resultadoPesquisa->total );
        $this->view->TotalRegistros = intval( $resultadoPesquisa->total );

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

            if (isset($_POST) && !empty($_POST)) {
                $registroGravado = $this->salvar($this->view->parametros);
            }

        } catch (ErrorException $e) {
            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
        }

        if ($registroGravado){
            unset($_POST);
            $this->index();
        } else {
            require_once _MODULEDIR_ . "Cadastro/View/cad_subtipo_veiculo_obrigacao_financeira/cadastrar.php";
        }
    }

    public function editar() {

        try {

            $parametros = $this->tratarParametros();

            if (isset($parametros->vstoid) && intval($parametros->vstoid) > 0) {
                $parametros->vstoid = (int) $parametros->vstoid;
                $dados = $this->dao->pesquisarPorID($parametros->vstoid);
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

        $this->dao->begin();
        $gravacao = FALSE;

        if ($dados->tipvoid > 0) {
            $gravacao = $this->dao->inserir($dados);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        } else {

            $dadosInseridos = $this->dao->pesquisarPorID( $dados->vstoid[0] );
            $dados->lista_obrigacao = $dadosInseridos->lista_obrigacao;
            $gravacao = $this->dao->atualizar($dados);

            if($gravacao) {
                $dados->obroid =  array_diff($dados->obroid, $dadosInseridos->lista_obrigacao);
                $gravacao = $this->dao->inserir($dados);
            }

            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        }

        $this->dao->commit();

        return $gravacao;
    }


    public function excluirRegistroAjax() {

       try {

            $parametros = $this->tratarParametros();

            if (!isset($parametros->vstoid) || empty($parametros->vstoid) ) {
                echo 'ERRO';
                exit;
            }

            $this->dao->begin();

            $confirmacao = $this->dao->excluirRegistro( $parametros->vstoid );

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